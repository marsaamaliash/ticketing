<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DailyReport;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(
            $request->user()->hasRole(['admin', 'manager'])
                || $request->user()->can('view-reports'),
            403,
        );

        $from = $request->date('from') ?: now()->subDays(30)->toDateString();
        $to = $request->date('to') ?: now()->toDateString();

        $base = Ticket::query()
            ->whereBetween('created_at', [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()]);

        $byStatus = (clone $base)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byPriority = (clone $base)
            ->selectRaw('priority, COUNT(*) as total')
            ->groupBy('priority')
            ->pluck('total', 'priority');

        $byCategory = (clone $base)
            ->selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $byTechnician = (clone $base)
            ->selectRaw('assigned_technician_id, COUNT(*) as total')
            ->whereNotNull('assigned_technician_id')
            ->groupBy('assigned_technician_id')
            ->pluck('total', 'assigned_technician_id');

        $dailyTrend = (clone $base)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $avgRating = (clone $base)->whereNotNull('rating')->avg('rating');

        $slaOpen = (clone $base)
            ->whereIn('status', ['open', 'forwarded', 'assigned', 'in_progress', 'reopened'])
            ->count();

        $slaFinished = (clone $base)
            ->whereIn('status', ['finished', 'verified', 'closed'])
            ->count();

        $overdue = (clone $base)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<', now())
            ->count();

        $technicians = User::role('teknisi')->orderBy('name')->get();
        $technicianStats = $technicians->map(function ($t) use ($base) {
            $total = (clone $base)->where('assigned_technician_id', $t->id)->count();
            $finished = (clone $base)->where('assigned_technician_id', $t->id)
                ->whereIn('status', ['finished', 'verified', 'closed'])->count();
            $open = (clone $base)->where('assigned_technician_id', $t->id)
                ->whereIn('status', ['assigned', 'in_progress'])->count();

            return compact('t', 'total', 'finished', 'open');
        });

        $categories = Category::orderBy('name')->get()->keyBy('id');
        $reportsCount = DailyReport::whereBetween('report_date', [Carbon::parse($from), Carbon::parse($to)])->count();

        return view('reports.index', compact(
            'from', 'to',
            'byStatus', 'byPriority', 'byCategory', 'byTechnician',
            'dailyTrend', 'avgRating',
            'slaOpen', 'slaFinished', 'overdue',
            'technicianStats', 'categories',
            'reportsCount',
        ));
    }
}
