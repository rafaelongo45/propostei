<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import FormErrorSummary from '@/Components/FormErrorSummary.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});
const page = usePage();

const cleanEmail = (value) => value?.trim().toLowerCase() || '';

const submit = () => {
    form.email = cleanEmail(form.email);

    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Entrar" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>
        <div
            v-if="page.props.flash?.error"
            class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
        >
            {{ page.props.flash.error }}
        </div>

        <a
            :href="route('auth.google.redirect')"
            class="mb-5 flex w-full items-center justify-center gap-3 rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
        >
            <span class="flex h-6 w-6 items-center justify-center rounded-full border border-gray-200 text-sm font-bold text-blue-600">G</span>
            <span>Continuar com Google</span>
        </a>

        <div class="mb-5 flex items-center gap-3">
            <div class="h-px flex-1 bg-gray-200" />
            <span class="text-xs font-medium text-gray-500">ou entre com e-mail e senha</span>
            <div class="h-px flex-1 bg-gray-200" />
        </div>

        <form novalidate @submit.prevent="submit">
            <FormErrorSummary class="mb-4" :errors="form.errors" />

            <div>
                <InputLabel for="email" value="E-mail" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    maxlength="255"
                    autofocus
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
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label for="remember" class="flex items-center">
                    <Checkbox
                        id="remember"
                        name="remember"
                        v-model:checked="form.remember"
                        autocomplete="off"
                    />
                    <span class="ms-2 text-sm text-gray-600">Lembrar de mim</span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Esqueceu sua senha?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Entrar
                </PrimaryButton>
            </div>
        </form>

        <div class="mt-6 border-t border-gray-200 pt-5 text-center">
            <p class="text-sm text-gray-600">Ainda não tem uma conta?</p>
            <Link
                :href="route('register')"
                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                Criar conta grátis
            </Link>
        </div>
    </GuestLayout>
</template>
