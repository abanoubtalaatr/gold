<template>
    <div class="trend-indicator" :class="trendClass">
        <component :is="trendIcon" class="trend-icon" :class="iconClass" />
        <span class="trend-value">{{ formattedValue }}</span>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { ArrowUpIcon, ArrowDownIcon, MinusIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    value: {
        type: Number,
        default: 0,
    },
    showZero: {
        type: Boolean,
        default: true,
    },
});

const trendClass = computed(() => {
    if (props.value > 0) return "trend-up";
    if (props.value < 0) return "trend-down";
    return "trend-neutral";
});

const trendIcon = computed(() => {
    if (props.value > 0) return ArrowUpIcon;
    if (props.value < 0) return ArrowDownIcon;
    return MinusIcon;
});

const iconClass = computed(() => {
    if (props.value > 0) return "text-green-500";
    if (props.value < 0) return "text-red-500";
    return "text-gray-400";
});

const formattedValue = computed(() => {
    if (props.value === 0 && !props.showZero) return "";
    const absValue = Math.abs(props.value);
    return `${absValue}%`;
});
</script>

<style scoped>
.trend-indicator {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.trend-icon {
    width: 1rem;
    height: 1rem;
    margin-right: 0.25rem;
}

.trend-up {
    background-color: rgb(34 197 94 / 0.1);
    color: rgb(34 197 94);
}

.trend-down {
    background-color: rgb(239 68 68 / 0.1);
    color: rgb(239 68 68);
}

.trend-neutral {
    background-color: rgb(156 163 175 / 0.1);
    color: rgb(156 163 175);
}

.trend-value {
    font-size: 0.75rem;
}
</style>
