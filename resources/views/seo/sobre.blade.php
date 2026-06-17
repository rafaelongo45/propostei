@php
    $faq = [
        [
            'question' => 'O propostei é para quem?',
            'answer' => 'O propostei foi criado para freelancers, autônomos, MEIs e pequenos prestadores que precisam enviar propostas comerciais profissionais sem montar tudo manualmente.',
        ],
        [
            'question' => 'O propostei substitui contrato?',
            'answer' => 'Não. O propostei organiza a proposta comercial, o aceite e o histórico, mas contratos formais podem continuar sendo necessários em projetos maiores.',
        ],
        [
            'question' => 'O cliente precisa criar conta?',
            'answer' => 'Não. O cliente acessa a proposta por um link público, visualiza os itens e pode aceitar ou recusar sem cadastro.',
        ],
        [
            'question' => 'O propostei processa pagamentos?',
            'answer' => 'Ainda não. O usuário pode informar dados de pagamento para aparecerem na proposta, mas o pagamento acontece fora da plataforma.',
        ],
    ];
@endphp

@extends('layouts.seo')

@section('intro')
    O propostei é uma plataforma brasileira para criar propostas comerciais profissionais por link ou PDF, pensada para quem vende serviços e precisa sair do improviso.
@endsection

@section('primaryCta', 'Começar grátis')

@section('content')
    <div class="seo-content">
        <p>
            O propostei nasceu para resolver um problema comum de freelancers, autônomos, MEIs e pequenos prestadores: propostas comerciais espalhadas em mensagens, arquivos antigos reaproveitados, orçamentos sem histórico e aprovações que ficam perdidas no WhatsApp.
        </p>
        <p>
            A ideia é simples. Você cadastra o cliente, cria uma proposta com serviços, valores, prazo e validade, envia um link público ou baixa um PDF, e acompanha se o cliente aceitou ou recusou. Sem transformar o processo em um CRM complexo.
        </p>

        <h2>Para quem o propostei foi criado</h2>
        <p>
            O produto foi pensado para pessoas e pequenos negócios que vendem serviço: designers, desenvolvedores, social medias, consultores, gestores de tráfego, redatores, fotógrafos, editores de vídeo, técnicos, prestadores locais e pequenas agências.
        </p>
        <p>
            Em comum, todos precisam apresentar escopo, prazo e valores de forma clara. Uma proposta bem organizada ajuda o cliente a decidir e ajuda o prestador a manter registro do que foi combinado.
        </p>

        <h2>O que o propostei faz hoje</h2>
        <ul>
            <li>Cadastro de clientes.</li>
            <li>Criação de propostas comerciais com itens, quantidades e valores.</li>
            <li>Link público para o cliente visualizar a proposta.</li>
            <li>PDF da proposta para envio ou arquivo.</li>
            <li>Aceite e recusa com registro de responsável, data e observações.</li>
            <li>Status da proposta no painel.</li>
            <li>Dados de pagamento opcionais exibidos na proposta e no PDF.</li>
        </ul>

        @include('seo._cta', [
            'title' => 'Crie sua primeira proposta no propostei',
            'text' => 'Monte uma proposta profissional, envie por link e acompanhe o aceite do cliente.',
            'label' => 'Começar grátis',
        ])

        <h2>O que o propostei não tenta ser</h2>
        <p>
            O propostei não é marketplace, não é um CRM completo, não substitui contrato jurídico e ainda não processa pagamentos dentro da plataforma. A escolha é intencional: o objetivo do MVP é ser simples, útil e rápido para o prestador que precisa enviar propostas melhores agora.
        </p>
        <p>
            Com o tempo, novos recursos podem entrar. Mas a base continua a mesma: ajudar pequenos prestadores a vender serviços com mais clareza, sem burocracia desnecessária.
        </p>

        <h2>Por onde começar</h2>
        <p>
            Se você quer entender a estrutura de uma proposta, veja o guia de <a href="{{ url('/modelo-de-proposta-comercial') }}">modelo de proposta comercial</a>. Se prefere criar direto, acesse o <a href="{{ url('/gerador-de-proposta-comercial') }}">gerador de proposta comercial online</a>. Para quem vende serviços, também há um guia específico de <a href="{{ url('/proposta-comercial-prestacao-de-servico') }}">proposta comercial para prestação de serviço</a>.
        </p>

        <h2>Perguntas frequentes</h2>
        @foreach ($faq as $item)
            <h3>{{ $item['question'] }}</h3>
            <p>{{ $item['answer'] }}</p>
        @endforeach
    </div>

    @include('seo._cta', [
        'title' => 'Organize suas propostas sem complicar',
        'text' => 'Comece grátis e envie uma proposta profissional para o próximo cliente.',
        'label' => 'Criar conta grátis',
    ])
@endsection
