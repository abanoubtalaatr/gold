<template>
  <Head title="Add New Branch" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ $t('Add New Branch') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Branch Name -->
              <div>
                <InputLabel for="name" :value="$t('Branch Name')" />
                <TextInput
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="block w-full mt-1"
                  required
                />
                <InputError :message="form.errors.name" class="mt-2" />
              </div>

              <!-- Location -->
              <div>
                <InputLabel :value="$t('Location')" />
                <div class="mt-1">
                  <MapPicker
                    v-model:latitude="form.latitude"
                    v-model:longitude="form.longitude"
                    v-model:address="form.address"
                    :error="form.errors.latitude || form.errors.longitude || form.errors.address"
                  />
                </div>
                <InputError
                  :message="form.errors.latitude || form.errors.longitude || form.errors.address"
                  class="mt-2"
                />
              </div>

              <!-- Working Days and Hours -->
              <div class="space-y-4">
                <InputLabel :value="$t('Working Days and Hours')" />
                <div class="grid gap-4">
                  <div
                    v-for="day in weekDays"
                    :key="day.value"
                    class="flex items-center space-x-4"
                  >
                    <div class="w-1/4">
                      <label class="flex items-center">
                        <Checkbox
                          :value="day.value"
                          v-model:checked="form.working_days"
                        />
                        <span class="ml-2 text-sm text-gray-600">{{ day.label }}</span>
                      </label>
                    </div>
                    <div v-if="form.working_days.includes(day.value)" class="flex-1 flex items-center space-x-2">
                      <TimePicker
                        v-model="form.working_hours[day.value].open"
                        :placeholder="$t('Opening Time')"
                      />
                      <span class="text-gray-500">to</span>
                      <TimePicker
                        v-model="form.working_hours[day.value].close"
                        :placeholder="$t('Closing Time')"
                      />
                    </div>
                  </div>
                </div>
                <InputError :message="form.errors.working_days" class="mt-2" />
                <InputError :message="form.errors.working_hours" class="mt-2" />
              </div>

              <!-- Services -->
              <div>
                <InputLabel :value="$t('Available Services')" />
                <MultiSelect
                  v-model="form.services"
                  :options="services"
                  :placeholder="$t('Select services')"
                  class="mt-1"
                />
                <InputError :message="form.errors.services" class="mt-2" />
              </div>

              <!-- Branch Images -->
              <div>
                <InputLabel :value="$t('Branch Images')" />
                <FileUpload
                  v-model="form.images"
                  :multiple="true"
                  accept="image/*"
                  :maxFiles="5"
                  :maxSize="5120"
                  class="mt-1"
                />
                <p class="mt-1 text-sm text-gray-500">
                  {{ $t('Maximum 5 images, each up to 5MB (JPG, PNG)') }}
                </p>
                <InputError :message="form.errors.images" class="mt-2" />
              </div>

              <!-- Submit Button -->
              <div class="flex items-center justify-end mt-6">
                <Link
                  :href="route('vendor.branches.index')"
                  class="text-gray-600 hover:text-gray-900"
                >
                  {{ $t('Cancel') }}
                </Link>
                <PrimaryButton
                  class="ml-4"
                  :disabled="form.processing"
                >
                  {{ $t('Create Branch') }}
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
import { computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Checkbox from '@/Components/Checkbox.vue'
import MapPicker from '@/Components/MapPicker.vue'
import TimePicker from '@/Components/TimePicker.vue'
import MultiSelect from '@/Components/MultiSelect.vue'
import FileUpload from '@/Components/FileUpload.vue'

const weekDays = [
  { value: 0, label: 'Sunday' },
  { value: 1, label: 'Monday' },
  { value: 2, label: 'Tuesday' },
  { value: 3, label: 'Wednesday' },
  { value: 4, label: 'Thursday' },
  { value: 5, label: 'Friday' },
  { value: 6, label: 'Saturday' },
]

const form = useForm({
  name: '',
  latitude: null,
  longitude: null,
  address: '',
  working_days: [],
  working_hours: {},
  services: [],
  images: [],
})

const selectedDays = computed(() => {
  return form.working_days.sort((a, b) => a - b)
})

watch(() => form.working_days, (newDays) => {
  // Initialize working hours for newly selected days
  newDays.forEach(day => {
    if (!form.working_hours[day]) {
      form.working_hours[day] = {
        open: '09:00',
        close: '17:00'
      }
    }
  })

  // Remove working hours for unselected days
  Object.keys(form.working_hours).forEach(day => {
    if (!newDays.includes(Number(day))) {
      delete form.working_hours[day]
    }
  })
}, { deep: true })

const submit = () => {
  form.post(route('vendor.branches.store'))
}
</script> 