<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/dashboard/quick-note', [DashboardController::class, 'quickNote'])->name('dashboard.quick-note');

    Route::get('/write-journal', [JournalEntryController::class, 'create'])->name('write-journal');
    Route::post('/write-journal', [JournalEntryController::class, 'store'])->name('journal.store');

    Route::get('/calendar', function () {
        return view('journal.calendar');
    })->name('calendar');

    Route::get('/journal-history', function () {
        return view('journal.history');
    })->name('journal-history');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/export', [ProfileController::class, 'export'])->name('profile.export');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
