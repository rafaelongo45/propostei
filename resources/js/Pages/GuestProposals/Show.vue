<script setup>
import Modal from '@/Components/Modal.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ proposal: Object });

const page = usePage();
const showingGate = ref(false);
const formatMoney = (value) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value || 0);
const formatDate = (value) => {
    if (!value) return 'Não informada';
    const [year, month, day] = String(value).split('-');
    return day && month && year ? `${day}/${month}/${year}` : value;
};
</script>

<template>
    <Head :title="proposal.title" />

    <div class="min-h-screen bg-gray-50">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-3">
                    <span class="block h-10 w-10 overflow-hidden rounded-md shadow-sm"><img src="/propostei-logo.png" alt="propostei" class="h-full w-full scale-[2.15] object-cover" /></span>
                    <span class="font-semibold text-gray-950">propostei</span>
                </Link>
                <Link :href="proposal.edit_url" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Editar proposta</Link>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            <div v-if="page.props.flash?.success" class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ page.props.flash.success }}</div>
            <div class="mb-5 rounded-lg border border-teal-200 bg-teal-50 p-5 text-teal-950">
                <div class="text-base font-semibold">Sua proposta está pronta.</div>
                <p class="mt-1 text-sm leading-6 text-teal-800">Confira o resultado. Crie sua conta grátis apenas quando quiser baixar, salvar ou enviar ao cliente.</p>
            </div>

            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 p-5 sm:p-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div><p class="text-sm font-semibold uppercase tracking-wide text-gray-500">Proposta comercial</p><h1 class="mt-2 text-2xl font-semibold text-gray-950 sm:text-3xl">{{ proposal.title }}</h1><p class="mt-3 max-w-3xl whitespace-pre-line text-gray-600">{{ proposal.introduction }}</p></div>
                        <span class="inline-flex rounded-full bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700">Rascunho</span>
                    </div>
                </div>

                <div class="grid gap-6 p-5 sm:p-8 lg:grid-cols-3">
                    <div class="min-w-0 lg:col-span-2">
                        <div class="overflow-hidden rounded-md border border-gray-200"><div class="overflow-x-auto"><table class="min-w-[640px] divide-y divide-gray-200 md:w-full md:min-w-0"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Serviço</th><th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Qtd.</th><th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Unitário</th><th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Subtotal</th></tr></thead><tbody class="divide-y divide-gray-100"><tr v-for="(item, index) in proposal.items" :key="index"><td class="px-4 py-4 text-sm text-gray-800">{{ item.description }}</td><td class="px-4 py-4 text-right text-sm text-gray-600">{{ item.quantity }}</td><td class="px-4 py-4 text-right text-sm text-gray-600">{{ formatMoney(item.unit_price) }}</td><td class="px-4 py-4 text-right text-sm font-semibold">{{ formatMoney(Number(item.quantity) * Number(item.unit_price)) }}</td></tr></tbody></table></div></div>
                    </div>
                    <aside class="space-y-5">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5"><div class="text-sm font-semibold text-gray-500">Cliente</div><div class="mt-2 font-semibold text-gray-950">{{ proposal.client.name }}</div><div v-if="proposal.client.company" class="text-sm text-gray-600">{{ proposal.client.company }}</div><div v-if="proposal.client.email" class="text-sm text-gray-600">{{ proposal.client.email }}</div><div v-if="proposal.client.phone" class="text-sm text-gray-600">{{ proposal.client.phone }}</div></div>
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-5"><dl class="space-y-3 text-sm"><div class="flex justify-between gap-3"><dt class="text-gray-500">Validade</dt><dd class="font-medium">{{ formatDate(proposal.valid_until) }}</dd></div><div class="flex justify-between gap-3"><dt class="text-gray-500">Prazo</dt><dd class="font-medium">{{ proposal.delivery_estimate || 'Não informado' }}</dd></div><div class="flex justify-between gap-3"><dt class="text-gray-500">Subtotal</dt><dd class="font-medium">{{ formatMoney(proposal.subtotal) }}</dd></div><div v-if="proposal.manual_discount > 0" class="flex justify-between gap-3"><dt class="text-gray-500">Desconto</dt><dd class="font-medium">{{ formatMoney(proposal.manual_discount) }}</dd></div><div class="flex justify-between border-t border-gray-200 pt-3 text-lg"><dt class="font-semibold">Total</dt><dd class="font-semibold">{{ formatMoney(proposal.total) }}</dd></div></dl></div>
                    </aside>
                </div>

                <div class="border-t border-gray-200 bg-gray-50 p-5 sm:p-8">
                    <h2 class="text-lg font-semibold text-gray-950">Próximo passo</h2>
                    <p class="mt-1 text-sm text-gray-600">Cadastre-se para salvar esta proposta na sua conta e liberar PDF e envio.</p>
                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                        <button type="button" class="rounded-md bg-teal-700 px-4 py-3 text-sm font-semibold text-white hover:bg-teal-800" @click="showingGate = true">Baixar PDF</button>
                        <button type="button" class="rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="showingGate = true">Enviar por WhatsApp</button>
                        <button type="button" class="rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="showingGate = true">Salvar na conta</button>
                    </div>
                </div>
            </section>
        </main>

        <Modal :show="showingGate" max-width="md" @close="showingGate = false">
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-950">Sua proposta está pronta</h2>
                <p class="mt-3 text-sm leading-6 text-gray-600">Crie uma conta grátis para baixar o PDF, salvar sua proposta e enviar ao cliente. Seus dados já estão guardados.</p>
                <div class="mt-6 grid gap-3 sm:grid-cols-2"><Link :href="proposal.register_url" class="inline-flex justify-center rounded-md bg-teal-700 px-4 py-3 text-sm font-semibold text-white hover:bg-teal-800">Cadastrar grátis</Link><Link :href="proposal.login_url" class="inline-flex justify-center rounded-md border border-gray-300 px-4 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Entrar</Link></div>
                <button type="button" class="mt-4 w-full text-sm font-medium text-gray-500 hover:text-gray-800" @click="showingGate = false">Continuar revisando</button>
            </div>
        </Modal>
    </div>
</template>
