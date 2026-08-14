<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketDiagnosisController;
use App\Http\Controllers\TicketPdfController;
use App\Http\Controllers\TicketWorkflowController;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('customers', CustomerController::class);

    Route::resource('tickets', TicketController::class);
    Route::get('tickets/{ticket}/pdf/surat', [TicketPdfController::class, 'surat'])->name('tickets.pdf.surat');
    Route::post('tickets/labels/preview', [TicketPdfController::class, 'labelBatch'])->name('tickets.labels.preview');

    Route::post('tickets/{ticket}/forward', [TicketWorkflowController::class, 'forward'])->name('tickets.forward');
    Route::post('tickets/{ticket}/assign', [TicketWorkflowController::class, 'assign'])->name('tickets.assign');
    Route::post('tickets/{ticket}/reschedule', [TicketWorkflowController::class, 'reschedule'])->name('tickets.reschedule');
    Route::post('tickets/{ticket}/start', [TicketWorkflowController::class, 'startProgress'])->name('tickets.start');
    Route::post('tickets/{ticket}/finish', [TicketWorkflowController::class, 'markFinished'])->name('tickets.finish');
    Route::post('tickets/{ticket}/verify', [TicketWorkflowController::class, 'verify'])->name('tickets.verify');
    Route::post('tickets/{ticket}/rate', [TicketWorkflowController::class, 'rate'])->name('tickets.rate');
    Route::post('tickets/{ticket}/reopen', [TicketWorkflowController::class, 'reopen'])->name('tickets.reopen');
    Route::post('tickets/{ticket}/cancel', [TicketWorkflowController::class, 'cancel'])->name('tickets.cancel');
    Route::post('tickets/{ticket}/comment', [TicketWorkflowController::class, 'comment'])->name('tickets.comment');

    Route::post('tickets/{ticket}/attachments', [TicketAttachmentController::class, 'store'])->name('tickets.attachments.store');
    Route::get('tickets/{ticket}/attachments/{attachment}', [TicketAttachmentController::class, 'download'])->name('tickets.attachments.download');
    Route::delete('tickets/{ticket}/attachments/{attachment}', [TicketAttachmentController::class, 'destroy'])->name('tickets.attachments.destroy');

    Route::post('tickets/{ticket}/diagnosis', [TicketDiagnosisController::class, 'store'])->name('tickets.diagnosis.store');

    Route::resource('daily-reports', DailyReportController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    Route::resource('categories', CategoryController::class)->except(['show']);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/api/customers/search', function (Request $request) {
        $search = $request->string('q')->toString();
        if (strlen($search) < 2) {
            return response()->json([]);
        }
        $items = Customer::query()
            ->where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->orWhere('customer_code', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'customer_code', 'name', 'phone', 'address']);

        return response()->json($items);
    })->name('api.customers.search');

    Route::get('/api/technicians', function () {
        return User::role('teknisi')->orderBy('name')->get(['id', 'name']);
    })->name('api.technicians');

    Route::get('/notifications', function () {
        $user = auth()->user();
        $notifications = $user->notifications()->paginate(20);
        $user->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    })->name('notifications.index');

    Route::post('/notifications/{id}/read', function ($id) {
        $notif = auth()->user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        return back();
    })->name('notifications.read');
});

require __DIR__.'/auth.php';
