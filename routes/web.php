<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\PublicProposalController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/p/{token}', [PublicProposalController::class, 'show'])->name('public.proposals.show');
Route::get('/p/{token}/pdf', [PublicProposalController::class, 'downloadPdf'])->name('public.proposals.pdf');
Route::post('/p/{token}/accept', [PublicProposalController::class, 'accept'])->name('public.proposals.accept');
Route::post('/p/{token}/reject', [PublicProposalController::class, 'reject'])->name('public.proposals.reject');
Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProposalController::class, 'index'])->name('dashboard');
    Route::resource('clients', ClientController::class);
    Route::get('/proposals/{proposal}/pdf', [ProposalController::class, 'downloadPdf'])->name('proposals.pdf');
    Route::post('/proposals/{proposal}/send', [ProposalController::class, 'send'])->name('proposals.send');
    Route::post('/proposals/{proposal}/link-copied', [ProposalController::class, 'linkCopied'])->name('proposals.link-copied');
    Route::resource('proposals', ProposalController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
