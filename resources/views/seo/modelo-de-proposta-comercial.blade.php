@php
    $faq = [
        [
            'question' => 'O que é uma proposta comercial?',
            'answer' => 'É um documento enviado a um possível cliente com escopo, valores, prazos, condições e próximos passos para contratar um serviço ou projeto.',
        ],
        [
            'question' => 'Uma proposta comercial precisa ser longa?',
            'answer' => 'Não. Para serviços simples, uma proposta clara com objetivo, itens, valor, prazo, validade e forma de aceite já costuma ser suficiente.',
        ],
        [
            'question' => 'Posso enviar proposta comercial por link?',
            'answer' => 'Sim. No propostei, você cria a proposta, compartilha um link público e também pode gerar PDF para enviar ao cliente.',
        ],
        [
            'question' => 'Qual a diferença entre proposta e orçamento?',
            'answer' => 'O orçamento costuma focar em preço. A proposta comercial inclui também escopo, benefícios, condições, prazo e aceite.',
        ],
    ];
@endphp

@extends('layouts.seo')

@section('intro')
    Uma boa proposta comercial transforma uma conversa solta em uma decisão mais clara para o cliente. Ela organiza o que será entregue, quanto custa, qual o prazo e como o cliente pode aceitar.
@endsection

@section('primaryCta', 'Criar minha proposta grátis')

@section('content')
    <div class="seo-content">
        <p>
            Quem vende serviço sabe que o cliente raramente decide só pelo preço. Ele precisa entender o que está incluso, quais etapas serão feitas, quando o trabalho será entregue e quais condições valem para aquela negociação. Um modelo de proposta comercial ajuda justamente nisso: ele evita mensagens quebradas no WhatsApp, PDFs improvisados e dúvidas que atrasam o fechamento.
        </p>

        <h2>O que uma proposta comercial deve conter</h2>
        <p>
            O modelo não precisa ser complexo. Para freelancers, MEIs e pequenos prestadores, a estrutura mais eficiente costuma ser direta. Comece com um título claro, uma breve introdução sobre o objetivo do projeto, os dados do cliente e a lista de serviços ou entregáveis. Em seguida, detalhe quantidade, valor unitário, subtotal, total, prazo estimado, validade da proposta e observações importantes.
        </p>
        <ul>
            <li>Nome do cliente e, se houver, empresa.</li>
            <li>Título da proposta e contexto do serviço.</li>
            <li>Itens ou etapas com valores separados.</li>
            <li>Prazo de entrega ou início previsto.</li>
            <li>Validade da proposta comercial.</li>
            <li>Forma de aceite e dados de pagamento, quando fizer sentido.</li>
        </ul>

        <h2>Exemplo simples de estrutura</h2>
        <p>
            Imagine uma proposta para criação de identidade visual. O título pode ser “Proposta para identidade visual da Marca X”. A introdução explica o objetivo: criar um sistema visual profissional para uso em redes sociais, apresentações e materiais comerciais. Depois, a proposta lista os itens: reunião inicial, criação de conceito, paleta de cores, tipografia, logotipo principal, variações e manual simplificado.
        </p>
        <p>
            Cada item pode ter valor próprio ou compor um pacote fechado. O importante é que o cliente veja o que está comprando. Ao final, inclua total, prazo, validade e instruções para aceitar. Isso reduz retrabalho e evita aquele cenário em que o cliente aprova algo que não estava combinado.
        </p>

        @include('seo._cta', [
            'title' => 'Transforme seu modelo em uma proposta real',
            'text' => 'No propostei, você cadastra o cliente, adiciona serviços e envia uma proposta por link ou PDF.',
            'label' => 'Criar minha proposta grátis',
        ])

        <h2>Erros comuns ao montar uma proposta</h2>
        <p>
            O erro mais comum é enviar apenas um valor solto, sem explicar escopo. Outro problema é não definir validade, o que deixa o preço aberto por tempo indefinido. Também é comum misturar observações internas com informações para o cliente, usar linguagem vaga ou deixar itens importantes fora do documento.
        </p>
        <p>
            Uma proposta profissional não precisa ser formal demais, mas precisa ser objetiva. Evite prometer resultados que dependem de terceiros, descreva o que está incluso e, se houver limites, deixe claro. Isso protege o prestador e ajuda o cliente a comparar opções de forma justa.
        </p>

        <h2>Como o propostei ajuda</h2>
        <p>
            O propostei funciona como um <a href="{{ url('/gerador-de-proposta-comercial') }}">gerador de proposta comercial online</a>. Você cria uma conta grátis, cadastra o cliente, adiciona serviços e valores, define prazo e validade, e gera uma página pública para enviar. O cliente pode visualizar sem login, aceitar ou recusar a proposta e você acompanha o status no painel.
        </p>
        <p>
            Se sua busca começou por um orçamento, veja também o guia de <a href="{{ url('/modelo-de-orcamento-para-cliente') }}">modelo de orçamento para cliente</a>. Para serviços recorrentes ou projetos sob demanda, o conteúdo sobre <a href="{{ url('/proposta-comercial-prestacao-de-servico') }}">proposta comercial para prestação de serviço</a> pode ser mais específico.
        </p>

        <h2>Perguntas frequentes</h2>
        @foreach ($faq as $item)
            <h3>{{ $item['question'] }}</h3>
            <p>{{ $item['answer'] }}</p>
        @endforeach
    </div>

    @include('seo._cta', [
        'title' => 'Crie sua próxima proposta em poucos minutos',
        'text' => 'Comece grátis e envie uma proposta profissional por link para o seu cliente.',
        'label' => 'Começar grátis',
    ])
@endsection
