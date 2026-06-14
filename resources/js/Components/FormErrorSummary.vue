<script setup>
import { computed } from 'vue';

const props = defineProps({
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const messages = computed(() => Object.values(props.errors).filter(Boolean));
</script>

<template>
    <div
        v-if="messages.length"
        class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
        role="alert"
        aria-live="polite"
    >
        <p class="font-semibold">Revise os campos destacados antes de continuar.</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            <li v-for="message in messages" :key="message">
                {{ message }}
            </li>
        </ul>
    </div>
</template>
