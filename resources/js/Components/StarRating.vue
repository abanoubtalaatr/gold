<template>
    <div class="flex items-center">
        <div class="flex">
            <template v-for="index in 5" :key="index">
                <StarIcon
                    :class="[
                        'h-5 w-5',
                        index <= Math.round(rating)
                            ? 'text-yellow-400'
                            : 'text-gray-300'
                    ]"
                    :aria-hidden="true"
                />
            </template>
        </div>
        <p class="mr-2 text-sm text-gray-600">{{ rating }}/5</p>
    </div>
</template>

<script setup>
import { StarIcon } from '@heroicons/vue/24/solid'

defineProps({
    rating: {
        type: Number,
        required: true,
        validator: (value) => value >= 0 && value <= 5
    },
    readonly: {
        type: Boolean,
        default: true
    }
})
</script>

<style scoped>
.star-rating {
    display: inline-flex;
    align-items: center;
}
</style>