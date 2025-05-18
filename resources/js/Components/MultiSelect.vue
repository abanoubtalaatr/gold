<template>
  <div class="relative">
    <div
      class="min-h-[42px] w-full px-4 py-2 border rounded-lg focus-within:ring-2 focus-within:ring-blue-500 cursor-text"
      :class="{ 'border-red-500': error }"
      @click="open = true"
    >
      <!-- Selected Items -->
      <div class="flex flex-wrap gap-2">
        <div
          v-for="value in selectedValues"
          :key="value"
          class="flex items-center gap-1 px-2 py-1 text-sm bg-blue-100 text-blue-800 rounded"
        >
          <span>{{ getOptionLabel(value) }}</span>
          <button
            type="button"
            class="text-blue-600 hover:text-blue-800"
            @click.stop="removeValue(value)"
          >
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
        <input
          type="text"
          class="flex-1 min-w-[60px] bg-transparent outline-none placeholder-gray-400"
          :placeholder="placeholder"
          v-model="search"
          @focus="open = true"
        />
      </div>
    </div>

    <!-- Dropdown -->
    <div
      v-if="open"
      class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg"
    >
      <div class="max-h-60 overflow-y-auto">
        <div
          v-for="option in filteredOptions"
          :key="option.value"
          class="px-4 py-2 cursor-pointer hover:bg-gray-100"
          @click.stop="toggleOption(option.value)"
        >
          <div class="flex items-center">
            <input
              type="checkbox"
              :checked="isSelected(option.value)"
              class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            />
            <span class="ml-2">{{ option.label }}</span>
          </div>
        </div>
        <div
          v-if="filteredOptions.length === 0"
          class="px-4 py-2 text-sm text-gray-500"
        >
          {{ $t('No options found') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
  options: {
    type: Array,
    default: () => [],
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

const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const search = ref('')
const selectedValues = ref(props.modelValue)

const filteredOptions = computed(() => {
  return props.options.filter(option => {
    const label = option.label.toLowerCase()
    const searchTerm = search.value.toLowerCase()
    return label.includes(searchTerm)
  })
})

const getOptionLabel = (value) => {
  const option = props.options.find(opt => opt.value === value)
  return option ? option.label : value
}

const isSelected = (value) => {
  return selectedValues.value.includes(value)
}

const toggleOption = (value) => {
  const index = selectedValues.value.indexOf(value)
  if (index === -1) {
    selectedValues.value.push(value)
  } else {
    selectedValues.value.splice(index, 1)
  }
  emit('update:modelValue', selectedValues.value)
}

const removeValue = (value) => {
  const index = selectedValues.value.indexOf(value)
  if (index !== -1) {
    selectedValues.value.splice(index, 1)
    emit('update:modelValue', selectedValues.value)
  }
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    open.value = false
    search.value = ''
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script> 