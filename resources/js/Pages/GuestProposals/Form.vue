<script setup>
import FormErrorSummary from '@/Components/FormErrorSummary.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    proposal: Object,
});

const isEditing = Boolean(props.proposal);
const today = new Date();
today.setHours(0, 0, 0, 0);

const formatDateForBrazil = (value) => {
    if (!value) return '';
    const [year, month, day] = String(value).slice(0, 10).split('-');
    return day && month && year ? `${day}/${month}/${year}` : value;
};

const dateToIso = (value) => {
    const [day, month, year] = value.split('/');
    return day && month && year ? `${year}-${month}-${day}` : value;
};

const form = useForm({
    client: {
        name: props.proposal?.client?.name ?? '',
        email: props.proposal?.client?.email ?? '',
        phone: props.proposal?.client?.phone ?? '',
        company: props.proposal?.client?.company ?? '',
    },
    title: props.proposal?.title ?? '',
    introduction: props.proposal?.introduction ?? '',
    valid_until: formatDateForBrazil(props.proposal?.valid_until ?? ''),
    delivery_estimate: props.proposal?.delivery_estimate ?? '',
    manual_discount: props.proposal?.manual_discount ?? 0,
    items: props.proposal?.items?.length
        ? props.proposal.items.map((item) => ({ ...item }))
        : [{ description: '', quantity: 1, unit_price: 0 }],
});

const subtotal = computed(() => form.items.reduce(
    (sum, item) => sum + Number(item.quantity || 0) * Number(item.unit_price || 0),
    0,
));
const total = computed(() => Math.max(0, subtotal.value - Number(form.manual_discount || 0)));
const formatMoney = (value) => new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
}).format(value || 0);
const clean = (value) => String(value ?? '').trim();
const cleanDecimal = (value) => String(value ?? '').trim().replace(',', '.');

const maskPhone = (event) => {
    const digits = event.target.value.replace(/\D/g, '').slice(0, 11);
    const area = digits.slice(0, 2);
    const first = digits.length > 10 ? digits.slice(2, 7) : digits.slice(2, 6);
    const last = digits.length > 10 ? digits.slice(7) : digits.slice(6);
    form.client.phone = area ? `(${area}${area.length === 2 ? ') ' : ''}${first}${last ? `-${last}` : ''}` : '';
};

const maskDate = (event) => {
    const digits = event.target.value.replace(/\D/g, '').slice(0, 8);
    form.valid_until = digits
        .replace(/^(\d{2})(\d)/, '$1/$2')
        .replace(/^(\d{2})\/(\d{2})(\d)/, '$1/$2/$3');
};

const addItem = () => form.items.push({ description: '', quantity: 1, unit_price: 0 });
const removeItem = (index) => {
    if (form.items.length > 1) form.items.splice(index, 1);
};

