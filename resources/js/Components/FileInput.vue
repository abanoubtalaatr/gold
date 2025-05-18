<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  modelValue: {
    type: [File, Array],
    default: null,
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  accept: {
    type: String,
    default: 'image/*',
  },
  maxSize: {
    type: Number,
    default: 5242880, // 5MB in bytes
  },
  maxFiles: {
    type: Number,
    default: 5,
  },
  error: {
    type: String,
    default: '',
  },
  label: {
    type: String,
    default: '',
  },
  helpText: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);

const fileInput = ref(null);
const dragOver = ref(false);

const previews = ref([]);

const hasError = computed(() => props.error !== '');

const handleFiles = (files) => {
  const validFiles = Array.from(files).filter(file => {
    const isValidType = file.type.startsWith('image/');
    const isValidSize = file.size <= props.maxSize;
    return isValidType && isValidSize;
  });

  if (props.multiple) {
    const totalFiles = (props.modelValue?.length || 0) + validFiles.length;
    if (totalFiles > props.maxFiles) {
      validFiles.splice(props.maxFiles - (props.modelValue?.length || 0));
    }
    emit('update:modelValue', [...(props.modelValue || []), ...validFiles]);
    generatePreviews([...validFiles]);
  } else {
    emit('update:modelValue', validFiles[0] || null);
    generatePreviews([validFiles[0]].filter(Boolean));
  }
};

const generatePreviews = (files) => {
  files.forEach(file => {
    const reader = new FileReader();
    reader.onload = (e) => {
      if (props.multiple) {
        previews.value.push(e.target.result);
      } else {
        previews.value = [e.target.result];
      }
    };
    reader.readAsDataURL(file);
  });
};

const handleDrop = (e) => {
  e.preventDefault();
  dragOver.value = false;
  handleFiles(e.dataTransfer.files);
};

const handleDragOver = (e) => {
  e.preventDefault();
  dragOver.value = true;
};

const handleDragLeave = () => {
  dragOver.value = false;
};

const removeFile = (index) => {
  if (props.multiple) {
    const newFiles = [...props.modelValue];
    newFiles.splice(index, 1);
    emit('update:modelValue', newFiles);
    previews.value.splice(index, 1);
  } else {
    emit('update:modelValue', null);
    previews.value = [];
  }
};

const openFileDialog = () => {
  fileInput.value.click();
};
</script>

<template>
  <div class="w-full">
    <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
      {{ label }}
    </label>
    
    <div
      class="relative"
      @dragover="handleDragOver"
      @dragleave="handleDragLeave"
      @drop="handleDrop"
    >
      <input
        ref="fileInput"
        type="file"
        :accept="accept"
        :multiple="multiple"
        class="hidden"
        @change="handleFiles($event.target.files)"
      >
      
      <div
        class="min-h-[150px] p-4 border-2 border-dashed rounded-lg cursor-pointer transition-colors"
        :class="{
          'border-primary-500 bg-primary-50': dragOver,
          'border-red-500 bg-red-50': hasError && !dragOver,
          'border-gray-300 hover:border-primary-500 dark:border-gray-600 dark:hover:border-primary-400': !dragOver && !hasError,
          'bg-gray-50 dark:bg-gray-800': !dragOver && !hasError
        }"
        @click="openFileDialog"
      >
        <!-- Upload Area -->
        <div v-if="!modelValue || (multiple && !modelValue.length)" class="text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <div class="mt-4 flex text-sm text-gray-600 dark:text-gray-300">
            <span class="relative cursor-pointer rounded-md font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
              Upload files
            </span>
            <p class="pl-1">or drag and drop</p>
          </div>
          <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ helpText }}
          </p>
        </div>

        <!-- Preview Area -->
        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div
            v-for="(preview, index) in previews"
            :key="index"
            class="relative group aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700"
          >
            <img
              :src="preview"
              class="w-full h-full object-cover"
              alt="Preview"
            >
            <button
              @click.stop="removeFile(index)"
              class="absolute top-2 right-2 p-1 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <p v-if="error" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ error }}</p>
    </div>
  </div>
</template> 