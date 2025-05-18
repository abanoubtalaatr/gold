<template>
  <div class="relative">
    <input
      type="time"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="w-32 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      :class="{ 'border-red-500': error }"
      :placeholder="placeholder"
      step="300"
    />
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: '',
  },
  error: {
    type: [String, Boolean],
    default: false,
  },
})

defineEmits(['update:modelValue'])
</script>

<style>
/* Hide browser's default time picker styling */
input[type="time"]::-webkit-calendar-picker-indicator {
  background: none;
  cursor: pointer;
  padding: 0;
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  width: 20px;
}

input[type="time"] {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
}

/* Show custom time picker icon */
input[type="time"]::-webkit-calendar-picker-indicator {
  opacity: 1;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'%3E%3C/path%3E%3C/svg%3E");
  background-position: center;
  background-repeat: no-repeat;
  background-size: contain;
}
</style> 