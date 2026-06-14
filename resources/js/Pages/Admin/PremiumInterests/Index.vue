<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    interests: Array,
});

const statusLabels = {
    interested: 'Interessado',
    contacted: 'Contatado',
    converted: 'Convertido',
    dismissed: 'Dispensado',
};
</script>

<template>
    <Head title="Interessados no Premium" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    Interessados no Premium
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Usuários que manifestaram interesse no plano Premium.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                    <table class="min-w-[820px] w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">E-mail</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Propostas</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Origem</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="interests.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Nenhum interessado registrado ainda.
                                </td>
                            </tr>
                            <tr v-for="interest in interests" :key="interest.id">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ interest.name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ interest.email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ interest.current_proposals_count }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ interest.source }}</td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700 ring-1 ring-teal-200">
                                        {{ statusLabels[interest.status] || interest.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ interest.created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
