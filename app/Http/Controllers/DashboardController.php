<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'total' => 0,
            'open' => 0,
            'forwarded' => 0,
            'assigned' => 0,
            'in_progress' => 0,
            'finished' => 0,
            'verified' => 0,
            'closed' => 0,
            'reopened' => 0,
            'overdue' => 0,
        ];

        $query = Ticket::query()->forUser($user);
        $counts = (clone $query)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        foreach ($counts as $status => $count) {
            if (isset($stats[$status])) {
                $stats[$status] = $count;
            }
            $stats['total'] += $count;
        }

        if ($user->hasRole(['admin', 'manager'])) {
            $stats['overdue'] = Ticket::whereIn('status', ['assigned', 'in_progress', 'reopened'])
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '<', now())
                ->count();
        } elseif ($user->hasRole('teknisi')) {
            $stats['overdue'] = Ticket::where('assigned_technician_id', $user->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '<', now())
                ->count();
        }

        $myTasksQuery = Ticket::with(['customer', 'category'])->forUser($user);
        if ($user->hasRole('cs')) {
            $myTasksQuery->whereIn('status', ['open', 'finished']);
        } elseif ($user->hasRole('teknisi')) {
            $myTasksQuery->whereIn('status', ['assigned', 'in_progress']);
        } elseif ($user->hasRole('manager')) {
            $myTasksQuery->whereIn('status', ['forwarded', 'reopened']);
        } else {
            $myTasksQuery->whereIn('status', ['open', 'finished', 'forwarded', 'assigned', 'in_progress']);
        }

        $myTasks = $myTasksQuery->latest()->limit(10)->get();

        $todaySchedule = collect();
        if ($user->hasRole(['admin', 'manager', 'teknisi'])) {
            $todaySchedule = Ticket::with(['customer', 'category', 'technician'])
                ->whereNotNull('scheduled_at')
                ->whereDate('scheduled_at', today())
                ->forUser($user)
                ->orderBy('scheduled_at')
                ->limit(20)
                ->get();
        }

        $latestActivities = TicketActivity::with(['ticket', 'user'])
            ->whereHas('ticket', function ($q) use ($user) {
                $q->forUser($user);
            })
            ->latest()
            ->limit(10)
            ->get();

        $technicians = collect();
        if ($user->hasRole(['admin', 'manager', 'cs'])) {
            $technicians = User::role('teknisi')
                ->withCount(['assignedTickets as open_count' => function ($q) {
                    $q->whereIn('status', ['assigned', 'in_progress']);
                }])
                ->orderBy('name')
                ->get();
        }

        return view('dashboard', compact(
            'stats', 'myTasks', 'todaySchedule', 'latestActivities', 'technicians',
        ));
    }
}
