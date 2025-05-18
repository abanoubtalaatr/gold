<template>
  <div class="relative">
    <select
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :class="[
        'block w-full px-3 py-2.5 border rounded-lg shadow-sm text-base transition duration-150 ease-in-out',
        'bg-white dark:bg-gray-800 dark:text-gray-200',
        error
          ? 'border-red-500 focus:border-red-500 focus:ring-red-500 dark:border-red-400'
          : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600',
        'focus:outline-none focus:ring-2 focus:ring-opacity-50',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        'hover:border-gray-400 dark:hover:border-gray-500'
      ]"
    >
      <option value="" disabled selected class="text-gray-500">
        {{ $t('Please select an option') }}
      </option>
      <template v-if="typeof options === 'object' && !Array.isArray(options)">
        <option
          v-for="(label, value) in options"
          :key="value"
          :value="value"
          class="text-gray-900 dark:text-gray-200"
        >
          {{ label }}
        </option>
      </template>
      <template v-else>
        <option
          v-for="option in options"
          :key="option.value"
          :value="option.value"
          class="text-gray-900 dark:text-gray-200"
        >
          {{ option.label }}
        </option>
      </template>
    </select>
    
    <!-- Custom Arrow -->
    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
      <svg
        class="w-5 h-5 text-gray-400"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z"
          clip-rule="evenodd"
        />
      </svg>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    required: true
  },
  options: {
    type: [Object, Array],
    required: true
  },
  error: {
    type: [String, undefined],
    default: undefined
  }
})

defineEmits(['update:modelValue'])
</script>

<style scoped>
select {
  -webkit-appearance: none;
  -moz-appearance: none;
  text-indent: 1px;
  text-overflow: '';
}

/* Remove default arrow in IE */
select::-ms-expand {
  display: none;
}
</style> 