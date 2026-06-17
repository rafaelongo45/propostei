<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    periods: Object,
    metrics: Array,
    funnel: Array,
    recentEvents: Array,
    recentUsers: Array,
});

const featuredEvents = [
    'landing_viewed',
    'register_clicked',
    'user_registered',
    'proposal_created',
    'proposal_public_viewed',
    'proposal_accepted',
    'premium_interest_clicked',
];

const featuredMetrics = computed(() => props.metrics.filter((metric) => featuredEvents.includes(metric.event)));

const formatCount = (value) => new Intl.NumberFormat('pt-BR').format(value || 0);
const formatRate = (value) => (value === null ? '—' : `${new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 1 }).format(value)}%`);
const metadataPreview = (metadata) => {
    if (!metadata) {
        return '—';
    }

    return JSON.stringify(metadata).slice(0, 140);
};
</script>

<template>
    <Head title="Métricas do funil" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900">
                        Métricas do funil
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Acompanhe as etapas principais: visita, cadastro, proposta, envio e aceite.
                    </p>
                </div>
                <Link
                    :href="route('admin.premium-interests.index')"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                >
                    Ver interessados Premium
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto flex max-w-5xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
                <section class="grid gap-4 md:grid-cols-2">
                    <article
                        v-for="metric in featuredMetrics"
                        :key="metric.event"
                        class="h-36 overflow-hidden rounded-lg border border-gray-200 bg-white p-5 shadow-sm"
                    >
                        <div class="text-sm font-semibold text-gray-900">{{ metric.label }}</div>
                        <dl class="mt-4 grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Últimos 30 dias</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-950">
                                    {{ formatCount(metric.counts.thirty_days) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Total</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-950">
                                    {{ formatCount(metric.counts.total) }}
                                </dd>
                            </div>
                        </dl>
                    </article>
                </section>

                <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-950">Funil dos últimos 30 dias</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Taxas calculadas contra a etapa anterior mais relevante.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 max-h-80 overflow-y-auto rounded-md border border-gray-200">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Etapa</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Volume</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Taxa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="step in funnel" :key="step.label">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ step.label }}</td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-950">{{ formatCount(step.count) }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-700">{{ formatRate(step.rate) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="flex flex-col gap-6">
                    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-4">
                            <h3 class="text-base font-semibold text-gray-950">Eventos recentes</h3>
                            <span class="text-xs font-medium text-gray-500">Últimos 15</span>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <div v-if="recentEvents.length === 0" class="px-5 py-8 text-center text-sm text-gray-500">
                                Nenhum evento registrado ainda.
                            </div>
                            <article
                                v-for="event in recentEvents"
                                :key="event.id"
                                class="border-b border-gray-100 px-5 py-4 last:border-b-0"
                            >
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold text-gray-950">{{ event.label }}</div>
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ event.user ? `${event.user.name} - ${event.user.email}` : 'Visitante sem usuário' }}
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-xs text-gray-500">{{ event.created_at }}</div>
                                </div>
                                <p class="mt-2 break-words text-xs text-gray-500">
                                    {{ metadataPreview(event.metadata) }}
                                </p>
                            </article>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-4">
                            <h3 class="text-base font-semibold text-gray-950">Usuários recentes</h3>
                            <span class="text-xs font-medium text-gray-500">Últimos 12</span>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <div v-if="recentUsers.length === 0" class="px-5 py-8 text-center text-sm text-gray-500">
                                Nenhum usuário cadastrado ainda.
                            </div>
                            <article
                                v-for="user in recentUsers"
                                :key="user.id"
                                class="border-b border-gray-100 px-5 py-4 last:border-b-0"
                            >
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="break-words text-sm font-semibold text-gray-950">{{ user.name }}</div>
                                        <div class="break-words text-xs text-gray-500">{{ user.email }}</div>
                                        <div class="text-xs text-gray-400">{{ user.created_at }}</div>
                                    </div>
                                    <span
                                        class="w-fit shrink-0 rounded-full px-3 py-1 text-xs font-semibold ring-1"
                                        :class="user.has_premium_interest ? 'bg-teal-50 text-teal-700 ring-teal-200' : 'bg-gray-100 text-gray-600 ring-gray-200'"
                                    >
                                        {{ user.has_premium_interest ? 'Premium' : 'Sem premium' }}
                                    </span>
                                </div>

                                <dl class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                                    <div>
                                        <dt class="text-xs text-gray-500">Clientes</dt>
                                        <dd class="font-semibold text-gray-900">{{ user.clients_count }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Propostas</dt>
                                        <dd class="font-semibold text-gray-900">{{ user.proposals_count }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Aceitas</dt>
                                        <dd class="font-semibold text-gray-900">{{ user.accepted_proposals_count }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Eventos</dt>
                                        <dd class="font-semibold text-gray-900">{{ user.events_count }}</dd>
                                    </div>
                                </dl>
                            </article>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
