<template>
  <Head :title="$t('Edit Service')" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ $t('Edit Service') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <form @submit.prevent="submit" class="space-y-6">
              <!-- Service Type -->
              <div>
                <InputLabel :value="$t('services.type')" />
                <Select
                  v-model="form.type"
                  :options="types"
                  class="mt-1 block w-full"
                  :error="form.errors.type"
                />
                <InputError :message="form.errors.type" class="mt-2" />
              </div>

              <!-- Service Name -->
              <div>
                <InputLabel :value="$t('services.name')" />
                <TextInput
                  v-model="form.name"
                  type="text"
                  class="mt-1 block w-full"
                  :error="form.errors.name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
              </div>

              <!-- Service Description -->
              <div>
                <InputLabel :value="$t('services.description')" />
                <Textarea
                  v-model="form.description"
                  class="mt-1 block w-full"
                  :error="form.errors.description"
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>

              <!-- Service Price -->
              <div>
                <InputLabel :value="$t('services.price')" />
                <TextInput
                  v-model="form.price"
                  type="number"
                  step="0.01"
                  min="0"
                  class="mt-1 block w-full"
                  :error="form.errors.price"
                />
                <InputError :message="form.errors.price" class="mt-2" />
              </div>

              <!-- Available Sessions Per Day -->
              <div>
                <InputLabel :value="$t('services.available_sessions_per_day')" />
                <TextInput
                  v-model="form.available_sessions_per_day"
                  type="number"
                  min="1"
                  class="mt-1 block w-full"
                  :error="form.errors.available_sessions_per_day"
                />
                <InputError :message="form.errors.available_sessions_per_day" class="mt-2" />
              </div>

              <!-- Service Duration -->
              <div>
                <InputLabel :value="$t('services.duration')" />
                <Select
                  v-model="form.duration"
                  :options="durations"
                  class="mt-1 block w-full"
                  :error="form.errors.duration"
                />
                <InputError :message="form.errors.duration" class="mt-2" />
              </div>

              <!-- Maximum Concurrent Requests -->
              <div>
                <InputLabel :value="$t('services.max_concurrent_requests')" />
                <TextInput
                  v-model="form.max_concurrent_requests"
                  type="number"
                  min="1"
                  class="mt-1 block w-full"
                  :error="form.errors.max_concurrent_requests"
                />
                <InputError :message="form.errors.max_concurrent_requests" class="mt-2" />
              </div>

              <!-- Location Type -->
              <div>
                <InputLabel :value="$t('services.location_type')" />
                <Select
                  v-model="form.location_type"
                  :options="locationTypes"
                  class="mt-1 block w-full"
                  :error="form.errors.location_type"
                />
                <InputError :message="form.errors.location_type" class="mt-2" />
              </div>

              <!-- Available Branches -->
              <div>
                <InputLabel :value="$t('services.branches')" />
                <MultiSelect
                  v-model="form.branches"
                  :options="branches"
                  class="mt-1 block w-full"
                  :error="form.errors.branches"
                />
                <InputError :message="form.errors.branches" class="mt-2" />
              </div>

              <!-- Current Images -->
              <div v-if="service.images.length > 0">
                <InputLabel :value="$t('Current Images')" />
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-2">
                  <div
                    v-for="image in service.images"
                    :key="image.id"
                    class="relative group"
                  >
                    <img
                      :src="image.url"
                      :alt="service.name"
                      class="w-full h-40 object-cover rounded-lg"
                    />
                    <button
                      type="button"
                      @click="removeImage(image)"
                      class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <XMarkIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>

              <!-- New Images -->
              <div>
                <InputLabel :value="$t('Add New Images')" />
                <FileUpload
                  v-model="form.images"
                  multiple
                  accept="image/*"
                  :max-size="5120"
                  :max-files="5"
                  class="mt-1"
                  :error="form.errors.images"
                />
                <InputError :message="form.errors.images" class="mt-2" />
              </div>

              <!-- Submit Button -->
              <div class="flex items-center justify-end mt-6">
                <Link
                  :href="route('vendor.services.index')"
                  class="text-gray-600 hover:text-gray-900 mr-4"
                >
                  {{ $t('Cancel') }}
                </Link>
                <PrimaryButton
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  {{ $t('Update Service') }}
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
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import Textarea from '@/Components/Textarea.vue'
import Select from '@/Components/Select.vue'
import MultiSelect from '@/Components/MultiSelect.vue'
import FileUpload from '@/Components/FileUpload.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { XMarkIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  service: {
    type: Object,
    required: true,
  },
  branches: {
    type: Array,
    required: true,
  },
  durations: {
    type: Object,
    required: true,
  },
  types: {
    type: Object,
    required: true,
  },
  locationTypes: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  type: props.service.type,
  name: props.service.name,
  description: props.service.description,
  price: props.service.price,
  available_sessions_per_day: props.service.available_sessions_per_day,
  duration: props.service.duration,
  max_concurrent_requests: props.service.max_concurrent_requests,
  location_type: props.service.location_type,
  branches: props.service.branches.map(branch => branch.id),
  images: [],
  _method: 'PUT',
})

const submit = () => {
  form.post(route('vendor.services.update', props.service.id), {
    preserveScroll: true,
  })
}

const removeImage = (image) => {
  // TODO: Implement image removal logic
  console.log('Remove image:', image)
}
</script> 