<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    proposal: Object,
});

const formatMoney = (value) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value || 0);

const formatDate = (value) => {
    if (!value) {
        return 'Não informada';
    }

    const [year, month, day] = String(value).split('-');

    return day && month && year ? `${day}/${month}/${year}` : value;
};
</script>

<template>
    <Head title="Exemplo de proposta comercial" />

    <div class="min-h-screen bg-gray-50">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-3">
                    <span class="block h-10 w-10 overflow-hidden rounded-md shadow-sm">
                        <img src="/propostei-logo.png" alt="propostei" class="h-full w-full scale-[2.15] object-cover" />
                    </span>
                    <span class="text-sm font-semibold text-gray-950">propostei</span>
                </Link>
                <Link
                    :href="route('start')"
                    class="rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800"
                >
                    Criar uma proposta como esta
                </Link>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            <div class="mb-4 rounded-md border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-800">
                Esta é uma proposta de demonstração. Os botões de aceite e recusa são apenas visuais.
            </div>
            <div class="mb-4 flex flex-col gap-3 rounded-md border border-gray-200 bg-white px-4 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-950">PDF da proposta</div>
                    <p class="mt-1 text-sm text-gray-600">
                        Veja como uma cópia em PDF pode ser enviada ou arquivada.
                    </p>
                </div>
                <a
                    :href="proposal.pdf_url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                >
                    Visualizar PDF
                </a>
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
                        <span class="inline-flex rounded-full bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-700 ring-1 ring-teal-200">
                            {{ proposal.status }}
                        </span>
                    </div>
                </div>

                <div class="grid gap-6 p-5 sm:p-8 lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="overflow-hidden rounded-md border border-gray-200">
                            <div class="overflow-x-auto md:overflow-visible">
                                <table class="min-w-[640px] divide-y divide-gray-200 md:w-full md:min-w-0">
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
                            <div v-if="proposal.client.company" class="text-sm text-gray-600">{{ proposal.client.company }}</div>
                            <div v-if="proposal.client.email" class="text-sm text-gray-600">{{ proposal.client.email }}</div>
                            <div v-if="proposal.client.phone" class="text-sm text-gray-600">{{ proposal.client.phone }}</div>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Validade</dt>
                                    <dd class="font-medium text-gray-900">{{ formatDate(proposal.valid_until) }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500">Prazo</dt>
                                    <dd class="font-medium text-gray-900">{{ proposal.delivery_estimate }}</dd>
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
                                    <dd class="break-words font-medium text-emerald-950">{{ proposal.payment.recipient_name }}</dd>
                                </div>
                                <div v-if="proposal.payment.pix_key">
                                    <dt class="text-emerald-700">
                                        Chave Pix{{ proposal.payment.pix_key_type_label ? ` (${proposal.payment.pix_key_type_label})` : '' }}
                                    </dt>
                                    <dd class="break-words font-medium text-emerald-950">{{ proposal.payment.pix_key }}</dd>
                                </div>
                            </dl>
                            <p v-if="proposal.payment.instructions" class="mt-4 whitespace-pre-line rounded-md bg-white/70 p-3 text-sm text-emerald-950">
                                {{ proposal.payment.instructions }}
                            </p>
                        </div>
                    </aside>
                </div>

                <section class="border-t border-gray-200 bg-gray-50 p-5 sm:p-8">
                    <div class="mb-5">
                        <h2 class="text-lg font-semibold text-gray-950">Decisão sobre a proposta</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Na proposta real, o cliente pode aceitar ou recusar e o responsável acompanha tudo no painel.
                        </p>
                    </div>

                    <div class="grid items-stretch gap-5 lg:grid-cols-2">
                        <div class="flex h-full flex-col rounded-lg border border-emerald-200 bg-white p-5 shadow-sm">
                            <div class="flex-1 rounded-md bg-emerald-50 px-4 py-3">
                                <div class="text-base font-semibold text-emerald-900">Aceitar proposta</div>
                                <p class="mt-1 text-sm leading-5 text-emerald-800">
                                    A ação real registra nome, e-mail, data e observações.
                                </p>
                            </div>
                            <button
                                type="button"
                                disabled
                                class="mt-5 w-full rounded-md bg-emerald-700 px-4 py-3 text-sm font-semibold text-white opacity-70"
                            >
                                Aceitar proposta
                            </button>
                        </div>

                        <div class="flex h-full flex-col rounded-lg border border-rose-200 bg-white p-5 shadow-sm">
                            <div class="flex-1 rounded-md bg-rose-50 px-4 py-3">
                                <div class="text-base font-semibold text-rose-900">Recusar proposta</div>
                                <p class="mt-1 text-sm leading-5 text-rose-800">
                                    A recusa real permite informar o motivo para o prestador analisar.
                                </p>
                            </div>
                            <button
                                type="button"
                                disabled
                                class="mt-5 w-full rounded-md border border-rose-300 bg-white px-4 py-3 text-sm font-semibold text-rose-700 opacity-70"
                            >
                                Recusar proposta
                            </button>
                        </div>
                    </div>
                </section>
            </section>

            <section class="mt-6 rounded-lg border border-slate-200 bg-slate-950 p-6 text-white shadow-sm sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold">Quer enviar uma proposta como esta?</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-300">
                            Comece grátis, cadastre um cliente e gere sua primeira proposta por link ou PDF.
                        </p>
                    </div>
                    <Link
                        :href="route('start')"
                        class="inline-flex justify-center rounded-md bg-white px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100"
                    >
                        Criar uma proposta como esta
                    </Link>
                    <a
                        :href="proposal.pdf_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex justify-center rounded-md border border-white/30 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10"
                    >
                        Ver PDF exemplo
                    </a>
                </div>
            </section>
        </main>
    </div>
</template>
