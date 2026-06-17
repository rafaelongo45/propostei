<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PremiumInterestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\PublicProposalController;
use App\Http\Controllers\SeoPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);
Route::get('/modelo-de-proposta-comercial', [SeoPageController::class, 'modeloDePropostaComercial'])->name('seo.modelo-de-proposta-comercial');
Route::get('/gerador-de-proposta-comercial', [SeoPageController::class, 'geradorDePropostaComercial'])->name('seo.gerador-de-proposta-comercial');
Route::get('/proposta-comercial-prestacao-de-servico', [SeoPageController::class, 'propostaComercialPrestacaoDeServico'])->name('seo.proposta-comercial-prestacao-de-servico');
Route::get('/modelo-de-orcamento-para-cliente', [SeoPageController::class, 'modeloDeOrcamentoParaCliente'])->name('seo.modelo-de-orcamento-para-cliente');
Route::get('/proposta-comercial-freelancer', [SeoPageController::class, 'propostaComercialFreelancer'])->name('seo.proposta-comercial-freelancer');
Route::get('/sobre', [SeoPageController::class, 'sobre'])->name('seo.sobre');
// Route::get('/contato', [ContactController::class, 'create'])->name('contact.create');
// Route::post('/contato', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

Route::get('/p/{token}', [PublicProposalController::class, 'show'])->name('public.proposals.show');
Route::get('/p/{token}/pdf', [PublicProposalController::class, 'downloadPdf'])->name('public.proposals.pdf');
Route::post('/p/{token}/accept', [PublicProposalController::class, 'accept'])->name('public.proposals.accept');
Route::post('/p/{token}/reject', [PublicProposalController::class, 'reject'])->name('public.proposals.reject');
Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProposalController::class, 'index'])->name('dashboard');
    Route::post('/premium-interest', [PremiumInterestController::class, 'store'])->name('premium-interest.store');
    Route::resource('clients', ClientController::class);
    Route::get('/proposals/{proposal}/pdf', [ProposalController::class, 'downloadPdf'])->name('proposals.pdf');
    Route::post('/proposals/{proposal}/send', [ProposalController::class, 'send'])->name('proposals.send');
    Route::post('/proposals/{proposal}/link-copied', [ProposalController::class, 'linkCopied'])->name('proposals.link-copied');
    Route::resource('proposals', ProposalController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/premium-interests', [PremiumInterestController::class, 'adminIndex'])->name('premium-interests.index');
});

require __DIR__.'/auth.php';
