<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class SeoPageController extends Controller
{
    private const PAGES = [
        'modelo-de-proposta-comercial' => [
            'title' => 'Modelo de Proposta Comercial: guia simples + exemplo | propostei',
            'description' => 'Veja como montar uma proposta comercial profissional e crie sua proposta online por link ou PDF com o propostei. Comece grátis.',
            'h1' => 'Modelo de proposta comercial: como criar uma proposta profissional',
        ],
        'gerador-de-proposta-comercial' => [
            'title' => 'Gerador de Proposta Comercial Online | propostei',
            'description' => 'Crie propostas comerciais profissionais online, envie por link ou gere PDF. Ideal para freelancers, MEIs e prestadores de serviço.',
            'h1' => 'Gerador de proposta comercial online',
        ],
        'proposta-comercial-prestacao-de-servico' => [
            'title' => 'Proposta Comercial para Prestação de Serviço: como fazer | propostei',
            'description' => 'Aprenda como montar uma proposta comercial para prestação de serviço com escopo, prazo, valores e condições claras.',
            'h1' => 'Proposta comercial para prestação de serviço: como fazer',
        ],
        'modelo-de-orcamento-para-cliente' => [
            'title' => 'Modelo de Orçamento para Cliente: estrutura simples | propostei',
            'description' => 'Veja como criar um orçamento profissional para cliente e transforme orçamentos simples em propostas mais organizadas.',
            'h1' => 'Modelo de orçamento para cliente: como montar um orçamento profissional',
        ],
        'proposta-comercial-freelancer' => [
            'title' => 'Proposta Comercial para Freelancer: modelo e dicas | propostei',
            'description' => 'Veja como freelancers podem enviar propostas comerciais mais profissionais, com escopo, prazo, valores e aceite online.',
            'h1' => 'Proposta comercial para freelancer: modelo e dicas',
        ],
        'sobre' => [
            'title' => 'Sobre o propostei | propostas comerciais online',
            'description' => 'Conheça o propostei, uma plataforma brasileira para freelancers, MEIs e pequenos prestadores criarem propostas comerciais por link ou PDF.',
            'h1' => 'Sobre o propostei',
        ],
    ];

    public function modeloDePropostaComercial(): View
    {
        return $this->render('modelo-de-proposta-comercial');
    }

    public function geradorDePropostaComercial(): View
    {
        return $this->render('gerador-de-proposta-comercial');
    }

    public function propostaComercialPrestacaoDeServico(): View
    {
        return $this->render('proposta-comercial-prestacao-de-servico');
    }

    public function modeloDeOrcamentoParaCliente(): View
    {
        return $this->render('modelo-de-orcamento-para-cliente');
    }

    public function propostaComercialFreelancer(): View
    {
        return $this->render('proposta-comercial-freelancer');
    }

    public function sobre(): View
    {
        return $this->render('sobre');
    }

    private function render(string $slug): View
    {
        $page = self::PAGES[$slug];

        return view("seo.{$slug}", [
            ...$page,
            'slug' => $slug,
            'canonical' => "https://propostei.com/{$slug}",
        ]);
    }
}
