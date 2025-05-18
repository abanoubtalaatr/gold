<template>
  <Head :title="$t('Add New Service')" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold leading-tight text-gray-800 dark:text-gray-200">
            {{ $t('Add New Service') }}
          </h2>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $t('Fill in the details to create a new service offering') }}
          </p>
        </div>
        <Link
          :href="route('vendor.services.index')"
          class="flex items-center px-4 py-2 text-sm font-medium text-white transition-all duration-200 bg-gradient-to-r from-blue-600 to-blue-500 rounded-lg shadow hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          {{ $t('Back to Services') }}
        </Link>
      </div>
    </template>

    <div class="pb-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <form @submit.prevent="submit">
          <input type="hidden" name="_token" :value="$page.props.csrf_token">
          
          <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Left Column -->
            <div class="space-y-8">
              <!-- Basic Information Card -->
              <div class="overflow-hidden bg-white rounded-xl shadow-sm dark:bg-gray-800">
                <div class="px-6 py-5 border-b border-gray-200/50 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                  <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 mr-4 bg-blue-100 rounded-lg dark:bg-blue-900/50">
                      <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $t('Basic Information') }}
                      </h3>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $t('Enter the basic details of your service') }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="px-6 py-6 space-y-6">
                  <!-- Service Type -->
                  <div>
                    <InputLabel :value="$t('Type')" class="text-sm font-medium"  />
                    <div class="mt-2">
                      <Select
                        v-model="form.type"
                        :options="types"
                        :error="form.errors.type"
                        @change="validateField('type')"
                        
                      />
                      <InputError :message="form.errors.type" class="mt-1" />
                      <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" v-if="!form.errors.type">
                        {{ $t('Select the type of service you want to offer') }}
                      </p>
                    </div>
                  </div>

                  <!-- Service Name -->
                  <div>
                    <InputLabel :value="$t('Name')" class="text-sm font-medium"  />
                    <div class="mt-2">
                      <TextInput
                        v-model="form.name"
                        type="text"
                        :error="form.errors.name"
                        :placeholder="$t('e.g. Professional Haircut, Car Detailing')"
                        @input="validateField('name')"
                        
                        minlength="3"
                        maxlength="255"
                      />
                      <InputError :message="form.errors.name" class="mt-1" />
                      <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" v-if="!form.errors.name">
                        {{ $t('Enter a descriptive name for your service (3-255 characters)') }}
                      </p>
                    </div>
                  </div>

                  <!-- Service Description -->
                  <div>
                    <InputLabel :value="$t('Description')" class="text-sm font-medium"  />
                    <div class="mt-2">
                      <Textarea
                        v-model="form.description"
                        rows="4"
                        :error="form.errors.description"
                        :placeholder="$t('Describe what your service includes, benefits, and any important details...')"
                        @input="validateField('description')"
                        
                        minlength="10"
                        maxlength="1000"
                      />
                      <InputError :message="form.errors.description" class="mt-1" />
                      <div class="flex justify-between mt-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400" v-if="!form.errors.description">
                          {{ $t('Provide a detailed description (10-1000 characters)') }}
                        </p>
                        <span class="text-xs" :class="form.description.length > 1000 ? 'text-red-500' : 'text-gray-500'">
                          {{ form.description.length }}/1000
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pricing and Duration Card -->
              <div class="overflow-hidden bg-white rounded-xl shadow-sm dark:bg-gray-800">
                <div class="px-6 py-5 border-b border-gray-200/50 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                  <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 mr-4 bg-green-100 rounded-lg dark:bg-green-900/50">
                      <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $t('Pricing and Duration') }}
                      </h3>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $t('Set your service pricing and duration details') }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="px-6 py-6 space-y-6">
                  <!-- Service Price -->
                  <div>
                    <InputLabel :value="$t('Price')" class="text-sm font-medium"  />
                    <div class="relative mt-2">
                      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <span class="text-gray-500 dark:text-gray-400">$</span>
                      </div>
                      <TextInput
                        v-model="form.price"
                        type="number"
                        step="0.01"
                        min="0"
                        class="pl-8"
                        :error="form.errors.price"
                        :placeholder="$t('e.g. 49.99')"
                        @input="validateField('price')"
                        
                      />
                      <InputError :message="form.errors.price" class="mt-1" />
                      <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" v-if="!form.errors.price">
                        {{ $t('Set a competitive price for your service') }}
                      </p>
                    </div>
                  </div>

                  <!-- Service Duration -->
                  <div>
                    <InputLabel :value="$t('Duration')" class="text-sm font-medium"  />
                    <div class="mt-2">
                      <Select
                        v-model="form.duration"
                        :options="durations"
                        :error="form.errors.duration"
                        @change="validateField('duration')"
                        
                      />
                      <InputError :message="form.errors.duration" class="mt-1" />
                      <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" v-if="!form.errors.duration">
                        {{ $t('Choose how long your service session will last') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-8">
              <!-- Availability Card -->
              <div class="overflow-hidden bg-white rounded-xl shadow-sm dark:bg-gray-800">
                <div class="px-6 py-5 border-b border-gray-200/50 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                  <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 mr-4 bg-purple-100 rounded-lg dark:bg-purple-900/50">
                      <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $t('Availability Settings') }}
                      </h3>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $t('Configure your service availability and capacity') }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="px-6 py-6 space-y-6">
                  <!-- Available Sessions Per Day -->
                  <div>
                    <InputLabel :value="$t('Available Sessions Per Day')" class="text-sm font-medium"  />
                    <div class="mt-2">
                      <TextInput
                        v-model="form.available_sessions_per_day"
                        type="number"
                        min="1"
                        max="100"
                        :error="form.errors.available_sessions_per_day"
                        :placeholder="$t('e.g. 10')"
                        @input="validateField('available_sessions_per_day')"
                        
                      />
                      <InputError :message="form.errors.available_sessions_per_day" class="mt-1" />
                      <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" v-if="!form.errors.available_sessions_per_day">
                        {{ $t('Number of sessions you can handle per day (1-100)') }}
                      </p>
                    </div>
                  </div>

                  <!-- Maximum Concurrent Requests -->
                  <div>
                    <InputLabel :value="$t('Maximum Concurrent Requests')" class="text-sm font-medium"  />
                    <div class="mt-2">
                      <TextInput
                        v-model="form.max_concurrent_requests"
                        type="number"
                        min="1"
                        max="50"
                        :error="form.errors.max_concurrent_requests"
                        :placeholder="$t('e.g. 5')"
                        @input="validateField('max_concurrent_requests')"
                        
                      />
                      <InputError :message="form.errors.max_concurrent_requests" class="mt-1" />
                      <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" v-if="!form.errors.max_concurrent_requests">
                        {{ $t('Maximum simultaneous requests you can handle (1-50)') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Location and Branches Card -->
              <div class="overflow-hidden bg-white rounded-xl shadow-sm dark:bg-gray-800">
                <div class="px-6 py-5 border-b border-gray-200/50 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                  <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 mr-4 bg-yellow-100 rounded-lg dark:bg-yellow-900/50">
                      <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $t('Location') }}
                      </h3>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $t('Specify service location type and available branches') }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="px-6 py-6 space-y-6">
                  <!-- Location Type -->
                  <div>
                    <InputLabel :value="$t('Location Type')" class="text-sm font-medium"  />
                    <div class="mt-2">
                      <Select
                        v-model="form.location_type"
                        :options="locationTypes"
                        :error="form.errors.location_type"
                        
                      />
                      <InputError :message="form.errors.location_type" class="mt-1" />
                      <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" v-if="!form.errors.location_type">
                        {{ $t('Select where this service will be provided') }}
                      </p>
                    </div>
                  </div>

                  <!-- Available Branches -->
                  <div v-if="branches.length > 0">
                    <InputLabel :value="$t('Available Branches')" class="text-sm font-medium" />
                    <div class="mt-2">
                      <MultiSelect
                        v-model="form.branches"
                        :options="branches"
                        :error="form.errors.branches"
                        :placeholder="$t('Select branches where this service is available')"
                      />
                      <InputError :message="form.errors.branches" class="mt-1" />
                      <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" v-if="!form.errors.branches">
                        {{ $t('Select all locations where this service is offered') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Service Images Card -->
              <div class="overflow-hidden bg-white rounded-xl shadow-sm dark:bg-gray-800">
                <div class="px-6 py-5 border-b border-gray-200/50 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
                  <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 mr-4 bg-pink-100 rounded-lg dark:bg-pink-900/50">
                      <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <div>
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $t('Service Images') }}
                      </h3>
                      <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $t('Upload images showcasing your service (max 5 images)') }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="px-6 py-6">
                  <FileUpload
                    v-model="form.images"
                    multiple
                    accept="image/*"
                    :max-size="5120"
                    :max-files="5"
                    :error="form.errors.images"
                  />
                  <InputError :message="form.errors.images" class="mt-1" />
                  <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ $t('Upload high-quality images (JPG, PNG, max 5MB each)') }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-end pt-6 mt-8 space-x-4 border-t border-gray-200 dark:border-gray-700">
            <Link
              :href="route('vendor.services.index')"
              class="px-5 py-2.5 text-sm font-medium text-gray-700 transition-all duration-200 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              {{ $t('Cancel') }}
            </Link>
            <PrimaryButton
              type="submit"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              class="px-6 py-2.5 text-sm font-medium text-white transition-all duration-200 bg-gradient-to-r from-blue-600 to-blue-500 rounded-lg shadow hover:from-blue-700 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <span v-if="!form.processing">{{ $t('Create Service') }}</span>
              <span v-else class="flex items-center">
                <svg class="w-4 h-4 mr-2 -ml-1 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ $t('Creating...') }}
              </span>
            </PrimaryButton>
          </div>
        </form>
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

