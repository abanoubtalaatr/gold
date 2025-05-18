<template>
  <textarea
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    :class="[
      'border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full',
      error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '',
    ]"
  ></textarea>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    required: true
  },
  error: {
    type: [String, undefined],
    default: undefined
  }
})

defineEmits(['update:modelValue'])
</script> 