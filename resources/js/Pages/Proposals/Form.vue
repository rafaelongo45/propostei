<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FormErrorSummary from '@/Components/FormErrorSummary.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    proposal: Object,
    clients: Array,
    statuses: Array,
});

const isEditing = Boolean(props.proposal);
const today = new Date();
today.setHours(0, 0, 0, 0);

const todayBrazil = `${String(today.getDate()).padStart(2, '0')}/${String(today.getMonth() + 1).padStart(2, '0')}/${today.getFullYear()}`;

const formatDateForBrazil = (value) => {
    if (!value) {
        return '';
    }

    const [year, month, day] = String(value).slice(0, 10).split('-');

    if (!year || !month || !day) {
        return value;
    }

    return `${day}/${month}/${year}`;
};

const dateToIso = (value) => {
    const [day, month, year] = value.split('/');

    if (!day || !month || !year) {
        return value;
    }

    return `${year}-${month}-${day}`;
};

const isPastBrazilDate = (value) => {
    const [day, month, year] = value.split('/');

    if (!day || !month || !year || year.length !== 4) {
        return false;
    }

    const date = new Date(Number(year), Number(month) - 1, Number(day));

    if (
        date.getFullYear() !== Number(year) ||
        date.getMonth() !== Number(month) - 1 ||
        date.getDate() !== Number(day)
    ) {
        return false;
    }

    date.setHours(0, 0, 0, 0);

    return date < today;
};

const form = useForm({
    client_id: props.proposal?.client_id ?? props.clients[0]?.id ?? '',
    title: props.proposal?.title ?? '',
    introduction: props.proposal?.introduction ?? '',
    valid_until: formatDateForBrazil(props.proposal?.valid_until ?? ''),
    delivery_estimate: props.proposal?.delivery_estimate ?? '',
    status: props.proposal?.status ?? 'draft',
    internal_notes: props.proposal?.internal_notes ?? '',
    manual_discount: props.proposal?.manual_discount ?? 0,
    items: props.proposal?.items?.length
        ? props.proposal.items.map((item) => ({
              description: item.description,
              quantity: item.quantity,
              unit_price: item.unit_price,
          }))
        : [{ description: '', quantity: 1, unit_price: 0 }],
});

const statusLabels = {
    draft: 'Rascunho',
    sent: 'Enviada',
    accepted: 'Aceita',
    rejected: 'Recusada',
};

const formatMoney = (value) =>
    new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    }).format(value || 0);

const subtotal = computed(() =>
    form.items.reduce(
        (sum, item) => sum + Number(item.quantity || 0) * Number(item.unit_price || 0),
        0,
    ),
);

const total = computed(() => Math.max(0, subtotal.value - Number(form.manual_discount || 0)));

const addItem = () => {
    form.items.push({ description: '', quantity: 1, unit_price: 0 });
};

const removeItem = (index) => {
    if (form.items.length === 1) {
        return;
    }

    form.items.splice(index, 1);
};

const maskDate = (event) => {
    const digits = event.target.value.replace(/\D/g, '').slice(0, 8);

    form.valid_until = digits
        .replace(/^(\d{2})(\d)/, '$1/$2')
        .replace(/^(\d{2})\/(\d{2})(\d)/, '$1/$2/$3');
};

const cleanOptional = (value) => String(value ?? '').trim();
const cleanDecimal = (value) => {
    if (value === null || value === undefined || value === '') {
        return value;
    }

    return String(value).trim().replace(',', '.');
};

const normalizeForm = () => {
    form.title = cleanOptional(form.title);
    form.introduction = cleanOptional(form.introduction);
    form.valid_until = cleanOptional(form.valid_until);
    form.delivery_estimate = cleanOptional(form.delivery_estimate);
    form.internal_notes = cleanOptional(form.internal_notes);
    form.manual_discount = cleanDecimal(form.manual_discount) || 0;
    form.items = form.items.map((item) => ({
        ...item,
        description: cleanOptional(item.description),
        quantity: cleanDecimal(item.quantity),
        unit_price: cleanDecimal(item.unit_price),
    }));
};

const submit = () => {
    normalizeForm();
    form.clearErrors('valid_until');

    if (form.valid_until && isPastBrazilDate(form.valid_until)) {
        form.setError('valid_until', `A validade deve ser hoje ou uma data futura. Hoje é ${todayBrazil}.`);
        return;
    }

    form.transform((data) => ({
        ...data,
        valid_until: data.valid_until ? dateToIso(data.valid_until) : null,
    }));

    if (isEditing) {
        form.put(route('proposals.update', props.proposal.id));
        return;
    }

    form.post(route('proposals.store'));
};
</script>

