<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    client: Object,
    proposals: Array,
});

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
    <Head :title="client.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900">
                        {{ client.name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Propostas e dados comerciais deste cliente.
                    </p>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
                    <Link
                        :href="route('clients.edit', client.id)"
                        class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        Editar cliente
                    </Link>
                    <Link
                        :href="route('proposals.create')"
                        class="inline-flex justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                    >
                        Nova proposta
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-3 lg:px-8">
                <aside class="space-y-6">
                    <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900">Dados do cliente</h3>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div v-if="client.company">
                                <dt class="text-gray-500">Empresa</dt>
                                <dd class="font-medium text-gray-900">{{ client.company }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">E-mail</dt>
                                <dd class="font-medium text-gray-900">{{ client.email || 'Não informado' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Telefone</dt>
                                <dd class="font-medium text-gray-900">{{ client.phone || 'Não informado' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Criado em</dt>
                                <dd class="font-medium text-gray-900">{{ client.created_at }}</dd>
                            </div>
                        </dl>
                    </section>

                    <section v-if="client.notes" class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-900">Observações</h3>
                        <p class="mt-3 whitespace-pre-line text-sm text-gray-700">{{ client.notes }}</p>
                    </section>
                </aside>

                <section class="rounded-lg border border-gray-200 bg-white shadow-sm lg:col-span-2">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Propostas deste cliente</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ proposals.length }} proposta{{ proposals.length === 1 ? '' : 's' }} encontrada{{ proposals.length === 1 ? '' : 's' }}.
                        </p>
                    </div>

                    <div class="space-y-4 p-4 md:hidden">
                        <div
                            v-if="proposals.length === 0"
                            class="px-4 py-8 text-center text-sm text-gray-500"
                        >
                            Nenhuma proposta criada para este cliente.
                        </div>

                        <article
                            v-for="proposal in proposals"
                            :key="proposal.id"
                            class="rounded-lg border border-gray-200 bg-white p-4"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h4 class="break-words text-base font-semibold text-gray-900">
                                        {{ proposal.title }}
                                    </h4>
                                    <div class="mt-1 text-sm text-gray-500">Criada em {{ proposal.created_at }}</div>
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
                                A validade terminou em {{ proposal.valid_until }}.
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <div class="text-gray-500">Validade</div>
                                    <div :class="proposal.is_expired ? 'font-semibold text-red-700' : 'font-medium text-gray-900'">{{ proposal.valid_until || 'Não informada' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-gray-500">Total</div>
                                    <div class="font-semibold text-gray-900">{{ formatMoney(proposal.total) }}</div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-2">
                                <Link :href="route('proposals.show', proposal.id)" class="inline-flex justify-center rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700">
                                    Ver
                                </Link>
                                <Link :href="route('proposals.edit', proposal.id)" class="inline-flex justify-center rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700">
                                    {{ proposal.is_expired ? 'Atualizar' : 'Editar' }}
                                </Link>
                                <a :href="proposal.public_url" target="_blank" class="inline-flex justify-center rounded-md border border-blue-200 px-3 py-2 text-sm font-semibold text-blue-700">
                                    Público
                                </a>
                            </div>
                        </article>
                    </div>

                    <div class="hidden md:block">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Proposta</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Validade</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Total</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-if="proposals.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                        Nenhuma proposta criada para este cliente.
                                    </td>
                                </tr>
                                <tr v-for="proposal in proposals" :key="proposal.id" :class="proposal.is_expired ? 'bg-red-50/40' : ''">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ proposal.title }}</div>
                                        <div class="text-sm text-gray-500">Criada em {{ proposal.created_at }}</div>
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
                                        {{ proposal.valid_until || 'Não informada' }}
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
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
