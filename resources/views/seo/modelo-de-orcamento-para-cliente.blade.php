@php
    $faq = [
        [
            'question' => 'Qual a diferença entre orçamento e proposta?',
            'answer' => 'O orçamento normalmente informa preço. A proposta organiza preço, escopo, prazo, condições e aceite, deixando a negociação mais profissional.',
        ],
        [
            'question' => 'Um orçamento precisa ter validade?',
            'answer' => 'Sim. Informar validade evita que um preço antigo seja usado fora do prazo planejado pelo prestador.',
        ],
        [
            'question' => 'Posso transformar um orçamento em proposta?',
            'answer' => 'Sim. Basta adicionar contexto, escopo, condições, prazo e forma de aceite ao orçamento.',
        ],
        [
            'question' => 'O propostei gera orçamento em PDF?',
            'answer' => 'O propostei cria propostas com itens e valores que podem funcionar como orçamento profissional, com link público e PDF.',
        ],
    ];
@endphp

@extends('layouts.seo')

@section('intro')
    Um orçamento para cliente não precisa ser apenas uma lista de preços. Com uma estrutura simples, ele pode virar uma proposta mais clara, profissional e fácil de aprovar.
@endsection

@section('primaryCta', 'Criar orçamento profissional grátis')

@section('content')
    <div class="seo-content">
        <p>
            Muitas vendas começam com a pergunta: “quanto fica?”. A resposta pode ser um valor rápido, mas quando o serviço envolve etapas, prazo ou entregas diferentes, um orçamento simples pode gerar confusão. Um modelo de orçamento para cliente ajuda a apresentar preço com contexto, sem deixar tudo espalhado em mensagens.
        </p>

        <h2>Quando um orçamento simples não basta</h2>
        <p>
            Se você vende um serviço pontual e padronizado, talvez um orçamento curto funcione. Mas quando o cliente precisa comparar opções, aprovar internamente ou entender o que está incluso, vale transformar o orçamento em algo mais completo. Isso reduz idas e vindas e aumenta a percepção de profissionalismo.
        </p>
        <p>
            Um orçamento profissional mostra o valor, mas também explica o motivo daquele valor. Ele deixa claro o que será entregue e por quanto tempo a condição vale.
        </p>

        <h2>Estrutura de um orçamento profissional</h2>
        <ul>
            <li>Identificação do cliente.</li>
            <li>Descrição curta do serviço solicitado.</li>
            <li>Lista de itens, quantidades e valores.</li>
            <li>Subtotal, desconto se houver e total final.</li>
            <li>Prazo de entrega ou execução.</li>
            <li>Validade do orçamento.</li>
            <li>Condições de pagamento e próximos passos.</li>
        </ul>
        <p>
            Essa estrutura ainda é simples, mas já evita ambiguidades. O cliente passa a entender que o valor está ligado a um conjunto de entregas e condições, não a um número solto.
        </p>

        @include('seo._cta', [
            'title' => 'Crie um orçamento com cara de proposta',
            'text' => 'No propostei, você organiza itens, valores, prazo e gera link ou PDF para o cliente.',
            'label' => 'Criar orçamento profissional grátis',
        ])

        <h2>Como evoluir de orçamento para proposta</h2>
        <p>
            Para transformar um orçamento em proposta comercial, adicione uma introdução, explique o objetivo do serviço, destaque o escopo e informe como o cliente pode aprovar. Você não precisa escrever páginas e páginas. O importante é que o cliente consiga responder: o que será feito, quanto custa, até quando vale e qual é o próximo passo?
        </p>
        <p>
            Se o seu serviço é recorrente, criativo ou consultivo, considere usar diretamente uma <a href="{{ url('/modelo-de-proposta-comercial') }}">proposta comercial</a>. Se quer praticidade, o <a href="{{ url('/gerador-de-proposta-comercial') }}">gerador de proposta comercial online</a> do propostei ajuda a montar tudo mais rápido.
        </p>

        <h2>Como apresentar preço sem parecer improvisado</h2>
        <p>
            A forma de apresentar o preço influencia a percepção do cliente. Um total enviado sozinho pode parecer caro porque não mostra esforço, etapas nem entregas. Quando o orçamento separa itens, quantidades e condições, o cliente entende melhor o que está pagando. Isso também facilita negociar: você pode ajustar escopo em vez de apenas reduzir preço.
        </p>
        <p>
            Para serviços, vale incluir uma frase curta explicando o objetivo do orçamento. Algo como “Este orçamento contempla a criação de uma landing page institucional com estrutura responsiva, publicação e ajustes finais”. Essa contextualização deixa o documento mais próximo de uma proposta profissional.
        </p>

        <h2>Erros comuns em orçamento para cliente</h2>
        <p>
            Não informar validade, não separar itens, deixar o total sem contexto e esquecer condições de pagamento são erros frequentes. Também é comum enviar orçamento sem identificação do cliente, o que dificulta encontrar depois. Uma ferramenta simples ajuda a manter histórico e padrão visual.
        </p>

        <h2>Perguntas frequentes</h2>
        @foreach ($faq as $item)
            <h3>{{ $item['question'] }}</h3>
            <p>{{ $item['answer'] }}</p>
        @endforeach
    </div>

    @include('seo._cta', [
        'title' => 'Envie orçamentos mais organizados',
        'text' => 'Crie uma proposta com itens, total, prazo e link público para aprovação.',
        'label' => 'Começar grátis',
    ])
@endsection
