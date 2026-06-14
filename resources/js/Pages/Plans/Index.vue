<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    planUsage: Object,
});

const page = usePage();
const interestForm = useForm({
    source: 'plans',
});

const freeBenefits = [
    'Até 5 propostas',
    'Clientes cadastrados',
    'Link público da proposta',
    'PDF',
    'Aceite e recusa pelo cliente',
];

const premiumBenefits = [
    'Propostas ilimitadas',
    'Mais liberdade para uso comercial',
    'Recursos premium futuros',
    'Ideal para quem envia propostas com frequência',
];

const registerPremiumInterest = () => {
    interestForm.post(route('premium-interest.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Planos" />

    <div class="min-h-screen bg-slate-50 text-slate-950">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <span class="block h-12 w-12 overflow-hidden rounded-md shadow-sm">
                        <img src="/propostei-logo.png" alt="propostei" class="h-full w-full scale-[2.15] object-cover" />
                    </span>
                    <span class="text-lg font-semibold">propostei</span>
                </Link>

                <nav class="flex items-center gap-3">
                    <Link
                        v-if="page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
                    >
                        Abrir painel
                    </Link>
                    <template v-else>
                        <Link :href="route('login')" class="text-sm font-semibold text-slate-700 hover:text-slate-950">
                            Entrar
                        </Link>
                        <Link :href="route('register')" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                            Criar conta
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
            <div
                v-if="page.props.flash?.success"
                class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
            >
                {{ page.props.flash.success }}
            </div>

            <div
                v-if="page.props.flash?.error"
                class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
            >
                {{ page.props.flash.error }}
            </div>

            <section class="max-w-2xs">
                <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">Planos</p>
                <h1 class="mt-3 text-4xl font-semibold text-slate-950 sm:text-4xl">
                    Comece grátis e evolua quando precisar de mais propostas.
                </h1>
                <p class="mt-5 text-lg leading-8 text-slate-600">
                    Crie até 5 propostas profissionais sem pagar nada. Quando precisar de mais, registre interesse no Premium por R$ 19,90/mês e tenha propostas ilimitadas.
                </p>
            </section>

            <section
                v-if="planUsage"
                class="mt-8 rounded-lg border p-5 shadow-sm"
                :class="planUsage.is_premium ? 'border-emerald-200 bg-emerald-50' : planUsage.can_create_proposal ? 'border-slate-200 bg-white' : 'border-red-200 bg-red-50'"
            >
                <div class="text-sm font-semibold" :class="planUsage.is_premium ? 'text-emerald-900' : planUsage.can_create_proposal ? 'text-slate-900' : 'text-red-900'">
                    Seu plano atual
                </div>
                <p v-if="planUsage.is_premium" class="mt-1 text-sm text-emerald-800">
                    Plano Premium ativo: propostas ilimitadas.
                </p>
                <p v-else-if="planUsage.can_create_proposal" class="mt-1 text-sm text-slate-700">
                    Plano grátis: {{ planUsage.used }} de {{ planUsage.limit }} propostas usadas.
                </p>
                <p v-else class="mt-1 text-sm text-red-800">
                    Você atingiu o limite do plano grátis. Registre interesse no Premium por R$ 19,90/mês para criar propostas ilimitadas.
                </p>
            </section>

            <section class="mt-10 grid gap-6 lg:grid-cols-2">
                <article class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-950">Grátis</h2>
                            <p class="mt-2 text-sm text-slate-600">
                                Para começar validando seu fluxo comercial.
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-semibold text-slate-950">R$ 0</div>
                            <div class="text-sm text-slate-500">sem mensalidade</div>
                        </div>
                    </div>

                    <p class="mt-6 rounded-md bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        Comece criando até 5 propostas profissionais.
                    </p>

                    <ul class="mt-6 space-y-3">
                        <li v-for="benefit in freeBenefits" :key="benefit" class="flex gap-3 text-sm text-slate-700">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-800">✓</span>
                            <span>{{ benefit }}</span>
                        </li>
                    </ul>

                    <Link
                        v-if="!page.props.auth.user"
                        :href="route('register')"
                        class="mt-8 inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Começar grátis
                    </Link>
                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="mt-8 inline-flex w-full justify-center rounded-md border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Voltar ao painel
                    </Link>
                </article>

                <article class="rounded-lg border border-teal-700 bg-slate-950 p-6 text-white shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-semibold">Premium</h2>
                            <p class="mt-2 text-sm text-slate-300">
                                Para quem envia propostas com frequência.
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-semibold">R$ 19,90</div>
                            <div class="text-sm text-slate-300">/mês</div>
                        </div>
                    </div>

                    <p class="mt-6 rounded-md bg-white/10 px-4 py-3 text-sm text-slate-200">
                        Mais liberdade para uso comercial e uma base pronta para recursos premium futuros.
                    </p>

                    <ul class="mt-6 space-y-3">
                        <li v-for="benefit in premiumBenefits" :key="benefit" class="flex gap-3 text-sm text-slate-200">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-teal-400 text-xs font-bold text-slate-950">✓</span>
                            <span>{{ benefit }}</span>
                        </li>
                    </ul>

                    <Link
                        v-if="!page.props.auth.user"
                        :href="route('register')"
                        class="mt-8 inline-flex w-full justify-center rounded-md bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100"
                    >
                        Criar conta para demonstrar interesse
                    </Link>
                    <button
                        v-else-if="planUsage?.is_premium"
                        type="button"
                        disabled
                        class="mt-8 inline-flex w-full cursor-not-allowed justify-center rounded-md bg-white/70 px-4 py-3 text-sm font-semibold text-slate-700"
                    >
                        Premium ativo
                    </button>
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
            </section>
        </main>
    </div>
</template>
