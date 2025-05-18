<template>
  <Head title="Edit Branch" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ $t('Edit Branch') }}
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

              <!-- Working Days -->
              <div>
                <InputLabel :value="$t('Working Days')" />
                <div class="grid grid-cols-2 gap-4 mt-1 sm:grid-cols-4">
                  <label
                    v-for="(day, index) in weekDays"
                    :key="index"
                    class="flex items-center"
                  >
                    <Checkbox
                      :value="index"
                      v-model:checked="form.working_days"
                    />
                    <span class="ml-2 text-sm text-gray-600">{{ day }}</span>
                  </label>
                </div>
                <InputError :message="form.errors.working_days" class="mt-2" />
              </div>

              <!-- Working Hours -->
              <div>
                <InputLabel :value="$t('Working Hours')" />
                <div class="space-y-4 mt-1">
                  <div
                    v-for="(day, index) in selectedDays"
                    :key="index"
                    class="flex items-center space-x-4"
                  >
                    <span class="w-24 text-sm text-gray-600">{{ weekDays[day] }}</span>
                    <div class="flex items-center space-x-2">
                      <TimePicker
                        v-model="form.working_hours[day].open"
                        :placeholder="$t('Opening Time')"
                      />
                      <span>-</span>
                      <TimePicker
                        v-model="form.working_hours[day].close"
                        :placeholder="$t('Closing Time')"
                      />
                    </div>
                  </div>
                </div>
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
                <!-- Existing Images -->
                <div v-if="branch.images.length > 0" class="grid grid-cols-2 gap-4 mt-1 sm:grid-cols-4">
                  <div
                    v-for="image in branch.images"
                    :key="image.id"
                    class="relative group"
                  >
                    <img
                      :src="image.url"
                      class="object-cover w-full h-32 rounded-lg"
                      :alt="branch.name"
                    />
                    <button
                      type="button"
                      class="absolute inset-0 flex items-center justify-center w-full h-full transition-opacity bg-black bg-opacity-50 opacity-0 group-hover:opacity-100"
                      @click="removeImage(image.id)"
                    >
                      <span class="text-white">{{ $t('Remove') }}</span>
                    </button>
                  </div>
                </div>
                <!-- New Images Upload -->
                <FileUpload
                  v-model="form.images"
                  :multiple="true"
                  accept="image/*"
                  :maxFiles="5"
                  :maxSize="5120"
                  class="mt-4"
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
import { computed } from 'vue'
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

const props = defineProps({
  branch: {
    type: Object,
    required: true,
  },
})

const weekDays = [
  'Sunday',
  'Monday',
  'Tuesday',
  'Wednesday',
  'Thursday',
  'Friday',
  'Saturday',
]

const form = useForm({
  name: props.branch.name,
  latitude: props.branch.latitude,
  longitude: props.branch.longitude,
  address: props.branch.address,
  working_days: props.branch.working_days,
  working_hours: props.branch.working_hours,
  services: props.branch.services,
  images: [],
})

const selectedDays = computed(() => {
  return form.working_days.sort()
})

const removeImage = (imageId) => {
  form.delete(route('vendor.branches.images.destroy', [props.branch.id, imageId]), {
    preserveScroll: true,
    preserveState: true,
  })
}

const submit = () => {
  form.post(route('vendor.branches.update', props.branch.id), {
    _method: 'PUT',
  })
}
</script> 