const props = defineProps({
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
  type: '',
  name: '',
  description: '',
  price: '',
  available_sessions_per_day: 1,
  duration: '',
  max_concurrent_requests: 1,
  location_type: '',
  branches: [],
  images: [],
})

const validationRules = {
  name: {
    required: false,
    min: 3,
    max: 255,
  },
  description: {
    required: false,
    min: 10,
    max: 1000,
  },
  price: {
    required: false,
    min: 0,
  },
  available_sessions_per_day: {
    required: false,
    min: 1,
    max: 100,
  },
  max_concurrent_requests: {
    required: false,
    min: 1,
    max: 50,
  },
  location_type: {
    required: false,
  },
  images: {
    required: false,
  }
}

const validateField = (field) => {
  const rules = validationRules[field]
  if (!rules) return

  const value = form[field]
  let error = null

  if (rules.required && !value) {
    error = $t('This field is required')
  } else if (rules.min && value < rules.min) {
    error = $t('Minimum value is {min}', { min: rules.min })
  } else if (rules.max && value > rules.max) {
    error = $t('Maximum value is {max}', { max: rules.max })
  }

  if (error) {
    form.setError(field, error)
  } else {
    form.clearErrors(field)
  }
}

const submit = () => {
  // Validate all fields before submitting
  Object.keys(validationRules).forEach(field => validateField(field))
  
  if (Object.keys(form.errors).length === 0) {
    form.post(route('vendor.services.store'), {
      preserveScroll: true,
      onSuccess: () => {
        toast.success($t('Service created successfully'))
      },
      onError: (errors) => {
        if (errors && !errors.message) {
          toast.error($t('Please fix the errors in the form'))
        } else {
          toast.error(errors.message || $t('Failed to create service'))
        }
      },
      onFinish: () => {
        form.reset()
      }
    })
  } else {
    toast.error($t('Please fix the errors in the form'))
  }
}
</script>