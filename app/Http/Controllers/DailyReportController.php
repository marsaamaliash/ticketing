<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyReportRequest;
use App\Models\DailyReport;
use App\Models\Ticket;
use App\Models\TicketActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = DailyReport::with(['technician', 'ticket'])
            ->where('technician_id', $request->user()->id)
            ->latest('report_date');

        if ($request->filled('from')) {
            $query->whereDate('report_date', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('report_date', '<=', $request->date('to'));
        }

        $reports = $query->paginate(20);

        $today = DailyReport::where('technician_id', $request->user()->id)
            ->whereDate('report_date', today())
            ->get();

        return view('daily-reports.index', compact('reports', 'today'));
    }

    public function create(Request $request): View
    {
        $myTickets = Ticket::where('assigned_technician_id', $request->user()->id)
            ->whereNotIn('status', ['closed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('daily-reports.create', compact('myTickets'));
    }

    public function store(StoreDailyReportRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['technician_id'] = $request->user()->id;

        $report = DailyReport::create($data);

        if ($data['ticket_id']) {
            $ticket = Ticket::find($data['ticket_id']);
            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'user_id' => $request->user()->id,
                'type' => 'daily_report',
                'description' => "Daily report: {$report->activity}",
                'meta' => ['daily_report_id' => $report->id],
            ]);
        }

        return redirect()
            ->route('daily-reports.index')
            ->with('success', 'Daily report terkirim.');
    }

    public function show(DailyReport $dailyReport): View
    {
        $dailyReport->load(['technician', 'ticket']);

        return view('daily-reports.show', compact('dailyReport'));
    }

    public function edit(DailyReport $dailyReport): View
    {
        abort_unless($dailyReport->technician_id === auth()->id() || auth()->user()->isAdmin(), 403);
        $myTickets = Ticket::where('assigned_technician_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('daily-reports.edit', compact('dailyReport', 'myTickets'));
    }

    public function update(StoreDailyReportRequest $request, DailyReport $dailyReport): RedirectResponse
    {
        abort_unless($dailyReport->technician_id === auth()->id() || auth()->user()->isAdmin(), 403);
        $dailyReport->update($request->validated());

        return redirect()
            ->route('daily-reports.show', $dailyReport)
            ->with('success', 'Daily report diperbarui.');
    }

    public function destroy(DailyReport $dailyReport): RedirectResponse
    {
        abort_unless($dailyReport->technician_id === auth()->id() || auth()->user()->isAdmin(), 403);
        $dailyReport->delete();

        return redirect()
            ->route('daily-reports.index')
            ->with('success', 'Daily report dihapus.');
    }
}
