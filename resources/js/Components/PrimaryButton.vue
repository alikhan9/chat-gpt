<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    type: {
        type: String,
        default: ''
    },
    method: String,
    href: String
})

</script>

<template>
    <button v-if="!type"
        class="px-4 py-2 text-[hsl(0,0%,70%)] hover:text-white border border-[hsl(0,0%,30%)] rounded hover:border-green-500 hover:scale-[1.01]">
        <slot />
    </button>
    <Link :href="href" as="button" :method="method" v-else
        class="px-4 py-2 text-[hsl(0,0%,70%)] hover:text-white border border-[hsl(0,0%,30%)] rounded hover:border-green-500 hover:scale-[1.01]">
    <slot />
    </Link>
</template>
