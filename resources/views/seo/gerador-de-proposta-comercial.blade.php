@php
    $faq = [
        [
            'question' => 'O que é um gerador de proposta comercial?',
            'answer' => 'É uma ferramenta para criar propostas com cliente, escopo, itens, valores, prazo e validade sem depender de arquivos soltos ou mensagens improvisadas.',
        ],
        [
            'question' => 'Preciso instalar algum programa?',
            'answer' => 'Não. O propostei funciona online: você acessa pelo navegador, cria a proposta e compartilha o link com o cliente.',
        ],
        [
            'question' => 'Dá para gerar PDF da proposta?',
            'answer' => 'Sim. Além do link público, o propostei permite baixar a proposta em PDF para enviar ou arquivar.',
        ],
        [
            'question' => 'O cliente precisa ter conta para ver a proposta?',
            'answer' => 'Não. O cliente acessa a proposta por um link público e pode aceitar ou recusar sem criar cadastro.',
        ],
    ];
@endphp

@extends('layouts.seo')

@section('intro')
    Crie propostas comerciais online sem depender de Word, Canva, planilhas ou textos perdidos no WhatsApp. O propostei organiza o fluxo para você enviar algo profissional em poucos minutos.
@endsection

@section('primaryCta', 'Gerar proposta online grátis')

@section('content')
    <div class="seo-content">
        <p>
            Um gerador de proposta comercial online economiza tempo e reduz erros. Em vez de copiar um arquivo antigo, trocar dados manualmente e correr o risco de esquecer um valor, você preenche as informações essenciais e monta uma proposta pronta para enviar. Para freelancers, autônomos, MEIs e pequenos prestadores, isso significa menos improviso e mais clareza na hora de fechar serviços.
        </p>

        <h2>Por que não usar apenas Word ou WhatsApp?</h2>
        <p>
            Word e Canva podem funcionar para criar um visual bonito, mas não resolvem o fluxo completo. Você ainda precisa controlar cliente, status, validade, itens, valores, aceite e versões. Já o WhatsApp é rápido, mas se perde facilmente: o preço fica em uma mensagem, o prazo em outra e o escopo vira uma conversa longa.
        </p>
        <p>
            O problema não é o canal, e sim a falta de estrutura. Uma proposta comercial precisa deixar claro o que será entregue e como o cliente avança. Quando tudo está em um link ou PDF organizado, a decisão fica mais simples.
        </p>

        <h2>Como funciona um gerador de proposta</h2>
        <ol>
            <li>Você cadastra o cliente com nome, e-mail, empresa e telefone.</li>
            <li>Cria uma proposta com título, introdução, validade e prazo.</li>
            <li>Adiciona serviços, quantidades e valores unitários.</li>
            <li>Confere subtotal e total final.</li>
            <li>Envia um link público ou baixa o PDF.</li>
            <li>Acompanha se a proposta foi aceita ou recusada.</li>
        </ol>

        @include('seo._cta', [
            'title' => 'Gere uma proposta comercial online agora',
            'text' => 'Cadastre o cliente, adicione seus serviços e envie um link profissional para aprovação.',
            'label' => 'Gerar proposta online grátis',
        ])

        <h2>O que incluir na proposta gerada</h2>
        <p>
            Mesmo usando uma ferramenta, o conteúdo continua importante. Descreva o objetivo do serviço, os itens contratados, o prazo estimado, a validade do preço e as condições de pagamento. Se o trabalho tiver etapas, liste-as de forma objetiva. Se houver limites de revisão, suporte ou entrega, deixe claro no texto.
        </p>
        <p>
            Um bom gerador não deve complicar. Ele deve ajudar você a montar uma estrutura consistente sem transformar a criação de proposta em burocracia.
        </p>

        <h2>Quando usar uma proposta por link</h2>
        <p>
            A proposta por link é útil quando você quer enviar algo rápido, mas ainda profissional. Ela funciona bem depois de uma reunião comercial, uma conversa por WhatsApp ou uma troca de e-mails em que o cliente já demonstrou interesse. Em vez de anexar várias versões de arquivo, você compartilha uma página única, com o resumo do que foi combinado.
        </p>
        <p>
            Outra vantagem é a organização do histórico. Se o cliente pede alteração, você atualiza a proposta dentro do sistema. Se ele aceita, o status fica registrado. Para quem trabalha com muitos orçamentos pequenos ao longo do mês, esse controle evita perder oportunidades ou esquecer quais propostas ainda estão em aberto.
        </p>

        <h2>Para quem o propostei é indicado</h2>
        <p>
            O propostei foi pensado para quem vende serviço: designers, desenvolvedores, social medias, gestores de tráfego, consultores, fotógrafos, redatores, editores de vídeo, agências pequenas e prestadores locais. Se você precisa enviar uma proposta com itens e valores, o fluxo serve.
        </p>
        <p>
            Se você quer entender a estrutura antes de criar, veja o guia de <a href="{{ url('/modelo-de-proposta-comercial') }}">modelo de proposta comercial</a>. Se trabalha como autônomo criativo ou técnico, veja também a página de <a href="{{ url('/proposta-comercial-freelancer') }}">proposta comercial para freelancer</a>.
        </p>

        <h2>Perguntas frequentes</h2>
        @foreach ($faq as $item)
            <h3>{{ $item['question'] }}</h3>
            <p>{{ $item['answer'] }}</p>
        @endforeach
    </div>

    @include('seo._cta', [
        'title' => 'Saia do orçamento improvisado',
        'text' => 'Use o propostei para criar propostas comerciais por link e PDF com aparência profissional.',
        'label' => 'Começar grátis',
    ])
@endsection
