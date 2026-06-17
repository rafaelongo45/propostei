@php
    $faq = [
        [
            'question' => 'Como fazer uma proposta comercial para prestação de serviço?',
            'answer' => 'Defina o escopo, liste as entregas, informe valores, prazo, validade, condições e deixe claro como o cliente pode aceitar.',
        ],
        [
            'question' => 'Preciso detalhar todas as etapas do serviço?',
            'answer' => 'Você não precisa escrever um contrato completo, mas deve explicar as etapas principais para evitar dúvida sobre o que está incluso.',
        ],
        [
            'question' => 'A proposta substitui contrato?',
            'answer' => 'Não necessariamente. Ela ajuda na negociação e no aceite comercial, mas serviços maiores podem exigir contrato separado.',
        ],
        [
            'question' => 'Posso colocar dados de pagamento na proposta?',
            'answer' => 'Sim. No propostei, o usuário pode cadastrar Pix, link e instruções para aparecerem na proposta pública e no PDF.',
        ],
    ];
@endphp

@extends('layouts.seo')

@section('intro')
    Uma proposta comercial para prestação de serviço precisa deixar escopo, prazo e valores fáceis de entender. Isso evita ruído na negociação e ajuda o cliente a aprovar com mais segurança.
@endsection

@section('primaryCta', 'Criar proposta de serviço grátis')

@section('content')
    <div class="seo-content">
        <p>
            Vender serviço é diferente de vender um produto pronto. Muitas vezes o cliente não compra algo padronizado: ele contrata conhecimento, tempo, execução e acompanhamento. Por isso, uma proposta comercial para prestação de serviço deve explicar exatamente o que será feito, como será entregue e quais condições estão valendo.
        </p>

        <h2>Comece pelo escopo</h2>
        <p>
            O escopo é o coração da proposta. Ele define o que está incluso e, tão importante quanto, o que não está. Para um serviço de marketing, por exemplo, o escopo pode incluir planejamento mensal, criação de posts, relatórios e reuniões. Para um serviço técnico, pode incluir diagnóstico, execução, testes e entrega final.
        </p>
        <p>
            Evite frases genéricas como “gerenciamento completo” ou “suporte total” sem explicar limites. Quanto mais claro o escopo, menor a chance de retrabalho ou expectativa desalinhada.
        </p>

        <h2>Inclua prazo, valores e condições</h2>
        <p>
            Depois do escopo, apresente os itens com valores. Você pode cobrar por pacote, por etapa, por hora ou por serviço fechado. O formato depende do seu modelo de negócio, mas a proposta deve mostrar subtotal, desconto se houver e total final.
        </p>
        <ul>
            <li>Prazo estimado de entrega ou início.</li>
            <li>Validade da proposta e dos valores.</li>
            <li>Forma de pagamento e dados necessários.</li>
            <li>Condições para início do serviço.</li>
            <li>Responsabilidades do cliente, se existirem.</li>
        </ul>

        @include('seo._cta', [
            'title' => 'Monte uma proposta de serviço com clareza',
            'text' => 'Use o propostei para listar escopo, prazo, valores e enviar um link de aceite ao cliente.',
            'label' => 'Criar proposta de serviço grátis',
        ])

        <h2>Como facilitar o aceite</h2>
        <p>
            Uma proposta boa não termina no preço. Ela precisa indicar o próximo passo. O cliente deve saber se precisa responder, assinar, pagar uma entrada ou aceitar por link. No propostei, a proposta pública tem botão de aceite e recusa, com registro de responsável e data.
        </p>
        <p>
            Esse registro não substitui todos os cuidados jurídicos de um contrato, mas traz organização para o fluxo comercial. Para pequenos serviços, muitas vezes já resolve o problema de saber quando o cliente aprovou.
        </p>

        <h2>Exemplo prático para serviços</h2>
        <p>
            Uma proposta para consultoria, por exemplo, pode separar diagnóstico, reunião de alinhamento, plano de ação e acompanhamento. Já uma proposta para manutenção pode separar visita técnica, mão de obra, materiais e prazo estimado. Essa divisão ajuda o cliente a perceber valor em cada parte do serviço, em vez de enxergar apenas um total final.
        </p>
        <p>
            Sempre que possível, escreva como se o cliente fosse ler a proposta sem você ao lado para explicar. Se uma informação depende de contexto verbal, provavelmente ela também merece aparecer no documento.
        </p>

        <h2>Erros comuns em prestação de serviço</h2>
        <p>
            Os erros mais frequentes são não detalhar entregas, não definir prazo, deixar a validade aberta e misturar conversa comercial com informações internas. Também é comum esquecer condições de pagamento ou prometer algo amplo demais. Uma proposta deve ser simples, mas não vaga.
        </p>
        <p>
            Se você ainda está comparando formatos, veja também o <a href="{{ url('/modelo-de-proposta-comercial') }}">modelo de proposta comercial</a> e o <a href="{{ url('/modelo-de-orcamento-para-cliente') }}">modelo de orçamento para cliente</a>.
        </p>

        <h2>Perguntas frequentes</h2>
        @foreach ($faq as $item)
            <h3>{{ $item['question'] }}</h3>
            <p>{{ $item['answer'] }}</p>
        @endforeach
    </div>

    @include('seo._cta', [
        'title' => 'Crie uma proposta de serviço profissional',
        'text' => 'Organize escopo, valores e aceite em um link claro para o cliente.',
        'label' => 'Começar grátis',
    ])
@endsection
