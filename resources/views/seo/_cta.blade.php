@php
    $ctaUrl = auth()->check() ? route('proposals.create') : route('register');
@endphp

<div class="my-10 rounded-lg border border-teal-200 bg-teal-50 p-6 shadow-sm">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-950">{{ $title ?? 'Crie sua proposta com o propostei' }}</h2>
            <p class="mt-2 text-sm leading-6 text-slate-700">
                {{ $text ?? 'Monte propostas profissionais, envie por link e gere PDF em poucos minutos.' }}
            </p>
        </div>
        <a href="{{ $ctaUrl }}" class="inline-flex justify-center rounded-md bg-teal-700 px-5 py-3 text-sm font-semibold !text-white no-underline shadow-sm transition hover:bg-teal-800">
            {{ $label ?? 'Começar grátis' }}
        </a>
    </div>
</div>
