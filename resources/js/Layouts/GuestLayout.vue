<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/">
                <div class="flex items-center gap-3">
                    <span class="block h-14 w-14 overflow-hidden rounded-md shadow-sm">
                        <img
                            src="/propostei-logo.png"
                            alt="propostei"
                            class="h-full w-full scale-[2.15] object-cover"
                        />
                    </span>
                    <span class="text-lg font-semibold text-gray-950">propostei</span>
                </div>
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
