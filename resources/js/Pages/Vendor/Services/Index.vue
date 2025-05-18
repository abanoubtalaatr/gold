<template>
  <Head :title="$t('Manage Services')" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ $t('Manage Services') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Search and Filters -->
            <div class="flex flex-wrap items-center justify-between mb-6">
              <div class="flex-1 min-w-0 mr-4">
                <input
                  v-model="form.search"
                  type="text"
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :placeholder="$t('Search services...')"
                />
              </div>
              <div class="flex items-center space-x-4">
                <Link
                  :href="route('vendor.services.create')"
                  class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                  {{ $t('Add New Service') }}
                </Link>
              </div>
            </div>

            <!-- Services List -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('services.type') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('services.name') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('services.price') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('services.duration') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('services.location_type') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('services.rating') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('services.is_active') }}
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Actions') }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="service in services" :key="service.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ service.type_text }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ service.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ service.price }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ service.duration_text }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ service.location_type_text }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <StarIcon class="w-5 h-5 text-yellow-400" />
                        <span class="ml-1">{{ service.rating }} ({{ service.rating_count }})</span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <Switch
                        v-model="service.is_active"
                        @change="toggleStatus(service)"
                        class="relative inline-flex h-6 w-11 items-center rounded-full"
                      >
                        <span class="sr-only">{{ $t('Toggle status') }}</span>
                      </Switch>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                      <div class="flex items-center justify-end space-x-3">
                        <Link
                          :href="route('vendor.services.edit', service.id)"
                          class="text-blue-600 hover:text-blue-900"
                        >
                          {{ $t('Edit') }}
                        </Link>
                        <button
                          @click="confirmDelete(service)"
                          class="text-red-600 hover:text-red-900"
                        >
                          {{ $t('Delete') }}
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="closeDeleteModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          {{ $t('Confirm Delete') }}
        </h2>
        <p class="mt-3 text-sm text-gray-600">
          {{ $t('services.errors.has_active_bookings') }}
        </p>
        <div class="mt-6 flex justify-end space-x-3">
          <SecondaryButton @click="closeDeleteModal">
            {{ $t('Cancel') }}
          </SecondaryButton>
          <DangerButton
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="deleteService"
          >
            {{ $t('Delete Service') }}
          </DangerButton>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import Switch from '@/Components/Switch.vue'
import { StarIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
  services: {
    type: Array,
    required: true,
  },
})

const form = useForm({
  search: '',
})

const showDeleteModal = ref(false)
const serviceToDelete = ref(null)

const confirmDelete = (service) => {
  serviceToDelete.value = service
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  serviceToDelete.value = null
}

const deleteService = () => {
  if (serviceToDelete.value) {
    form.delete(route('vendor.services.destroy', serviceToDelete.value.id), {
      onSuccess: () => {
        closeDeleteModal()
      },
    })
  }
}

const toggleStatus = (service) => {
  form.patch(route('vendor.services.toggle-status', service.id))
}
</script> 