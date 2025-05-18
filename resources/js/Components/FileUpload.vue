<template>
  <div>
    <div
      class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg"
      :class="{ 'border-red-500': error }"
      @dragover.prevent="dragover = true"
      @dragleave.prevent="dragover = false"
      @drop.prevent="handleDrop"
    >
      <div class="space-y-1 text-center">
        <svg
          class="mx-auto h-12 w-12 text-gray-400"
          stroke="currentColor"
          fill="none"
          viewBox="0 0 48 48"
          aria-hidden="true"
        >
          <path
            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
        <div class="flex text-sm text-gray-600">
          <label
            class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500"
          >
            <span>{{ $t('Upload files') }}</span>
            <input
              type="file"
              class="sr-only"
              :multiple="multiple"
              :accept="accept"
              @change="handleFileSelect"
            />
          </label>
          <p class="pl-1">{{ $t('or drag and drop') }}</p>
        </div>
        <p class="text-xs text-gray-500">
          {{ acceptText }}
        </p>
      </div>
    </div>

    <!-- Preview -->
    <div v-if="previewUrls.length > 0" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
      <div
        v-for="(url, index) in previewUrls"
        :key="index"
        class="relative group aspect-w-1 aspect-h-1"
      >
        <img
          :src="url"
          class="object-cover rounded-lg"
          :alt="$t('Preview')"
        />
        <button
          type="button"
          class="absolute top-0 right-0 p-1 m-1 text-white bg-red-500 rounded-full opacity-0 group-hover:opacity-100"
          @click="removeFile(index)"
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
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  accept: {
    type: String,
    default: 'image/*',
  },
  maxFiles: {
    type: Number,
    default: 5,
  },
  maxSize: {
    type: Number, // in KB
    default: 5120, // 5MB
  },
  error: {
    type: [String, Boolean],
    default: false,
  },
})

const emit = defineEmits(['update:modelValue'])

const dragover = ref(false)
const files = ref([])
const previewUrls = ref([])

const acceptText = computed(() => {
  const types = props.accept.split(',').map(type => type.trim())
  const typeText = types.map(type => type.replace('image/', '')).join(', ').toUpperCase()
  return `${typeText}, up to ${props.maxSize / 1024}MB`
})

const validateFile = (file) => {
  // Check file type
  if (!file.type.match(props.accept.replace('*', '.*'))) {
    alert($t('Invalid file type'))
    return false
  }

  // Check file size
  if (file.size > props.maxSize * 1024) {
    alert($t('File is too large'))
    return false
  }

  return true
}

const handleFileSelect = (event) => {
  const newFiles = Array.from(event.target.files)
  
  if (!props.multiple && newFiles.length > 0) {
    files.value = []
    previewUrls.value = []
  }

  newFiles.forEach(file => {
    if (validateFile(file)) {
      if (files.value.length >= props.maxFiles) {
        alert($t('Maximum number of files reached'))
        return
      }

      files.value.push(file)
      const reader = new FileReader()
      reader.onload = (e) => {
        previewUrls.value.push(e.target.result)
      }
      reader.readAsDataURL(file)
    }
  })

  emit('update:modelValue', files.value)
  event.target.value = '' // Reset input
}

const handleDrop = (event) => {
  dragover.value = false
  const droppedFiles = Array.from(event.dataTransfer.files)
  
  if (!props.multiple && droppedFiles.length > 0) {
    files.value = []
    previewUrls.value = []
  }

  droppedFiles.forEach(file => {
    if (validateFile(file)) {
      if (files.value.length >= props.maxFiles) {
        alert($t('Maximum number of files reached'))
        return
      }

      files.value.push(file)
      const reader = new FileReader()
      reader.onload = (e) => {
        previewUrls.value.push(e.target.result)
      }
      reader.readAsDataURL(file)
    }
  })

  emit('update:modelValue', files.value)
}

const removeFile = (index) => {
  files.value.splice(index, 1)
  previewUrls.value.splice(index, 1)
  emit('update:modelValue', files.value)
}

// Watch for external changes to modelValue
watch(() => props.modelValue, (newValue) => {
  if (newValue !== files.value) {
    files.value = newValue
  }
}, { deep: true })
</script> 