<template>
    <Head :title="isEditing ? 'Editar proposta' : 'Nova proposta'" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    {{ isEditing ? 'Editar proposta' : 'Nova proposta' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Monte a proposta com cliente, prazo, serviços e valores.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <form class="space-y-6" novalidate @submit.prevent="submit">
                    <FormErrorSummary :errors="form.errors" />

                    <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <InputLabel for="client_id" value="Cliente" />
                                <select
                                    id="client_id"
                                    v-model="form.client_id"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.name }}{{ client.company ? ` - ${client.company}` : '' }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.client_id" />
                            </div>
                            <div>
                                <InputLabel for="status" value="Status" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option v-for="status in statuses" :key="status" :value="status">
                                        {{ statusLabels[status] }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>
                            <div class="sm:col-span-2">
                                <InputLabel for="title" value="Título" />
                                <TextInput id="title" v-model="form.title" class="mt-1 block w-full" required minlength="3" maxlength="255" @blur="form.title = cleanOptional(form.title)" />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>
                            <div class="sm:col-span-2">
                                <InputLabel for="introduction" value="Descrição / introdução" />
                                <textarea
                                    id="introduction"
                                    v-model="form.introduction"
                                    rows="4"
                                    maxlength="4000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @blur="form.introduction = cleanOptional(form.introduction)"
                                />
                                <InputError class="mt-2" :message="form.errors.introduction" />
                            </div>
                            <div>
                                <InputLabel for="valid_until" value="Validade" />
                                <TextInput
                                    id="valid_until"
                                    v-model="form.valid_until"
                                    type="text"
                                    class="mt-1 block w-full"
                                    maxlength="10"
                                    placeholder="dd/mm/aaaa"
                                    inputmode="numeric"
                                    pattern="^(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])/\d{4}$"
                                    :title="`Informe uma data no formato dd/mm/aaaa, igual ou posterior a ${todayBrazil}`"
                                    @input="maskDate"
                                    @blur="form.valid_until = cleanOptional(form.valid_until)"
                                />
                                <InputError class="mt-2" :message="form.errors.valid_until" />
                            </div>
                            <div>
                                <InputLabel for="delivery_estimate" value="Prazo estimado de entrega" />
                                <TextInput id="delivery_estimate" v-model="form.delivery_estimate" class="mt-1 block w-full" maxlength="255" placeholder="Ex.: 15 dias úteis" @blur="form.delivery_estimate = cleanOptional(form.delivery_estimate)" />
                                <InputError class="mt-2" :message="form.errors.delivery_estimate" />
                            </div>
                            <div class="sm:col-span-2">
                                <InputLabel for="internal_notes" value="Observações internas" />
                                <textarea
                                    id="internal_notes"
                                    v-model="form.internal_notes"
                                    rows="3"
                                    maxlength="4000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    @blur="form.internal_notes = cleanOptional(form.internal_notes)"
                                />
                                <InputError class="mt-2" :message="form.errors.internal_notes" />
                            </div>
                        </div>
                    </section>

                    <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">Itens da proposta</h3>
                            <button
                                type="button"
                                class="rounded-md border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                                @click="addItem"
                            >
                                Adicionar item
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div
                                v-for="(item, index) in form.items"
                                :key="index"
                                class="grid gap-3 rounded-md border border-gray-200 p-4 sm:grid-cols-12"
                            >
                                <div class="sm:col-span-6">
                                    <InputLabel :for="`item-description-${index}`" value="Descrição" />
                                    <TextInput :id="`item-description-${index}`" v-model="item.description" class="mt-1 block w-full" required minlength="2" maxlength="255" @blur="item.description = cleanOptional(item.description)" />
                                    <InputError class="mt-2" :message="form.errors[`items.${index}.description`]" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel :for="`item-quantity-${index}`" value="Qtd." />
                                    <TextInput :id="`item-quantity-${index}`" v-model="item.quantity" type="number" min="0.01" max="999999.99" step="0.01" inputmode="decimal" class="mt-1 block w-full" required @blur="item.quantity = cleanDecimal(item.quantity)" />
                                    <InputError class="mt-2" :message="form.errors[`items.${index}.quantity`]" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel :for="`item-price-${index}`" value="Valor unitário" />
                                    <TextInput :id="`item-price-${index}`" v-model="item.unit_price" type="number" min="0" max="9999999999.99" step="0.01" inputmode="decimal" class="mt-1 block w-full" required @blur="item.unit_price = cleanDecimal(item.unit_price)" />
                                    <InputError class="mt-2" :message="form.errors[`items.${index}.unit_price`]" />
                                </div>
                                <div class="sm:col-span-2">
                                    <InputLabel value="Subtotal" />
                                    <div class="mt-1 flex h-10 items-center justify-between rounded-md bg-gray-50 px-3 text-sm font-semibold text-gray-900">
                                        {{ formatMoney(Number(item.quantity || 0) * Number(item.unit_price || 0)) }}
                                        <button
                                            type="button"
                                            class="ml-2 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md text-red-600 transition hover:bg-red-50 hover:text-red-800 disabled:text-gray-300 disabled:hover:bg-transparent"
                                            :disabled="form.items.length === 1"
                                            :aria-label="`Remover item ${index + 1}`"
                                            title="Remover item"
                                            @click="removeItem(index)"
                                        >
                                            <svg
                                                class="h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.8"
                                                stroke="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7m2 0-.7 11.2A2 2 0 0 1 14.3 20H9.7a2 2 0 0 1-2-1.8L7 7m3 4v5m4-5v5" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <InputLabel for="manual_discount" value="Desconto manual opcional" />
                                <TextInput id="manual_discount" v-model="form.manual_discount" type="number" min="0" max="9999999999.99" step="0.01" inputmode="decimal" class="mt-1 block w-full" @blur="form.manual_discount = cleanDecimal(form.manual_discount) || 0" />
                                <InputError class="mt-2" :message="form.errors.manual_discount" />
                            </div>
                            <div class="rounded-md bg-gray-50 p-4">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Subtotal</span>
                                    <span>{{ formatMoney(subtotal) }}</span>
                                </div>
                                <div class="mt-2 flex justify-between text-sm text-gray-600">
                                    <span>Desconto</span>
                                    <span>{{ formatMoney(form.manual_discount) }}</span>
                                </div>
                                <div class="mt-4 flex justify-between border-t border-gray-200 pt-4 text-lg font-semibold text-gray-900">
                                    <span>Total</span>
                                    <span>{{ formatMoney(total) }}</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="grid gap-3 sm:flex sm:items-center sm:justify-end">
                        <Link :href="route('proposals.index')" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            Salvar proposta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
