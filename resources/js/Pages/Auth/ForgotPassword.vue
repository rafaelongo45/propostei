<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import FormErrorSummary from '@/Components/FormErrorSummary.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const cleanEmail = (value) => value?.trim().toLowerCase() || '';

const submit = () => {
    form.email = cleanEmail(form.email);

    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar senha" />

        <div class="mb-4 text-sm text-gray-600">
            Esqueceu sua senha? Sem problema. Informe seu e-mail e enviaremos
            um link para você criar uma nova senha.
        </div>

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600"
        >
            {{ status }}
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

            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Enviar link de recuperação
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
