<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const steps = [
    {
        title: 'Cadastre o cliente',
        description: 'Guarde nome, empresa, e-mail, telefone e observações importantes em um só lugar.',
    },
    {
        title: 'Monte a proposta',
        description: 'Adicione serviços, quantidades, valores unitários, validade e prazo de entrega.',
    },
    {
        title: 'Envie o link',
        description: 'Compartilhe uma página pública profissional, pronta para o cliente avaliar.',
    },
    {
        title: 'Acompanhe o aceite',
        description: 'Veja status, data, responsável e histórico da proposta no painel.',
    },
];

const benefits = [
    'Propostas com aparência profissional sem planilha improvisada.',
    'Controle de status: rascunho, enviada, aceita ou recusada.',
    'Página pública com aceite simples e rastreável.',
    'Histórico organizado para consultar propostas antigas quando precisar.',
];

const interestForm = useForm({
    source: 'landing',
});

const freePlanBenefits = [
    'Até 5 propostas',
    'Link público',
    'PDF',
    'Aceite e recusa',
];

const premiumPlanBenefits = [
    'Propostas ilimitadas',
    'Mais liberdade para uso comercial',
    'Recursos premium futuros',
    'Ideal para freelancers e prestadores',
];

const registerPremiumInterest = () => {
    interestForm.post(route('premium-interest.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="propostei" />

    <div class="min-h-screen bg-slate-50 text-slate-950">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <span class="block h-12 w-12 overflow-hidden rounded-md shadow-sm">
                        <img
                            src="/propostei-logo.png"
                            alt="propostei"
                            class="h-full w-full scale-[2.15] object-cover"
                        />
                    </span>
                    <span class="text-lg font-semibold text-slate-950">propostei</span>
                </Link>

                <nav v-if="canLogin" class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                    >
                        Abrir painel
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="text-sm font-semibold text-slate-700 hover:text-slate-950">
                            Entrar
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                        >
                            Criar conta
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main>
            <div class="mx-auto max-w-6xl px-4 pt-6 sm:px-6 lg:px-8">
                <div
                    v-if="$page.props.flash?.success"
                    class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                >
                    {{ $page.props.flash.success }}
                </div>
                <div
                    v-if="$page.props.flash?.error"
                    class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                >
                    {{ $page.props.flash.error }}
                </div>
            </div>

            <section class="border-b border-slate-200 bg-white">
                <div class="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:px-6 sm:py-16 lg:grid-cols-[1fr_360px] lg:px-8 lg:py-20">
                    <div class="max-w-3xl">
                        <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">
                            Propostas comerciais em minutos
                        </p>
                        <h1 class="mt-4 text-4xl font-semibold tracking-normal text-slate-950 sm:text-5xl lg:text-6xl">
                            Envie propostas profissionais e acompanhe o aceite sem perder o histórico.
                        </h1>
                        <p class="mt-5 text-lg leading-8 text-slate-600">
                            O propostei ajuda freelancers e pequenos prestadores a cadastrar clientes, montar serviços com valores, compartilhar um link público e registrar quem aceitou cada proposta.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('dashboard')"
                                class="rounded-md bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                            >
                                Ir para o painel
                            </Link>
                            <Link
                                v-else
                                :href="route('register')"
                                class="rounded-md bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                            >
                                Criar minha conta
                            </Link>
                            <Link :href="route('login')" class="rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                Entrar
                            </Link>
                        </div>

                        <dl class="mt-10 grid gap-4 sm:grid-cols-3">
                            <div>
                                <dt class="text-sm text-slate-500">Tempo para criar</dt>
                                <dd class="mt-1 text-2xl font-semibold text-slate-950">5 min</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-slate-500">Link público</dt>
                                <dd class="mt-1 text-2xl font-semibold text-slate-950">1 clique</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-slate-500">Status</dt>
                                <dd class="mt-1 text-2xl font-semibold text-slate-950">4 etapas</dd>
                            </div>
                        </dl>
                    </div>

                    <aside class="rounded-lg border border-slate-200 bg-slate-50 p-6 shadow-sm">
                        <div class="flex items-center gap-3">
                            <span class="block h-12 w-12 overflow-hidden rounded-md shadow-sm">
                                <img
                                    src="/propostei-logo.png"
                                    alt="propostei"
                                    class="h-full w-full scale-[2.15] object-cover"
                                />
                            </span>
                            <div>
                                <div class="text-sm font-semibold text-slate-950">Fluxo completo</div>
                                <div class="text-sm text-slate-500">Da criação ao aceite</div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div class="rounded-md border border-slate-200 bg-white p-4">
                                <div class="text-sm font-semibold text-slate-950">1. Proposta organizada</div>
                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    Serviços, quantidades, valores, validade e prazo em uma estrutura clara.
                                </p>
                            </div>
                            <div class="rounded-md border border-slate-200 bg-white p-4">
                                <div class="text-sm font-semibold text-slate-950">2. Link para o cliente</div>
                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    Uma página pública para o cliente visualizar sem precisar de conta.
                                </p>
                            </div>
                            <div class="rounded-md border border-emerald-200 bg-emerald-50 p-4">
                                <div class="text-sm font-semibold text-emerald-900">3. Aceite rastreável</div>
                                <p class="mt-1 text-sm leading-6 text-emerald-800">
                                    Registro de data, responsável e status para consulta posterior.
                                </p>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">Como funciona</p>
                    <h2 class="mt-3 text-3xl font-semibold text-slate-950">
                        Do cadastro ao aceite, o fluxo fica simples.
                    </h2>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-4">
                    <article
                        v-for="(step, index) in steps"
                        :key="step.title"
                        class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm"
                    >
                        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-teal-700 text-sm font-semibold text-white">
                            {{ index + 1 }}
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-slate-950">{{ step.title }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ step.description }}</p>
                    </article>
                </div>
            </section>

            <section class="border-y border-slate-200 bg-white">
                <div class="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[0.85fr_1.15fr] lg:px-8">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">Por que usar</p>
                        <h2 class="mt-3 text-3xl font-semibold text-slate-950">
                            Uma proposta mais clara ajuda o cliente a decidir.
                        </h2>
                        <p class="mt-4 text-base leading-7 text-slate-600">
                            O objetivo não é criar um CRM complexo. É dar ao prestador uma forma rápida e organizada de enviar propostas que parecem profissionais e podem ser acompanhadas depois.
                        </p>
                    </div>

                    <div class="grid gap-3">
                        <div
                            v-for="benefit in benefits"
                            :key="benefit"
                            class="flex gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4"
                        >
                            <span class="mt-1 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800">✓</span>
                            <p class="text-sm leading-6 text-slate-700">{{ benefit }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">Preços</p>
                    <h2 class="mt-3 text-3xl font-semibold text-slate-950">
                        Comece grátis. Cresça quando fizer sentido.
                    </h2>
                    <p class="mt-4 text-base leading-7 text-slate-600">
                        Comece grátis com até 5 propostas profissionais. Quando precisar de mais, registre interesse no Premium por R$ 19,90/mês e tenha propostas ilimitadas.
                    </p>
                </div>

                <div class="mt-8 grid gap-6 lg:grid-cols-2">
                    <article class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-950">Grátis</h3>
                                <p class="mt-2 text-sm text-slate-600">
                                    Comece criando até 5 propostas profissionais.
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-semibold text-slate-950">R$ 0</div>
                                <div class="text-sm text-slate-500">para começar</div>
                            </div>
                        </div>

                        <ul class="mt-6 space-y-3">
                            <li v-for="benefit in freePlanBenefits" :key="benefit" class="flex gap-3 text-sm text-slate-700">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800">✓</span>
                                <span>{{ benefit }}</span>
                            </li>
                        </ul>
                    </article>

                    <article class="rounded-lg border border-teal-700 bg-slate-950 p-6 text-white shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-semibold">Premium</h3>
                                <p class="mt-2 text-sm text-slate-300">
                                    Para quem envia propostas com frequência.
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-semibold">R$ 19,90</div>
                                <div class="text-sm text-slate-300">/mês</div>
                            </div>
                        </div>

                        <ul class="mt-6 space-y-3">
                            <li v-for="benefit in premiumPlanBenefits" :key="benefit" class="flex gap-3 text-sm text-slate-200">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-teal-400 text-xs font-bold text-slate-950">✓</span>
                                <span>{{ benefit }}</span>
                            </li>
                        </ul>

                        <Link
                            v-if="!$page.props.auth.user"
                            :href="route('register')"
                            class="mt-8 inline-flex w-full justify-center rounded-md bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100"
                        >
                            Criar conta para demonstrar interesse
                        </Link>
                        <button
                            v-else
                            type="button"
                            class="mt-8 inline-flex w-full justify-center rounded-md bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100 disabled:opacity-70"
                            :disabled="interestForm.processing"
                            @click="registerPremiumInterest"
                        >
                            {{ interestForm.processing ? 'Registrando interesse...' : 'Quero ser Premium' }}
                        </button>
                    </article>
                </div>

                <div class="mt-6">
                    <Link
                        :href="route('plans.index')"
                        class="inline-flex rounded-md border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Ver planos
                    </Link>
                </div>
            </section>

            <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <div class="rounded-lg border border-slate-200 bg-slate-950 p-8 text-white shadow-sm sm:p-10">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold">Pronto para enviar propostas melhores?</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">
                                Crie uma conta, cadastre seu primeiro cliente e envie uma proposta por link ainda hoje.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('dashboard')"
                                class="rounded-md bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100"
                            >
                                Abrir painel
                            </Link>
                            <Link
                                v-else
                                :href="route('register')"
                                class="rounded-md bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100"
                            >
                                Criar conta
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="text-sm font-semibold text-slate-950">Guias para criar propostas</div>
                <nav class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2 lg:grid-cols-3">
                    <a href="/modelo-de-proposta-comercial" class="hover:text-slate-950">Modelo de proposta comercial</a>
                    <a href="/gerador-de-proposta-comercial" class="hover:text-slate-950">Gerador de proposta comercial</a>
                    <a href="/proposta-comercial-prestacao-de-servico" class="hover:text-slate-950">Proposta para prestação de serviço</a>
                    <a href="/modelo-de-orcamento-para-cliente" class="hover:text-slate-950">Modelo de orçamento para cliente</a>
                    <a href="/proposta-comercial-freelancer" class="hover:text-slate-950">Proposta para freelancer</a>
                    <a href="/contato" class="hover:text-slate-950">Contato</a>
                </nav>
            </div>
        </footer>
    </div>
</template>
