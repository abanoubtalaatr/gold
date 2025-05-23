<template>
    <div class="p-2 rounded-full" :class="iconContainerClass">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            :stroke-width="1.5">
            <path v-if="type === 'wallet_charge'" stroke-linecap="round" stroke-linejoin="round"
                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            <path v-if="type === 'settlement'" stroke-linecap="round" stroke-linejoin="round"
                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            <path v-if="type === 'admin_adjustment'" stroke-linecap="round" stroke-linejoin="round"
                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
            <path v-if="type === 'other'" stroke-linecap="round" stroke-linejoin="round"
                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
        </svg>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['wallet_charge', 'settlement', 'admin_adjustment', 'other'].includes(value),
    },
});

const iconContainerClass = computed(() => {
    switch (props.type) {
        case 'wallet_charge': return 'bg-blue-100 text-blue-600';
        case 'settlement': return 'bg-green-100 text-green-600';
        case 'admin_adjustment': return 'bg-purple-100 text-purple-600';
        default: return 'bg-gray-100 text-gray-600';
    }
});
</script>
