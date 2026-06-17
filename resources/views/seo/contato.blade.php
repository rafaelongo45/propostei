@extends('layouts.seo')

@section('intro')
    Fale com o propostei para tirar dúvidas, enviar sugestões ou pedir ajuda sobre propostas comerciais online.
@endsection

@section('primaryCta', 'Criar conta grátis')

@section('content')
    <div class="seo-content">
        <p>
            Use o formulário abaixo para falar com a equipe do propostei. Se você encontrou algum problema, tem uma sugestão ou quer entender melhor como a plataforma funciona, mande uma mensagem.
        </p>
        <p>
            A resposta será enviada para o e-mail informado no formulário.
        </p>
    </div>

    <section class="mt-10 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
        @if (session('status'))
            <div class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3">
                <p class="text-sm font-semibold text-red-800">Revise os campos abaixo.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}" class="grid gap-5">
            @csrf

            <div class="hidden" aria-hidden="true">
                <label for="website">Site</label>
                <input id="website" type="text" name="website" value="" tabindex="-1" autocomplete="off">
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-800">Nome</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        maxlength="120"
                        autocomplete="name"
                        class="mt-2 block w-full rounded-md border-slate-300 text-slate-950 shadow-sm focus:border-teal-600 focus:ring-teal-600"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-800">E-mail</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        maxlength="180"
                        autocomplete="email"
                        inputmode="email"
                        class="mt-2 block w-full rounded-md border-slate-300 text-slate-950 shadow-sm focus:border-teal-600 focus:ring-teal-600"
                    >
                </div>
            </div>

            <div>
                <label for="subject" class="block text-sm font-semibold text-slate-800">Assunto <span class="font-normal text-slate-500">(opcional)</span></label>
                <input
                    id="subject"
                    name="subject"
                    type="text"
                    value="{{ old('subject') }}"
                    maxlength="140"
                    class="mt-2 block w-full rounded-md border-slate-300 text-slate-950 shadow-sm focus:border-teal-600 focus:ring-teal-600"
                >
            </div>

            <div>
                <label for="message" class="block text-sm font-semibold text-slate-800">Mensagem</label>
                <textarea
                    id="message"
                    name="message"
                    rows="7"
                    required
                    minlength="10"
                    maxlength="3000"
                    class="mt-2 block w-full resize-y rounded-md border-slate-300 text-slate-950 shadow-sm focus:border-teal-600 focus:ring-teal-600"
                >{{ old('message') }}</textarea>
                <p class="mt-2 text-sm text-slate-500">Inclua o máximo de contexto possível para facilitar a resposta.</p>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">
                    Seus dados serão usados apenas para responder esta mensagem.
                </p>
                <button type="submit" class="inline-flex justify-center rounded-md bg-teal-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800">
                    Enviar mensagem
                </button>
            </div>
        </form>
    </section>
@endsection
