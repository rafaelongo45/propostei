<script setup>
import FormErrorSummary from '@/Components/FormErrorSummary.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const pixKeyTypes = {
    cpf: 'CPF',
    cnpj: 'CNPJ',
    email: 'E-mail',
    phone: 'Telefone',
    random: 'Chave aleatória',
};

const form = useForm({
    _method: 'patch',
    name: user.name,
    email: user.email,
    company_name: user.company_name || '',
    company_logo: null,
    remove_company_logo: false,
    payment_pix_key: user.payment_pix_key || '',
    payment_pix_key_type: user.payment_pix_key_type || '',
    payment_recipient_name: user.payment_recipient_name || '',
    payment_link: user.payment_link || '',
    payment_instructions: user.payment_instructions || '',
});

const cleanOptional = (value) => value?.trim() || '';
const cleanEmail = (value) => cleanOptional(value).toLowerCase();

const submit = () => {
    form.name = cleanOptional(form.name);
    form.email = cleanEmail(form.email);
    form.company_name = cleanOptional(form.company_name);
    form.payment_pix_key = cleanOptional(form.payment_pix_key);
    form.payment_pix_key_type = cleanOptional(form.payment_pix_key_type);
    form.payment_recipient_name = cleanOptional(form.payment_recipient_name);
    form.payment_link = cleanOptional(form.payment_link);
    form.payment_instructions = cleanOptional(form.payment_instructions);

    form.post(route('profile.update'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.company_logo = null;
            form.remove_company_logo = false;
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Dados do perfil
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Atualize o nome e o e-mail usados na sua conta.
            </p>
        </header>

        <form novalidate @submit.prevent="submit" class="mt-6 space-y-6">
            <FormErrorSummary :errors="form.errors" />

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

            <div>
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

            <div>
                <InputLabel for="company_name" value="Nome da empresa ou marca" />

                <TextInput
                    id="company_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.company_name"
                    maxlength="255"
                    autocomplete="organization"
                    placeholder="Ex.: Longo Studio"
                    @blur="form.company_name = cleanOptional(form.company_name)"
                />

                <InputError class="mt-2" :message="form.errors.company_name" />
            </div>

            <div>
                <InputLabel for="company_logo" value="Logo da empresa" />

                <div v-if="user.company_logo_url && !form.remove_company_logo" class="mt-3 flex items-center gap-4">
                    <img
                        :src="user.company_logo_url"
                        alt="Logo atual"
                        class="h-16 w-16 rounded-md border border-gray-200 object-contain p-2"
                    />
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input
                            v-model="form.remove_company_logo"
                            type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        />
                        Remover logo atual
                    </label>
                </div>

                <input
                    id="company_logo"
                    type="file"
                    class="mt-3 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700"
                    accept="image/png,image/jpeg,image/webp"
                    @input="form.company_logo = $event.target.files[0]"
                />

                <p class="mt-2 text-sm text-gray-500">
                    PNG, JPG ou WebP até 2 MB. O logo aparece no PDF da proposta.
                </p>

                <InputError class="mt-2" :message="form.errors.company_logo" />
                <InputError class="mt-2" :message="form.errors.remove_company_logo" />
            </div>

            <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                <h3 class="text-base font-semibold text-gray-900">Dados de pagamento</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Estes dados aparecerão nas propostas públicas e nos PDFs enviados aos clientes, apenas quando preenchidos.
                </p>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <InputLabel for="payment_pix_key_type" value="Tipo da chave Pix" />

                        <select
                            id="payment_pix_key_type"
                            v-model="form.payment_pix_key_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">Selecione, se usar Pix</option>
                            <option v-for="(label, value) in pixKeyTypes" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>

                        <InputError class="mt-2" :message="form.errors.payment_pix_key_type" />
                    </div>

                    <div>
                        <InputLabel for="payment_pix_key" value="Chave Pix" />

                        <TextInput
                            id="payment_pix_key"
                            v-model="form.payment_pix_key"
                            type="text"
                            class="mt-1 block w-full"
                            maxlength="255"
                            placeholder="CPF, CNPJ, e-mail, telefone ou chave aleatória"
                            @blur="form.payment_pix_key = cleanOptional(form.payment_pix_key)"
                        />

                        <InputError class="mt-2" :message="form.errors.payment_pix_key" />
                    </div>

                    <div>
                        <InputLabel for="payment_recipient_name" value="Nome do favorecido" />

                        <TextInput
                            id="payment_recipient_name"
                            v-model="form.payment_recipient_name"
                            type="text"
                            class="mt-1 block w-full"
                            maxlength="255"
                            placeholder="Nome que aparecerá para pagamento"
                            @blur="form.payment_recipient_name = cleanOptional(form.payment_recipient_name)"
                        />

                        <InputError class="mt-2" :message="form.errors.payment_recipient_name" />
                    </div>

                    <div>
                        <InputLabel for="payment_link" value="Link de pagamento externo" />

                        <TextInput
                            id="payment_link"
                            v-model="form.payment_link"
                            type="url"
                            class="mt-1 block w-full"
                            maxlength="2048"
                            placeholder="https://..."
                            inputmode="url"
                            @blur="form.payment_link = cleanOptional(form.payment_link)"
                        />

                        <InputError class="mt-2" :message="form.errors.payment_link" />
                    </div>

                    <div class="sm:col-span-2">
                        <InputLabel for="payment_instructions" value="Instruções de pagamento" />

                        <textarea
                            id="payment_instructions"
                            v-model="form.payment_instructions"
                            rows="4"
                            maxlength="2000"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ex.: Após o aceite, realize o pagamento de 50% para início do projeto."
                            @blur="form.payment_instructions = cleanOptional(form.payment_instructions)"
                        />

                        <InputError class="mt-2" :message="form.errors.payment_instructions" />
                    </div>
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Seu e-mail ainda não foi verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Clique aqui para reenviar o e-mail de verificação.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    Um novo link de verificação foi enviado para seu e-mail.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Salvar</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Salvo.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
