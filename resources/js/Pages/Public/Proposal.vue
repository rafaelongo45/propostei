<script setup>
import FormErrorSummary from '@/Components/FormErrorSummary.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    proposal: Object,
});

const page = usePage();
const form = useForm({
    accepted_by_name: props.proposal.accepted_by_name || props.proposal.client.name || '',
    accepted_by_email: props.proposal.accepted_by_email || props.proposal.client.email || '',
    acceptance_notes: props.proposal.acceptance_notes || '',
});
const rejectForm = useForm({
    rejected_by_name: props.proposal.rejected_by_name || props.proposal.client.name || '',
    rejected_by_email: props.proposal.rejected_by_email || props.proposal.client.email || '',
    rejection_reason: props.proposal.rejection_reason || '',
});

const statusLabels = {
    draft: 'Em análise',
    sent: 'Enviada',
    accepted: 'Aceita',
    rejected: 'Recusada',
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

const cleanOptional = (value) => value?.trim() || '';
const cleanEmail = (value) => cleanOptional(value).toLowerCase();

const acceptProposal = () => {
    form.accepted_by_name = cleanOptional(form.accepted_by_name);
    form.accepted_by_email = cleanEmail(form.accepted_by_email);
    form.acceptance_notes = cleanOptional(form.acceptance_notes);

    if (!window.confirm('Confirmar aceite desta proposta?')) {
        return;
    }

    form.post(props.proposal.accept_url, {
        preserveScroll: true,
    });
};

const rejectProposal = () => {
    rejectForm.rejected_by_name = cleanOptional(rejectForm.rejected_by_name);
    rejectForm.rejected_by_email = cleanEmail(rejectForm.rejected_by_email);
    rejectForm.rejection_reason = cleanOptional(rejectForm.rejection_reason);

    if (!window.confirm('Confirmar recusa desta proposta?')) {
        return;
    }

    rejectForm.post(props.proposal.reject_url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="proposal.title" />

    <div class="min-h-screen bg-gray-50">
        <main class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            <div
                v-if="page.props.flash?.success"
                class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
            >
                {{ page.props.flash.success }}
            </div>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 p-5 sm:p-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-gray-500">Proposta comercial</p>
                            <h1 class="mt-2 text-2xl font-semibold text-gray-950 sm:text-3xl">{{ proposal.title }}</h1>
                            <p class="mt-3 max-w-3xl whitespace-pre-line text-gray-600">
                                {{ proposal.introduction }}
                            </p>
                        </div>
                        <span class="inline-flex rounded-full bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700">
                            {{ statusLabels[proposal.status] }}
                        </span>
                    </div>
                </div>

                <div class="grid gap-6 p-5 sm:p-8 lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="overflow-hidden rounded-md border border-gray-200">
                            <div class="overflow-x-auto md:overflow-visible">
                            <table class="min-w-[640px] divide-y divide-gray-200 md:min-w-0 md:w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Serviço</th>
                                        <th class="w-20 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Qtd.</th>
                                        <th class="w-32 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Unitário</th>
                                        <th class="w-32 px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="(item, index) in proposal.items" :key="index">
                                        <td class="px-4 py-4 text-sm text-gray-800">{{ item.description }}</td>
                                        <td class="px-4 py-4 text-right text-sm text-gray-600">{{ item.quantity }}</td>
                                        <td class="px-4 py-4 text-right text-sm text-gray-600">{{ formatMoney(item.unit_price) }}</td>
                                        <td class="px-4 py-4 text-right text-sm font-semibold text-gray-900">{{ formatMoney(item.subtotal) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>

                    <aside class="space-y-5">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                            <div class="text-sm font-semibold text-gray-500">Cliente</div>
                            <div class="mt-2 text-base font-semibold text-gray-950">{{ proposal.client.name }}</div>
                            <div class="text-sm text-gray-600">{{ proposal.client.company }}</div>
                            <div class="text-sm text-gray-600">{{ proposal.client.email }}</div>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Validade</dt>
                                    <dd class="font-medium text-gray-900">{{ formatDate(proposal.valid_until) }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Prazo</dt>
                                    <dd class="font-medium text-gray-900">{{ proposal.delivery_estimate || 'Não informado' }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Subtotal</dt>
                                    <dd class="font-medium text-gray-900">{{ formatMoney(proposal.subtotal) }}</dd>
                                </div>
                                <div v-if="proposal.manual_discount > 0" class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Desconto</dt>
                                    <dd class="font-medium text-gray-900">{{ formatMoney(proposal.manual_discount) }}</dd>
                                </div>
                                <div class="flex justify-between gap-3 border-t border-gray-200 pt-3 text-lg">
                                    <dt class="font-semibold text-gray-950">Total</dt>
                                    <dd class="font-semibold text-gray-950">{{ formatMoney(proposal.total) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div v-if="proposal.payment" class="rounded-lg border border-emerald-200 bg-emerald-50 p-5">
                            <div class="text-sm font-semibold text-emerald-900">Pagamento</div>
                            <dl class="mt-3 space-y-3 text-sm">
                                <div v-if="proposal.payment.recipient_name">
                                    <dt class="text-emerald-700">Favorecido</dt>
                                    <dd class="break-words font-medium text-emerald-950">
                                        {{ proposal.payment.recipient_name }}
                                    </dd>
                                </div>
                                <div v-if="proposal.payment.pix_key">
                                    <dt class="text-emerald-700">
                                        Chave Pix{{ proposal.payment.pix_key_type_label ? ` (${proposal.payment.pix_key_type_label})` : '' }}
                                    </dt>
                                    <dd class="break-words font-medium text-emerald-950">
                                        {{ proposal.payment.pix_key }}
                                    </dd>
                                </div>
                                <div v-if="proposal.payment.link">
                                    <dt class="text-emerald-700">Link de pagamento</dt>
                                    <dd>
                                        <a
                                            :href="proposal.payment.link"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="break-words font-medium text-emerald-800 underline decoration-emerald-300 underline-offset-2 hover:text-emerald-950"
                                        >
                                            {{ proposal.payment.link }}
                                        </a>
                                    </dd>
                                </div>
                            </dl>
                            <p v-if="proposal.payment.instructions" class="mt-4 whitespace-pre-line rounded-md bg-white/70 p-3 text-sm text-emerald-950">
                                {{ proposal.payment.instructions }}
                            </p>
                        </div>

                    </aside>
                </div>

                <section class="border-t border-gray-200 bg-gray-50 p-5 sm:p-8">
                    <div v-if="!['accepted', 'rejected'].includes(proposal.status)">
                        <div class="mb-5">
                            <h2 class="text-lg font-semibold text-gray-950">Decisão sobre a proposta</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Confirme o aceite ou registre uma recusa com observações para o responsável.
                            </p>
                        </div>

                        <div class="grid items-start gap-5 lg:grid-cols-2">
                            <form
                                class="flex h-full flex-col rounded-lg border border-emerald-200 bg-white p-5 shadow-sm"
                                novalidate
                                @submit.prevent="acceptProposal"
                            >
                                <FormErrorSummary class="mb-4" :errors="form.errors" />

                                <div class="flex-1">
                                    <div class="min-h-24 rounded-md bg-emerald-50 px-4 py-3">
                                        <div class="text-base font-semibold text-emerald-900">Aceitar proposta</div>
                                        <p class="mt-1 text-sm leading-5 text-emerald-800">
                                            Informe quem está aprovando para registrar o aceite.
                                        </p>
                                    </div>

                                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="accepted_by_name" class="text-sm font-medium text-gray-700">Nome do responsável</label>
                                            <input
                                                id="accepted_by_name"
                                                v-model="form.accepted_by_name"
                                                type="text"
                                                required
                                                minlength="2"
                                                maxlength="255"
                                                autocomplete="name"
                                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600"
                                                @blur="form.accepted_by_name = cleanOptional(form.accepted_by_name)"
                                            />
                                            <div v-if="form.errors.accepted_by_name" class="mt-2 text-sm text-red-600">
                                                {{ form.errors.accepted_by_name }}
                                            </div>
                                        </div>
                                        <div>
                                            <label for="accepted_by_email" class="text-sm font-medium text-gray-700">E-mail do responsável</label>
                                            <input
                                                id="accepted_by_email"
                                                v-model="form.accepted_by_email"
                                                type="email"
                                                maxlength="255"
                                                autocomplete="email"
                                                inputmode="email"
                                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600"
                                                @blur="form.accepted_by_email = cleanEmail(form.accepted_by_email)"
                                            />
                                            <div v-if="form.errors.accepted_by_email" class="mt-2 text-sm text-red-600">
                                                {{ form.errors.accepted_by_email }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label for="acceptance_notes" class="text-sm font-medium text-gray-700">Observações do aceite</label>
                                        <textarea
                                            id="acceptance_notes"
                                            v-model="form.acceptance_notes"
                                            maxlength="4000"
                                            rows="4"
                                            class="mt-1 block w-full resize-y rounded-md border-gray-300 text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600"
                                            placeholder="Opcional"
                                            @blur="form.acceptance_notes = cleanOptional(form.acceptance_notes)"
                                        />
                                        <div v-if="form.errors.acceptance_notes" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.acceptance_notes }}
                                        </div>
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    class="mt-5 w-full rounded-md bg-emerald-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 disabled:opacity-50"
                                    :disabled="form.processing"
                                >
                                    Aceitar proposta
                                </button>
                            </form>

                            <form
                                class="flex h-full flex-col rounded-lg border border-rose-200 bg-white p-5 shadow-sm"
                                novalidate
                                @submit.prevent="rejectProposal"
                            >
                                <FormErrorSummary class="mb-4" :errors="rejectForm.errors" />

                                <div class="flex-1">
                                    <div class="min-h-24 rounded-md bg-rose-50 px-4 py-3">
                                        <div class="text-base font-semibold text-rose-900">Recusar proposta</div>
                                        <p class="mt-1 text-sm leading-5 text-rose-800">
                                            Registre o motivo da recusa para que o responsável entenda o retorno.
                                        </p>
                                    </div>

                                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label for="rejected_by_name" class="text-sm font-medium text-gray-700">Nome do responsável</label>
                                            <input
                                                id="rejected_by_name"
                                                v-model="rejectForm.rejected_by_name"
                                                type="text"
                                                required
                                                minlength="2"
                                                maxlength="255"
                                                autocomplete="name"
                                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-rose-600 focus:ring-rose-600"
                                                @blur="rejectForm.rejected_by_name = cleanOptional(rejectForm.rejected_by_name)"
                                            />
                                            <div v-if="rejectForm.errors.rejected_by_name" class="mt-2 text-sm text-red-600">
                                                {{ rejectForm.errors.rejected_by_name }}
                                            </div>
                                        </div>
                                        <div>
                                            <label for="rejected_by_email" class="text-sm font-medium text-gray-700">E-mail do responsável</label>
                                            <input
                                                id="rejected_by_email"
                                                v-model="rejectForm.rejected_by_email"
                                                type="email"
                                                maxlength="255"
                                                autocomplete="email"
                                                inputmode="email"
                                                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-rose-600 focus:ring-rose-600"
                                                @blur="rejectForm.rejected_by_email = cleanEmail(rejectForm.rejected_by_email)"
                                            />
                                            <div v-if="rejectForm.errors.rejected_by_email" class="mt-2 text-sm text-red-600">
                                                {{ rejectForm.errors.rejected_by_email }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label for="rejection_reason" class="text-sm font-medium text-gray-700">Motivo da recusa</label>
                                        <textarea
                                            id="rejection_reason"
                                            v-model="rejectForm.rejection_reason"
                                            required
                                            minlength="5"
                                            maxlength="4000"
                                            rows="4"
                                            class="mt-1 block w-full resize-y rounded-md border-gray-300 text-sm shadow-sm focus:border-rose-600 focus:ring-rose-600"
                                            @blur="rejectForm.rejection_reason = cleanOptional(rejectForm.rejection_reason)"
                                        />
                                        <div v-if="rejectForm.errors.rejection_reason" class="mt-2 text-sm text-red-600">
                                            {{ rejectForm.errors.rejection_reason }}
                                        </div>
                                    </div>
                                </div>

                                <button
                                    type="submit"
                                    class="mt-5 w-full rounded-md border border-rose-300 bg-white px-4 py-3 text-sm font-semibold text-rose-700 shadow-sm transition hover:bg-rose-50 disabled:opacity-50"
                                    :disabled="rejectForm.processing"
                                >
                                    Recusar proposta
                                </button>
                            </form>
                        </div>
                    </div>

                    <div
                        v-else-if="proposal.status === 'accepted'"
                        class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 text-emerald-900"
                    >
                        <div class="text-base font-semibold">Proposta aceita</div>
                        <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-3">
                            <div>
                                <dt class="text-emerald-700">Responsável</dt>
                                <dd class="font-medium">{{ proposal.accepted_by_name || 'Não registrado' }}</dd>
                            </div>
                            <div v-if="proposal.accepted_by_email">
                                <dt class="text-emerald-700">E-mail</dt>
                                <dd class="break-words font-medium">{{ proposal.accepted_by_email }}</dd>
                            </div>
                            <div v-if="proposal.accepted_at">
                                <dt class="text-emerald-700">Data do aceite</dt>
                                <dd class="font-medium">{{ proposal.accepted_at }}</dd>
                            </div>
                        </dl>
                        <div v-if="proposal.acceptance_notes" class="mt-4 rounded-md bg-white/70 p-4">
                            <div class="text-sm font-semibold text-emerald-800">Observações do aceite</div>
                            <p class="mt-2 whitespace-pre-line text-sm text-emerald-900">
                                {{ proposal.acceptance_notes }}
                            </p>
                        </div>
                        <a
                            :href="proposal.pdf_url"
                            class="mt-5 inline-flex rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800"
                        >
                            Baixar PDF da proposta
                        </a>
                    </div>

                    <div
                        v-else
                        class="rounded-lg border border-rose-200 bg-rose-50 p-5 text-rose-900"
                    >
                        <div class="text-base font-semibold">Proposta recusada</div>
                        <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-3">
                            <div>
                                <dt class="text-rose-700">Responsável</dt>
                                <dd class="font-medium">{{ proposal.rejected_by_name || 'Não registrado' }}</dd>
                            </div>
                            <div v-if="proposal.rejected_by_email">
                                <dt class="text-rose-700">E-mail</dt>
                                <dd class="break-words font-medium">{{ proposal.rejected_by_email }}</dd>
                            </div>
                            <div v-if="proposal.rejected_at">
                                <dt class="text-rose-700">Data da recusa</dt>
                                <dd class="font-medium">{{ proposal.rejected_at }}</dd>
                            </div>
                        </dl>
                        <div v-if="proposal.rejection_reason" class="mt-4 rounded-md bg-white/70 p-4">
                            <div class="text-sm font-semibold text-rose-800">Motivo da recusa</div>
                            <p class="mt-2 whitespace-pre-line text-sm text-rose-900">
                                {{ proposal.rejection_reason }}
                            </p>
                        </div>
                    </div>
                </section>
            </section>
        </main>
    </div>
</template>
