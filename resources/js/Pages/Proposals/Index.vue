<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    proposals: Array,
    planUsage: Object,
    stats: Object,
});

const page = usePage();

const statusLabels = {
    draft: 'Rascunho',
    sent: 'Enviada',
    accepted: 'Aceita',
    rejected: 'Recusada',
};

const statusClasses = {
    draft: 'bg-slate-100 text-slate-700 ring-slate-200',
    sent: 'bg-blue-50 text-blue-700 ring-blue-200',
    accepted: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    rejected: 'bg-rose-50 text-rose-700 ring-rose-200',
};

const formatMoney = (value) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value || 0);
</script>

<template>
    <Head title="Propostas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900">
                        Propostas
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Acompanhe propostas, valores e aceite do cliente.
                    </p>
                </div>
                <Link
                    :href="route('proposals.create')"
                    class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                >
                    Nova proposta
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div
                    v-if="page.props.flash?.success"
                    class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                >
                    {{ page.props.flash.success }}
                </div>
                <div
                    v-if="page.props.flash?.error"
                    class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                >
                    {{ page.props.flash.error }}
                </div>

                <section
                    class="mb-6 rounded-lg border p-5 shadow-sm"
                    :class="planUsage.is_premium ? 'border-emerald-200 bg-emerald-50' : planUsage.can_create_proposal ? 'border-gray-200 bg-white' : 'border-red-200 bg-red-50'"
                >
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="text-sm font-semibold" :class="planUsage.is_premium ? 'text-emerald-900' : planUsage.can_create_proposal ? 'text-gray-900' : 'text-red-900'">
                                Plano atual
                            </div>
                            <p v-if="planUsage.is_premium" class="mt-1 text-sm text-emerald-800">
                                Plano Premium ativo: propostas ilimitadas.
                            </p>
                            <p v-else-if="planUsage.can_create_proposal" class="mt-1 text-sm text-gray-600">
                                Plano grátis: {{ planUsage.used }} de {{ planUsage.limit }} propostas usadas.
                            </p>
                            <p v-else class="mt-1 text-sm text-red-800">
                                Você atingiu o limite do plano grátis. Assine o Premium por R$ 19,90/mês para criar propostas ilimitadas.
                            </p>
                        </div>
                        <Link
                            :href="route('plans.index')"
                            class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        >
                            Ver planos
                        </Link>
                    </div>
                </section>

                <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-sm text-gray-500">Total</div>
                        <div class="mt-2 text-2xl font-semibold text-gray-900">{{ stats.total }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-sm text-gray-500">Aceitas</div>
                        <div class="mt-2 text-2xl font-semibold text-emerald-700">{{ stats.accepted }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-sm text-gray-500">Enviadas</div>
                        <div class="mt-2 text-2xl font-semibold text-blue-700">{{ stats.sent }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="text-sm text-gray-500">Rascunhos</div>
                        <div class="mt-2 text-2xl font-semibold text-gray-700">{{ stats.draft }}</div>
                    </div>
                    <div class="rounded-lg border border-red-200 bg-red-50 p-5 shadow-sm">
                        <div class="text-sm text-red-700">Vencidas</div>
                        <div class="mt-2 text-2xl font-semibold text-red-700">{{ stats.expired }}</div>
                    </div>
                </div>

                <div class="space-y-4 md:hidden">
                    <div
                        v-if="proposals.length === 0"
                        class="rounded-lg border border-gray-200 bg-white px-4 py-10 text-center text-sm text-gray-500 shadow-sm"
                    >
                        Nenhuma proposta criada ainda.
                    </div>

                    <article
                        v-for="proposal in proposals"
                        :key="proposal.id"
                        class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="break-words text-base font-semibold text-gray-900">
                                    {{ proposal.title }}
                                </h3>
                                <Link :href="route('clients.show', proposal.client.id)" class="mt-1 inline-block text-sm font-medium text-gray-600 hover:text-gray-950">
                                    {{ proposal.client.name }}
                                </Link>
                            </div>
                            <span
                                class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold ring-1"
                                :class="proposal.is_expired ? 'bg-red-50 text-red-700 ring-red-200' : statusClasses[proposal.status]"
                            >
                                {{ proposal.is_expired ? 'Vencida' : statusLabels[proposal.status] }}
                            </span>
                        </div>

                        <div
                            v-if="proposal.is_expired"
                            class="mt-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                        >
                            A validade terminou em {{ proposal.valid_until_display }}. Atualize o status ou ajuste a validade.
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <div class="text-gray-500">Criada em</div>
                                <div class="font-medium text-gray-900">{{ proposal.created_at }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-gray-500">Total</div>
                                <div class="font-semibold text-gray-900">{{ formatMoney(proposal.total) }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Validade</div>
                                <div :class="proposal.is_expired ? 'font-semibold text-red-700' : 'font-medium text-gray-900'">
                                    {{ proposal.valid_until_display || 'Não informada' }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <Link :href="route('proposals.show', proposal.id)" class="inline-flex justify-center rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700">
                                Ver
                            </Link>
                            <Link :href="route('proposals.edit', proposal.id)" class="inline-flex justify-center rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700">
                                {{ proposal.is_expired ? 'Atualizar' : 'Editar' }}
                            </Link>
                            <a :href="proposal.public_url" target="_blank" class="inline-flex justify-center rounded-md border border-blue-200 px-3 py-2 text-sm font-semibold text-blue-700">
                                Público
                            </a>
                            <Link
                                :href="route('proposals.destroy', proposal.id)"
                                method="delete"
                                as="button"
                                class="inline-flex justify-center rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600"
                                @click="(event) => !confirm('Excluir esta proposta?') && event.preventDefault()"
                            >
                                Excluir
                            </Link>
                        </div>
                    </article>
                </div>

                <div class="hidden overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm md:block">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Proposta</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Validade</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Total</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="proposals.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Nenhuma proposta criada ainda.
                                </td>
                            </tr>
                            <tr v-for="proposal in proposals" :key="proposal.id" :class="proposal.is_expired ? 'bg-red-50/40' : ''">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ proposal.title }}</div>
                                    <div class="text-sm text-gray-500">Criada em {{ proposal.created_at }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <Link :href="route('clients.show', proposal.client.id)" class="font-medium text-gray-700 hover:text-gray-950">
                                        {{ proposal.client.name }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold ring-1"
                                        :class="proposal.is_expired ? 'bg-red-50 text-red-700 ring-red-200' : statusClasses[proposal.status]"
                                    >
                                        {{ proposal.is_expired ? 'Vencida' : statusLabels[proposal.status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm" :class="proposal.is_expired ? 'font-semibold text-red-700' : 'text-gray-600'">
                                    {{ proposal.valid_until_display || 'Não informada' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                    {{ formatMoney(proposal.total) }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex justify-end gap-x-4 whitespace-nowrap">
                                    <Link :href="route('proposals.show', proposal.id)" class="font-medium text-gray-700 hover:text-gray-950">
                                        Ver
                                    </Link>
                                    <Link :href="route('proposals.edit', proposal.id)" class="font-medium text-gray-700 hover:text-gray-950">
                                        {{ proposal.is_expired ? 'Atualizar' : 'Editar' }}
                                    </Link>
                                    <a :href="proposal.public_url" target="_blank" class="font-medium text-blue-700 hover:text-blue-900">
                                        Público
                                    </a>
                                    <Link
                                        :href="route('proposals.destroy', proposal.id)"
                                        method="delete"
                                        as="button"
                                        class="font-medium text-red-600 hover:text-red-800"
                                        @click="(event) => !confirm('Excluir esta proposta?') && event.preventDefault()"
                                    >
                                        Excluir
                                    </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
