<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController; 

// Welcome Page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'     => Route::has('login'),
        'canRegister'  => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

// Dashboard
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Invoices
Route::middleware(['auth'])->group(function () {
    Route::get('/invoices',           [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create',    [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices',          [InvoiceController::class, 'store'])->name('invoices.store');
    Route::put('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
});
Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])
    ->name('invoices.download')
    ->middleware('auth');




require __DIR__.'/auth.php';
