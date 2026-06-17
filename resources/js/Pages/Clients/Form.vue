<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FormErrorSummary from '@/Components/FormErrorSummary.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    client: Object,
});

const isEditing = Boolean(props.client);
const form = useForm({
    name: props.client?.name ?? '',
    email: props.client?.email ?? '',
    phone: props.client?.phone ?? '',
    company: props.client?.company ?? '',
    notes: props.client?.notes ?? '',
});

const maskPhone = (event) => {
    const digits = event.target.value.replace(/\D/g, '').slice(0, 11);

    if (digits.length <= 10) {
        form.phone = digits
            .replace(/^(\d{2})(\d)/, '($1) $2')
            .replace(/(\d{4})(\d)/, '$1-$2');
        return;
    }

    form.phone = digits
        .replace(/^(\d{2})(\d)/, '($1) $2')
        .replace(/(\d{5})(\d)/, '$1-$2');
};

const cleanOptional = (value) => value?.trim() || '';
const cleanEmail = (value) => cleanOptional(value).toLowerCase();

const normalizeForm = () => {
    form.name = cleanOptional(form.name);
    form.email = cleanEmail(form.email);
    form.phone = cleanOptional(form.phone);
    form.company = cleanOptional(form.company);
    form.notes = cleanOptional(form.notes);
};

const submit = () => {
    normalizeForm();

    if (isEditing) {
        form.put(route('clients.update', props.client.id));
        return;
    }

    form.post(route('clients.store'));
};
</script>

<template>
    <Head :title="isEditing ? 'Editar cliente' : 'Novo cliente'" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-900">
                    {{ isEditing ? 'Editar cliente' : 'Novo cliente' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Dados básicos para propostas comerciais.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <form class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm" novalidate @submit.prevent="submit">
                    <FormErrorSummary class="mb-5" :errors="form.errors" />

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <InputLabel for="name" value="Nome" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                maxlength="255"
                                minlength="2"
                                autocomplete="name"
                                @blur="form.name = cleanOptional(form.name)"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>
                        <div>
                            <InputLabel for="email" value="E-mail" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                maxlength="255"
                                autocomplete="email"
                                inputmode="email"
                                @blur="form.email = cleanEmail(form.email)"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>
                        <div>
                            <InputLabel for="phone" value="Telefone" />
                            <TextInput
                                id="phone"
                                v-model="form.phone"
                                class="mt-1 block w-full"
                                maxlength="15"
                                placeholder="(11) 99999-9999"
                                autocomplete="tel"
                                inputmode="tel"
                                pattern="^\(\d{2}\)\s\d{4,5}-\d{4}$"
                                title="Informe um telefone no formato (11) 99999-9999"
                                @input="maskPhone"
                                @blur="form.phone = cleanOptional(form.phone)"
                            />
                            <InputError class="mt-2" :message="form.errors.phone" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="company" value="Empresa, se houver" />
                            <TextInput id="company" v-model="form.company" class="mt-1 block w-full" maxlength="255" autocomplete="organization" @blur="form.company = cleanOptional(form.company)" />
                            <InputError class="mt-2" :message="form.errors.company" />
                        </div>
                        <div class="sm:col-span-2">
                            <InputLabel for="notes" value="Observações" />
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="4"
                                maxlength="2000"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                @blur="form.notes = cleanOptional(form.notes)"
                            />
                            <InputError class="mt-2" :message="form.errors.notes" />
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 sm:flex sm:items-center sm:justify-end">
                        <Link :href="route('clients.index')" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 disabled:opacity-50"
                            :disabled="form.processing"
                        >
                            Salvar cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
