<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    proposal: Object,
});

const page = usePage();

const statusLabels = {
    draft: 'Rascunho',
    sent: 'Enviada',
    accepted: 'Aceita',
    rejected: 'Recusada',
};

const eventLabels = {
    created: 'Proposta criada',
    updated: 'Proposta atualizada',
    email_queued: 'Envio por e-mail colocado na fila',
    email_failed: 'Falha no envio por e-mail',
    sent_email: 'Proposta enviada por e-mail',
    sent_whatsapp: 'Proposta enviada por WhatsApp',
    link_copied: 'Link público copiado',
    accepted: 'Proposta aceita',
    rejected: 'Proposta recusada',
    pdf_downloaded: 'PDF baixado',
};

const channelLabels = {
    whatsapp: 'WhatsApp',
    email: 'E-mail',
};

const formatMoney = (value) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value || 0);

const formatDate = (value) => {
    if (!value) {
        return 'Não informada';
    }

    const [year, month, day] = value.split('-');

    return day && month && year ? `${day}/${month}/${year}` : value;
};

const copyPublicUrl = async () => {
    await navigator.clipboard.writeText(props.proposal.public_url);
    router.post(props.proposal.link_copied_url, {}, {
        preserveScroll: true,
    });
};

const whatsappUrl = () => {
    const message = `Olá, ${props.proposal.client.name}! Segue o link da proposta comercial: ${props.proposal.public_url}`;
    const phone = (props.proposal.client.phone || '').replace(/\D/g, '');
    const normalizedPhone = phone.length >= 10 && !phone.startsWith('55') ? `55${phone}` : phone;

    return normalizedPhone
        ? `https://wa.me/${normalizedPhone}?text=${encodeURIComponent(message)}`
        : `https://wa.me/?text=${encodeURIComponent(message)}`;
};

const registerSend = (channel) => {
    router.post(props.proposal.send_url, { channel }, {
        preserveScroll: true,
    });
};

const sendWhatsapp = () => {
    window.open(whatsappUrl(), '_blank', 'noopener,noreferrer');
    registerSend('whatsapp');
};

const sendEmail = () => {
    registerSend('email');
};
</script>