const submit = () => {
    form.client.name = clean(form.client.name);
    form.client.email = clean(form.client.email).toLowerCase();
    form.client.phone = clean(form.client.phone);
    form.client.company = clean(form.client.company);
    form.title = clean(form.title);
    form.introduction = clean(form.introduction);
    form.delivery_estimate = clean(form.delivery_estimate);
    form.manual_discount = cleanDecimal(form.manual_discount) || 0;
    form.items = form.items.map((item) => ({
        description: clean(item.description),
        quantity: cleanDecimal(item.quantity),
        unit_price: cleanDecimal(item.unit_price),
    }));

    form.transform((data) => ({
        ...data,
        valid_until: data.valid_until ? dateToIso(data.valid_until) : null,
    }));

    if (isEditing) {
        form.put(props.proposal.update_url);
    } else {
        form.post(route('guest.proposals.store'));
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Editar proposta grátis' : 'Criar proposta grátis'" />

    <div class="min-h-screen bg-gray-50">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-3">
                    <span class="block h-10 w-10 overflow-hidden rounded-md shadow-sm">
                        <img src="/propostei-logo.png" alt="propostei" class="h-full w-full scale-[2.15] object-cover" />
                    </span>
                    <span class="font-semibold text-gray-950">propostei</span>
                </Link>
                <div class="text-right text-xs text-gray-500 sm:text-sm">
                    Sem cadastro · Sem cartão
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-wide text-teal-700">Proposta grátis</p>
                <h1 class="mt-2 text-2xl font-semibold text-gray-950 sm:text-3xl">
                    {{ isEditing ? 'Ajuste sua proposta' : 'Monte sua proposta antes de se cadastrar' }}
                </h1>
                <p class="mt-2 text-sm leading-6 text-gray-600 sm:text-base">
                    Preencha cliente, serviços e valores. Você verá o resultado pronto e só criará sua conta para salvar, baixar ou enviar.
                </p>
            </div>

            <form class="space-y-6" novalidate @submit.prevent="submit">
                <FormErrorSummary :errors="form.errors" />

                <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-base font-semibold text-gray-950">Cliente</h2>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div>
                            <InputLabel for="client_name" value="Nome do cliente" />
                            <TextInput id="client_name" v-model="form.client.name" class="mt-1 block w-full" required minlength="2" maxlength="255" />
                            <InputError class="mt-2" :message="form.errors['client.name']" />
                        </div>
                        <div>
                            <InputLabel for="client_company" value="Empresa ou identificação" />
                            <TextInput id="client_company" v-model="form.client.company" class="mt-1 block w-full" maxlength="255" placeholder="Opcional" />
                            <InputError class="mt-2" :message="form.errors['client.company']" />
                        </div>
                        <div>
                            <InputLabel for="client_email" value="E-mail" />
                            <TextInput id="client_email" v-model="form.client.email" type="email" class="mt-1 block w-full" maxlength="255" placeholder="Opcional" />
                            <InputError class="mt-2" :message="form.errors['client.email']" />
                        </div>
                        <div>
                            <InputLabel for="client_phone" value="Telefone" />
                            <TextInput id="client_phone" v-model="form.client.phone" class="mt-1 block w-full" maxlength="15" inputmode="numeric" placeholder="(11) 99999-9999" @input="maskPhone" />
                            <InputError class="mt-2" :message="form.errors['client.phone']" />
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <h2 class="text-base font-semibold text-gray-950">Proposta</h2>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <InputLabel for="title" value="Título" />
                            <TextInput id="title" v-model="form.title" class="mt-1 block w-full" required minlength="3" maxlength="255" placeholder="Ex.: Pintura residencial" />
                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="introduction" value="Descrição / introdução" />
                            <textarea id="introduction" v-model="form.introduction" rows="4" maxlength="4000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-600 focus:ring-teal-600" placeholder="Apresente brevemente o serviço e as condições." />
                            <InputError class="mt-2" :message="form.errors.introduction" />
                        </div>
                        <div>
                            <InputLabel for="valid_until" value="Validade" />
                            <TextInput id="valid_until" v-model="form.valid_until" class="mt-1 block w-full" maxlength="10" inputmode="numeric" placeholder="dd/mm/aaaa" @input="maskDate" />
                            <InputError class="mt-2" :message="form.errors.valid_until" />
                        </div>
                        <div>
                            <InputLabel for="delivery_estimate" value="Prazo estimado" />
                            <TextInput id="delivery_estimate" v-model="form.delivery_estimate" class="mt-1 block w-full" maxlength="255" placeholder="Ex.: 10 dias úteis" />
                            <InputError class="mt-2" :message="form.errors.delivery_estimate" />
                        </div>
                    </div>
                </section>

                <section class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-base font-semibold text-gray-950">Serviços e valores</h2>
                        <button type="button" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50" @click="addItem">
                            Adicionar item
                        </button>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div v-for="(item, index) in form.items" :key="index" class="grid gap-4 border-b border-gray-100 pb-4 sm:grid-cols-[1fr_110px_150px_44px] sm:items-start">
                            <div>
                                <InputLabel :for="`item_${index}`" value="Descrição" />
                                <TextInput :id="`item_${index}`" v-model="item.description" class="mt-1 block w-full" required maxlength="255" />
                                <InputError class="mt-2" :message="form.errors[`items.${index}.description`]" />
                            </div>
                            <div>
                                <InputLabel :for="`quantity_${index}`" value="Qtd." />
                                <TextInput :id="`quantity_${index}`" v-model="item.quantity" type="number" min="0.01" step="0.01" class="mt-1 block w-full" required />
                                <InputError class="mt-2" :message="form.errors[`items.${index}.quantity`]" />
                            </div>
                            <div>
                                <InputLabel :for="`price_${index}`" value="Valor unitário" />
                                <TextInput :id="`price_${index}`" v-model="item.unit_price" type="number" min="0" step="0.01" class="mt-1 block w-full" required />
                                <InputError class="mt-2" :message="form.errors[`items.${index}.unit_price`]" />
                            </div>
                            <button type="button" class="mt-6 inline-flex h-10 w-10 items-center justify-center rounded-md border border-red-200 text-red-600 hover:bg-red-50 disabled:opacity-40" :disabled="form.items.length === 1" title="Remover item" @click="removeItem(index)">
                                <span aria-hidden="true">×</span>
                                <span class="sr-only">Remover item</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-4 sm:grid-cols-[1fr_280px] sm:items-end">
                        <div>
                            <InputLabel for="manual_discount" value="Desconto manual" />
                            <TextInput id="manual_discount" v-model="form.manual_discount" type="number" min="0" step="0.01" class="mt-1 block w-full sm:max-w-xs" />
                            <InputError class="mt-2" :message="form.errors.manual_discount" />
                        </div>
                        <dl class="rounded-md bg-gray-50 p-4 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500">Subtotal</dt><dd class="font-medium">{{ formatMoney(subtotal) }}</dd></div>
                            <div class="mt-2 flex justify-between border-t border-gray-200 pt-2 text-base"><dt class="font-semibold">Total</dt><dd class="font-semibold">{{ formatMoney(total) }}</dd></div>
                        </dl>
                    </div>
                </section>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <Link href="/" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">Voltar</Link>
                    <button type="submit" class="inline-flex justify-center rounded-md bg-teal-700 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-800 disabled:opacity-60" :disabled="form.processing">
                        {{ form.processing ? 'Preparando...' : 'Ver minha proposta' }}
                    </button>
                </div>
            </form>
        </main>
    </div>
</template>
