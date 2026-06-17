<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import FormErrorSummary from '@/Components/FormErrorSummary.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const cleanOptional = (value) => value?.trim() || '';
const cleanEmail = (value) => cleanOptional(value).toLowerCase();

const submit = () => {
    form.name = cleanOptional(form.name);
    form.email = cleanEmail(form.email);

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Criar conta" />

        <a
            :href="route('auth.google.redirect')"
            class="mb-5 flex w-full items-center justify-center gap-3 rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
        >
            <span class="flex h-6 w-6 items-center justify-center rounded-full border border-gray-200 text-sm font-bold text-blue-600">G</span>
            <span>Continuar com Google</span>
        </a>

        <div class="mb-5 flex items-center gap-3">
            <div class="h-px flex-1 bg-gray-200" />
            <span class="text-xs font-medium text-gray-500">ou crie sua conta com e-mail e senha</span>
            <div class="h-px flex-1 bg-gray-200" />
        </div>

        <form novalidate @submit.prevent="submit">
            <FormErrorSummary class="mb-4" :errors="form.errors" />

            <div>
                <InputLabel for="name" value="Nome" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    minlength="2"
                    maxlength="255"
                    autofocus
                    autocomplete="name"
                    @blur="form.name = cleanOptional(form.name)"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="E-mail" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    maxlength="255"
                    autocomplete="username"
                    inputmode="email"
                    @blur="form.email = cleanEmail(form.email)"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Senha" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    minlength="8"
                    maxlength="255"
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar senha"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    minlength="8"
                    maxlength="255"
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Já tem uma conta?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Criar conta
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