<template>
    <Head :title="proposal.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-900">
                        {{ proposal.title }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ proposal.client.name }} · {{ statusLabels[proposal.status] }}
                    </p>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:flex">
                    <Link
                        :href="route('proposals.edit', proposal.id)"
                        class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        Editar
                    </Link>
                    <a
                        :href="proposal.pdf_url"
                        class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        Baixar PDF
                    </a>
                    <!-- <button
                        type="button"
                        class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        @click="sendEmail"
                        hidden
                    >
                        Enviar por e-mail
                    </button> -->
                    <button
                        type="button"
                        class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        @click="sendWhatsapp"
                    >
                        Enviar por WhatsApp
                    </button>
                    <a
                        :href="proposal.public_url"
                        target="_blank"
                        class="inline-flex justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700"
                    >
                        Abrir link público
                    </a>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
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

                <div
                    v-if="proposal.is_expired"
                    class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                >
                    Esta proposta passou da validade em {{ formatDate(proposal.valid_until) }}.
                    <Link :href="route('proposals.edit', proposal.id)" class="font-semibold underline">
                        Atualize o status ou ajuste a validade.
                    </Link>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                        <div class="mb-6">
                            <div class="text-sm font-medium text-gray-500">Introdução</div>
                            <p class="mt-2 whitespace-pre-line text-gray-700">
                                {{ proposal.introduction || 'Sem descrição informada.' }}
                            </p>
                        </div>

                        <div v-if="proposal.internal_notes" class="mb-6 rounded-md border border-amber-200 bg-amber-50 p-4">
                            <div class="text-sm font-semibold text-amber-900">Observações internas</div>
                            <p class="mt-2 whitespace-pre-line text-sm text-amber-900">
                                {{ proposal.internal_notes }}
                            </p>
                        </div>

                        <div class="overflow-hidden rounded-md border border-gray-200">
                            <div class="overflow-x-auto md:overflow-visible">
                            <table class="min-w-[640px] divide-y divide-gray-200 md:min-w-0 md:w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Item</th>
                                        <th class="w-20 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Qtd.</th>
                                        <th class="w-32 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Unitário</th>
                                        <th class="w-32 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="item in proposal.items" :key="item.id">
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ item.description }}</td>
                                        <td class="px-4 py-3 text-right text-sm text-gray-600">{{ item.quantity }}</td>
                                        <td class="px-4 py-3 text-right text-sm text-gray-600">{{ formatMoney(item.unit_price) }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">{{ formatMoney(item.subtotal) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </section>

                    <aside class="space-y-6">
                        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="text-sm font-medium text-gray-500">Resumo</div>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Subtotal</dt>
                                    <dd class="font-medium text-gray-900">{{ formatMoney(proposal.subtotal) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Desconto</dt>
                                    <dd class="font-medium text-gray-900">{{ formatMoney(proposal.manual_discount) }}</dd>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-3 text-base">
                                    <dt class="font-semibold text-gray-900">Total</dt>
                                    <dd class="font-semibold text-gray-900">{{ formatMoney(proposal.total) }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="text-sm font-medium text-gray-500">Dados</div>
                            <dl class="mt-4 space-y-3 text-sm text-gray-700">
                                <div>
                                    <dt class="text-gray-500">Cliente</dt>
                                    <dd class="font-medium text-gray-900">{{ proposal.client.name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Validade</dt>
                                    <dd>{{ formatDate(proposal.valid_until) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Prazo</dt>
                                    <dd>{{ proposal.delivery_estimate || 'Não informado' }}</dd>
                                </div>
                                <div v-if="proposal.sent_at">
                                    <dt class="text-gray-500">Enviada em</dt>
                                    <dd>{{ proposal.sent_at }}</dd>
                                </div>
                                <div v-if="proposal.sent_channel">
                                    <dt class="text-gray-500">Canal</dt>
                                    <dd>{{ channelLabels[proposal.sent_channel] || proposal.sent_channel }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section v-if="proposal.status === 'accepted'" class="rounded-lg border border-emerald-200 bg-emerald-50 p-6 shadow-sm">
                            <div class="text-sm font-semibold text-emerald-900">Aceite da proposta</div>
                            <dl class="mt-4 space-y-3 text-sm text-emerald-900">
                                <div>
                                    <dt class="text-emerald-700">Aceita em</dt>
                                    <dd class="font-medium">{{ proposal.accepted_at || 'Não registrado' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-emerald-700">Responsável</dt>
                                    <dd class="font-medium">{{ proposal.accepted_by_name || 'Não registrado' }}</dd>
                                </div>
                                <div v-if="proposal.accepted_by_email">
                                    <dt class="text-emerald-700">E-mail</dt>
                                    <dd class="font-medium">{{ proposal.accepted_by_email }}</dd>
                                </div>
                                <div v-if="proposal.acceptance_notes">
                                    <dt class="text-emerald-700">Observações do cliente</dt>
                                    <dd class="whitespace-pre-line font-medium">{{ proposal.acceptance_notes }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section v-if="proposal.status === 'rejected'" class="rounded-lg border border-rose-200 bg-rose-50 p-6 shadow-sm">
                            <div class="text-sm font-semibold text-rose-900">Recusa da proposta</div>
                            <dl class="mt-4 space-y-3 text-sm text-rose-900">
                                <div>
                                    <dt class="text-rose-700">Recusada em</dt>
                                    <dd class="font-medium">{{ proposal.rejected_at || 'Não registrado' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-rose-700">Responsável</dt>
                                    <dd class="font-medium">{{ proposal.rejected_by_name || 'Não registrado' }}</dd>
                                </div>
                                <div v-if="proposal.rejected_by_email">
                                    <dt class="text-rose-700">E-mail</dt>
                                    <dd class="font-medium">{{ proposal.rejected_by_email }}</dd>
                                </div>
                                <div v-if="proposal.rejection_reason">
                                    <dt class="text-rose-700">Motivo</dt>
                                    <dd class="whitespace-pre-line font-medium">{{ proposal.rejection_reason }}</dd>
                                </div>
                            </dl>
                        </section>

                        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <div class="text-sm font-medium text-gray-500">Link público</div>
                            <input
                                :value="proposal.public_url"
                                readonly
                                class="mt-3 block w-full rounded-md border-gray-300 text-sm shadow-sm"
                            />
                            <button
                                type="button"
                                class="mt-3 w-full rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                                @click="copyPublicUrl"
                            >
                                Copiar link
                            </button>
                        </section>

                        <section
                            v-if="proposal.events?.length"
                            class="flex max-h-64 flex-col overflow-hidden rounded-lg border border-gray-200 bg-white p-6 shadow-sm"
                        >
                            <div class="text-sm font-medium text-gray-500">Timeline</div>
                            <ol class="mt-4 min-h-0 flex-1 space-y-3 overflow-y-auto pr-2 text-sm text-gray-700">
                                <li v-for="event in proposal.events" :key="event.id" class="border-l border-gray-200 pl-3">
                                    <div class="font-medium text-gray-900">
                                        {{ eventLabels[event.type] || event.type }}
                                    </div>
                                    <div class="text-gray-500">
                                        {{ event.created_at }}
                                    </div>
                                    <div v-if="event.metadata?.message" class="mt-1 break-words text-xs text-rose-700">
                                        {{ event.metadata.message }}
                                    </div>
                                </li>
                            </ol>
                        </section>
                    </aside>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
