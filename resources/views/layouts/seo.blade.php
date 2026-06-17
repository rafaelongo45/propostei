@php
    $marketingQuery = request()->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'gclid']);
    $ctaUrl = auth()->check() ? route('proposals.create') : route('start', $marketingQuery);
    $loginUrl = auth()->check() ? route('dashboard') : route('login');
    $seoLinks = [
        'Modelo de proposta comercial' => url('/modelo-de-proposta-comercial'),
        'Gerador de proposta comercial' => url('/gerador-de-proposta-comercial'),
        'Proposta para prestação de serviço' => url('/proposta-comercial-prestacao-de-servico'),
        'Modelo de orçamento para cliente' => url('/modelo-de-orcamento-para-cliente'),
        'Proposta para freelancer' => url('/proposta-comercial-freelancer'),
        'Sobre o propostei' => url('/sobre'),
    ];
    $webPageSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $title,
        'description' => $description,
        'url' => $canonical,
    ];
    $faqSchema = ! empty($faq) ? [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($faq)->map(fn ($item) => [
            '@type' => 'Question',
            'name' => $item['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $item['answer'],
            ],
        ])->values()->all(),
    ] : null;
@endphp

<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:site_name" content="propostei">
    <meta property="og:image" content="{{ url('/propostei-logo.png') }}">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ url('/propostei-logo.png') }}">
    <link rel="icon" type="image/svg+xml" href="/propostei-favicon.svg">
    <link rel="apple-touch-icon" href="/propostei-logo.png">
    <script type="application/ld+json">@json($webPageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
    @if ($faqSchema)
        <script type="application/ld+json">@json($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)</script>
    @endif
    <style>
        .seo-content h2 {
            color: #0f172a;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.25;
            margin-top: 2.25rem;
        }

        .seo-content h3 {
            color: #0f172a;
            font-size: 1.125rem;
            font-weight: 700;
            margin-top: 1.5rem;
        }

        .seo-content p,
        .seo-content li {
            color: #475569;
            font-size: 1rem;
            line-height: 1.8;
            margin-top: 1rem;
        }

        .seo-content ul,
        .seo-content ol {
            margin-top: 1rem;
            padding-left: 1.5rem;
        }

        .seo-content ul {
            list-style: disc;
        }

        .seo-content ol {
            list-style: decimal;
        }

        .seo-content a {
            color: #0f766e;
            font-weight: 600;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
    </style>
    @vite(['resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-950 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <span class="block h-12 w-12 overflow-hidden rounded-md shadow-sm">
                    <img src="/propostei-logo.png" alt="propostei" class="h-full w-full scale-[2.15] object-cover">
                </span>
                <span class="text-lg font-semibold text-slate-950">propostei</span>
            </a>
            <nav class="flex items-center gap-3">
                <a href="{{ $loginUrl }}" class="text-sm font-semibold text-slate-700 hover:text-slate-950">
                    {{ auth()->check() ? 'Painel' : 'Entrar' }}
                </a>
                <a href="{{ $ctaUrl }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    Começar grátis
                </a>
            </nav>
        </div>
    </header>

    <main>
        <section class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">propostei</p>
                    <h1 class="mt-4 text-4xl font-semibold tracking-normal text-slate-950 sm:text-5xl">{{ $h1 }}</h1>
                    <p class="mt-5 text-lg leading-8 text-slate-600">@yield('intro')</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ $ctaUrl }}" class="rounded-md bg-teal-700 px-5 py-3 text-sm font-semibold !text-white no-underline shadow-sm transition hover:bg-teal-800">
                    @yield('primaryCta', 'Começar grátis')
                </a>
                <a href="{{ route('demo.proposal') }}" class="rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Ver exemplo de proposta
                </a>
                    </div>
                </div>
            </div>
        </section>

        <article class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
            @yield('content')
        </article>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-[1fr_2fr] lg:px-8">
            <div>
                <div class="flex items-center gap-3">
                    <span class="block h-10 w-10 overflow-hidden rounded-md shadow-sm">
                        <img src="/propostei-logo.png" alt="propostei" class="h-full w-full scale-[2.15] object-cover">
                    </span>
                    <span class="font-semibold text-slate-950">propostei</span>
                </div>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    Propostas comerciais profissionais por link ou PDF para freelancers, MEIs e prestadores de serviço.
                </p>
            </div>
            <div>
                <div class="text-sm font-semibold text-slate-950">Guias úteis</div>
                <nav class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                    @foreach ($seoLinks as $label => $href)
                        <a href="{{ $href }}" class="text-slate-600 hover:text-slate-950">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>
        </div>
    </footer>
</body>
</html>
