<template>
  <Head :title="$t('Edit Branch')" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">
        {{ $t('Edit Branch') }}
      </h2>
    </template>

    <div class="py-6 bg-gradient-to-b from-gray-50 to-white min-h-screen">
      <div class="mx-auto sm:px-3 lg:px-4">
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
          <div class="p-4">
            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-12 gap-2">
              <!-- Branch Name -->
              <div class="col-span-1 md:col-span-6">
                <InputLabel
                  for="name"
                  :value="$t('Branch Name')"
                  class="text-sm font-semibold text-gray-800"
                />
                <TextInput
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.name }"
                  placeholder="Branch name"
                />
                <InputError
                  :message="form.errors.name"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- City Selection -->
              <div class="col-span-1 md:col-span-6">
                <InputLabel
                  for="city_id"
                  :value="$t('City')"
                  class="text-sm font-semibold text-gray-800"
                />
                <select
                  id="city_id"
                  v-model="form.city_id"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.city_id }"
                >
                  <option value="" disabled selected>{{ $t('Select a city') }}</option>
                  <option v-for="city in cities" :key="city.value" :value="city.value">
                    {{ city.label }}
                  </option>
                </select>
                <InputError
                  :message="form.errors.city_id"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
              </div>

              <!-- Working Days and Hours -->
              <div class="col-span-1 md:col-span-6">
                <InputLabel
                  :value="$t('Working Days and Hours')"
                  class="text-sm font-semibold text-gray-800"
                />
                <div class="mt-1 bg-gray-50 border-2 border-gray-200 rounded-md p-2">
                  <InputError
                    :message="form.errors.working_days"
                    class="text-xs text-red-500 font-medium"
                  />
                  <div class="space-y-2">
                    <div
                      v-for="day in weekDays"
                      :key="day.value"
                      class="flex items-center space-x-2"
                    >
                      <div class="w-1/3">
                        <label class="flex items-center group">
                          <Checkbox
                            :value="day.value"
                            :modelValue="form.working_days"
                            @update:modelValue="(newValue) => {
                              form.working_days = Array.isArray(newValue) ? newValue : 
                                (newValue ? [...(Array.isArray(form.working_days) ? form.working_days : []), day.value] : 
                                (Array.isArray(form.working_days) ? form.working_days.filter(d => d !== day.value) : []));
                              handleDaySelection(day.value);
                            }"
                            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-0 group-hover:border-indigo-400 transition-all duration-200"
                            :class="{ 'border-red-500': form.errors.working_days }"
                          />
                          <span
                            class="ml-2 text-xs font-medium text-gray-700 group-hover:text-indigo-600 transition-colors duration-200"
                          >{{ day.label }}</span>
                        </label>
                      </div>
                      <div
                        v-if="form.working_days.includes(day.value)"
                        class="flex-1 grid grid-cols-2 gap-1 items-center"
                      >
                        <TimePicker
                          v-model="form.working_hours[day.value].open"
                          :placeholder="$t('Open')"
                          class="rounded-md border-2 border-gray-200 bg-white text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 text-xs"
                          :class="{ 'border-red-500 focus:border-red-500': form.errors[`working_hours.${day.value}.open`] }"
                        />
                        <TimePicker
                          v-model="form.working_hours[day.value].close"
                          :placeholder="$t('Close')"
                          class="rounded-md border-2 border-gray-200 bg-white text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 text-xs"
                          :class="{ 'border-red-500 focus:border-red-500': form.errors[`working_hours.${day.value}.close`] }"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Branch Images -->
              <div class="col-span-1 md:col-span-6">
                <InputLabel
                  :value="$t('Branch Images')"
                  class="text-sm font-semibold text-gray-800"
                />
                <FileUpload
                  v-model="form.images"
                  :multiple="true"
                  accept="image/*"
                  :maxFiles="5"
                  :maxSize="5120"
                  class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50"
                  :class="{ 'border-red-500': form.errors.images }"
                />
                <p class="mt-1 text-xs text-gray-500 font-medium">
                  {{ $t('Max 5 images, 5MB each (JPG, PNG)') }}
                </p>
                <InputError
                  :message="form.errors.images"
                  class="mt-1 text-xs text-red-500 font-medium"
                />
                
                <!-- Display existing images -->
                <div v-if="existingImages.length" class="mt-4">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $t('Existing Images') }}</h4>
                  <div class="flex flex-wrap gap-2">
                    <div v-for="(image, index) in existingImages" :key="index" class="relative">
                      <img :src="'/storage/' + image.path" class="h-20 w-20 object-cover rounded border">
                      <button 
                        type="button"
                        @click="removeExistingImage(index)"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <div class="col-span-1 md:col-span-12 flex items-center justify-end mt-3 space-x-2">
                <Link
                  :href="route('vendor.branches.index')"
                  class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 hover:text-gray-900 transition-all duration-200"
                >
                  {{ $t('Cancel') }}
                </Link>
                <PrimaryButton
                  class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold text-xs rounded-md hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200"
                  :disabled="form.processing || form.working_days.length === 0"
                >
                  {{ $t('Update Branch') }}
                </PrimaryButton>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Checkbox from '@/Components/Checkbox.vue'
import TimePicker from '@/Components/TimePicker.vue'
import FileUpload from '@/Components/FileUpload.vue'

const props = defineProps({
  cities: {
    type: Array,
    default: () => [],
  },
  branch: {
    type: Object,
    required: true,
  },
})

const weekDays = [
  { value: 0, label: 'Sunday' },
  { value: 1, label: 'Monday' },
  { value: 2, label: 'Tuesday' },
  { value: 3, label: 'Wednesday' },
  { value: 4, label: 'Thursday' },
  { value: 5, label: 'Friday' },
  { value: 6, label: 'Saturday' },
]

// Initialize form with default working hours structure
const initializeWorkingHours = (workingDays = [], existingHours = {}) => {
  const hours = {}
  weekDays.forEach(day => {
    hours[day.value] = {
      open: existingHours[day.value]?.open || '09:00',
      close: existingHours[day.value]?.close || '17:00'
    }
  })
  return hours
}

const form = useForm({
  name: props.branch.name,
  city_id: props.branch.city_id,
  working_days: props.branch.working_days || [], // Ensure this is always an array
  working_hours: initializeWorkingHours(
    props.branch.working_days, 
    props.branch.working_hours || {}
  ),
  images: [],
  deleted_images: [],
})

const existingImages = ref(props.branch.images || [])

const handleDaySelection = (dayValue) => {
  // Ensure working_days is always treated as an array
  const workingDays = Array.isArray(form.working_days) ? form.working_days : []
  
  if (workingDays.includes(dayValue)) {
    // Initialize working hours for the selected day if not already set
    if (!form.working_hours[dayValue]) {
      form.working_hours[dayValue] = {
        open: '09:00',
        close: '17:00'
      }
    }
  }
}

const removeExistingImage = (index) => {
  const imageToRemove = existingImages.value[index]
  form.deleted_images.push(imageToRemove.id)
  existingImages.value.splice(index, 1)
}

const submit = () => {
  form.put(route('vendor.branches.update', props.branch.id), {
    _method: 'PUT',
    onSuccess: () => {
      // Handle success
    },
    onError: () => {
      // Handle error
    },
  })
}
</script> 