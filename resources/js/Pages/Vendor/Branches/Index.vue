<template>
  <Head title="Manage Branches" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ $t('Manage Branches') }}
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
                  :placeholder="$t('Search branches...')"
                  @input="debouncedSearch"
                />
              </div>
              <Link
                :href="route('vendor.branches.create')"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <PlusIcon class="w-5 h-5 mr-2" />
                {{ $t('Add New Branch') }}
              </Link>
            </div>

            <!-- Branches List -->
            <div v-if="branches" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      v-for="header in tableHeaders"
                      :key="header"
                      class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase"
                    >
                      {{ $t(header) }}
                    </th>
                    <th class="relative px-6 py-3">
                      <span class="sr-only">{{ $t('Actions') }}</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="branch in branches.data" :key="branch.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ branch.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ branch.address }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="[
                          'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                          branch.is_active
                            ? 'bg-green-100 text-green-800'
                            : 'bg-red-100 text-red-800',
                        ]"
                      >
                        {{ branch.is_active ? $t('Active') : $t('Inactive') }}
                      </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                      <Link
                        :href="route('vendor.branches.edit', branch.id)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        {{ $t('Edit') }}
                      </Link>
                      <button
                        class="ml-4 text-red-600 hover:text-red-900"
                        @click="confirmDelete(branch)"
                      >
                        {{ $t('Delete') }}
                      </button>
                      <button
                        class="ml-4"
                        :class="
                          branch.is_active
                            ? 'text-red-600 hover:text-red-900'
                            : 'text-green-600 hover:text-green-900'
                        "
                        @click="toggleStatus(branch)"
                      >
                        {{
                          branch.is_active
                            ? $t('Deactivate')
                            : $t('Activate')
                        }}
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div
              v-else
              class="flex flex-col items-center justify-center py-12 text-gray-500"
            >
              <svg
                class="w-16 h-16 mb-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                />
              </svg>
              <p class="mb-2 text-xl font-semibold">
                {{ $t('No branches registered yet.') }}
              </p>
              <p class="mb-4">
                {{ $t('Start by adding your first branch.') }}
              </p>
              <Link
                :href="route('vendor.branches.create')"
                class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              >
                {{ $t('Add New Branch') }}
              </Link>
            </div>

            <!-- Pagination -->
            <Pagination
              v-if="branches"
              :links="branches.links"
              class="mt-6"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :show="confirmingDeletion" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          {{ $t('Are you sure you want to delete this branch?') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
          {{ $t('This action cannot be undone.') }}
        </p>
        <div class="mt-6 flex justify-end">
          <button
            type="button"
            class="mr-3 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25"
            @click="closeModal"
          >
            {{ $t('Cancel') }}
          </button>
          <button
            type="button"
            class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            @click="deleteBranch"
          >
            {{ $t('Delete Branch') }}
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'
import { PlusIcon } from '@heroicons/vue/24/outline'
import debounce from 'lodash/debounce'

const props = defineProps({
  branches: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
})

const form = useForm({
  search: props.filters.search || '',
})

const tableHeaders = ['Name', 'Address', 'Status']
const confirmingDeletion = ref(false)
const branchToDelete = ref(null)

const confirmDelete = (branch) => {
  branchToDelete.value = branch
  confirmingDeletion.value = true
}

const closeModal = () => {
  confirmingDeletion.value = false
  branchToDelete.value = null
}

const deleteBranch = () => {
  if (branchToDelete.value) {
    form.delete(route('vendor.branches.destroy', branchToDelete.value.id), {
      onSuccess: () => closeModal(),
    })
  }
}

const toggleStatus = (branch) => {
  form.patch(route('vendor.branches.toggle-status', branch.id))
}

const debouncedSearch = debounce(() => {
  form.get(route('vendor.branches.index'), {
    preserveState: true,
    preserveScroll: true,
  })
}, 300)
</script> 