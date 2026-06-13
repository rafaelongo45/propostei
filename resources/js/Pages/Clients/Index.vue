<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    clients: Array,
});

const page = usePage();
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900">
                        Clientes
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Organize os contatos usados nas propostas.
                    </p>
                </div>
                <Link
                    :href="route('clients.create')"
                    class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                >
                    Novo cliente
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

                <div class="space-y-4 md:hidden">
                    <div
                        v-if="clients.length === 0"
                        class="rounded-lg border border-gray-200 bg-white px-4 py-10 text-center text-sm text-gray-500 shadow-sm"
                    >
                        Nenhum cliente cadastrado ainda.
                    </div>

                    <article
                        v-for="client in clients"
                        :key="client.id"
                        class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
                    >
                        <Link :href="route('clients.show', client.id)" class="break-words text-base font-semibold text-gray-900 hover:text-gray-700">
                            {{ client.name }}
                        </Link>
                        <div class="mt-1 text-sm text-gray-500">{{ client.company || 'Sem empresa' }}</div>

                        <div class="mt-4 space-y-1 text-sm text-gray-600">
                            <div>{{ client.email || 'Sem e-mail' }}</div>
                            <div>{{ client.phone || 'Sem telefone' }}</div>
                            <div>{{ client.proposals_count }} proposta{{ client.proposals_count === 1 ? '' : 's' }}</div>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-2">
                            <Link :href="route('clients.show', client.id)" class="inline-flex justify-center rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700">
                                Propostas
                            </Link>
                            <Link :href="route('clients.edit', client.id)" class="inline-flex justify-center rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700">
                                Editar
                            </Link>
                            <Link
                                :href="route('clients.destroy', client.id)"
                                method="delete"
                                as="button"
                                class="inline-flex justify-center rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600"
                                @click="(event) => !confirm('Remover este cliente?') && event.preventDefault()"
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
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Cliente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Contato
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Propostas
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-if="clients.length === 0">
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Nenhum cliente cadastrado ainda.
                                </td>
                            </tr>
                            <tr v-for="client in clients" :key="client.id">
                                <td class="px-6 py-4">
                                    <Link :href="route('clients.show', client.id)" class="font-medium text-gray-900 hover:text-gray-700">
                                        {{ client.name }}
                                    </Link>
                                    <div class="text-sm text-gray-500">{{ client.company || 'Sem empresa' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div>{{ client.email || 'Sem e-mail' }}</div>
                                    <div>{{ client.phone || 'Sem telefone' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ client.proposals_count }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex justify-end gap-x-4 whitespace-nowrap">
                                    <Link :href="route('clients.show', client.id)" class="font-medium text-gray-700 hover:text-gray-950">
                                        Propostas
                                    </Link>
                                    <Link :href="route('clients.edit', client.id)" class="font-medium text-gray-700 hover:text-gray-950">
                                        Editar
                                    </Link>
                                    <Link
                                        :href="route('clients.destroy', client.id)"
                                        method="delete"
                                        as="button"
                                        class="font-medium text-red-600 hover:text-red-800"
                                        @click="(event) => !confirm('Remover este cliente?') && event.preventDefault()"
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
