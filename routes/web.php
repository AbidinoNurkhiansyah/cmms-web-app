<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// ----- Auth -----
Volt::route('/login', 'auth.login')->name('login')->middleware('guest');
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// ----- Protected routes -----
Route::middleware('auth')->group(function () {

    Route::redirect('/', '/dashboard');

    // Dashboard
    Volt::route('/dashboard', 'dashboard.index')->name('dashboard');

    // Master Data
    Volt::route('/assets', 'asset.asset-index')->name('assets.index');
    Volt::route('/assets/{id}', 'asset.asset-show')->name('assets.show');
    Volt::route('/spare-parts', 'spare-part.spare-part-index')->name('spare-parts.index');
    Volt::route('/spare-parts/repair', 'spare-part.repair.index')->name('spare-parts.repair.index');
    Volt::route('/spare-parts/repair/{id}', 'spare-part.repair.detail')->name('spare-parts.repair.show');
    Volt::route('/spare-parts/stock-taking', 'spare-part.stock-taking.index')->name('spare-parts.stock-taking.index');
    Volt::route('/spare-parts/stock-taking/{date}/{status?}', 'spare-part.stock-taking.detail')->name('spare-parts.stock-taking.detail');

    // User Management
    Volt::route('/users', 'users.index')->name('users.index');
    Volt::route('/profile', 'users.profile')->name('profile');

    // Maintenance - Cardty
    Volt::route('/maintenance/cardty', 'maintenance.cardty.index')->name('maintenance.cardty');
    Volt::route('/maintenance/cardty/create', 'maintenance.cardty.create')->name('maintenance.cardty.create');
    Volt::route('/maintenance/cardty/{id}/edit', 'maintenance.cardty.edit')->name('maintenance.cardty.edit');
    Volt::route('/maintenance/cardty/{id}', 'maintenance.cardty.show')->name('maintenance.cardty.show');

    // Checksheet
    Volt::route('/checksheet', 'checksheet.index')->name('checksheet.index');

    // Deep Cleaning / TPM
    Volt::route('/deep-cleaning', 'deep-cleaning.record.index')->name('deep-cleaning.index');
    Volt::route('/deep-cleaning/schedule', 'deep-cleaning.schedule.index')->name('deep-cleaning.schedule');
    Volt::route('/tpm/checksheet', 'deep-cleaning.checksheet.index')->name('tpm.checksheet.index');
    Volt::route('/deep-cleaning/{id}', 'deep-cleaning.record.show')->name('deep-cleaning.show')->where('id', '[0-9]+');

    // Overhaul Report
    Volt::route('/overhaul/report', 'overhaul.report.index')->name('overhaul.report.index');
    Volt::route('/overhaul/report/{id}', 'overhaul.report.show')->name('overhaul.report.show')->where('id', '[0-9]+');

    // Overhaul History Machine
    Volt::route('/overhaul/history-machine', 'overhaul.history-machine.index')->name('overhaul.history-machine.index');
    Volt::route('/overhaul/history-machine/qr-generator', 'overhaul.history-machine.qr-generator')->name('overhaul.history-machine.qr-generator');
    Volt::route('/overhaul/history-machine/qr-print', 'overhaul.history-machine.qr-print')->name('overhaul.history-machine.qr-print');
    Volt::route('/overhaul/history-machine/{id}', 'overhaul.history-machine.show')->name('overhaul.history-machine.show')->where('id', '[0-9]+');

    // Work Order
    Volt::route('/work-orders', 'work-order.index')->name('work-orders.index');

    // Andon
    Volt::route('/andon', 'andon.index')->name('andon.index');

    // One Hour Over
    Volt::route('/maintenance/one-hour-over', 'maintenance.one-hour-over.index')->name('maintenance.one-hour-over.index');

    // Administration (KYT / Safety)
    Volt::route('/administration/kyt', 'administration.kyt-index')->name('administration.kyt');

    // Problem Analysis
    Volt::route('/problem-analysis', 'problem-analysis.index')->name('problem-analysis.index');

    // Meeting
    Volt::route('/meeting', 'meeting.index')->name('meeting.index');

});
