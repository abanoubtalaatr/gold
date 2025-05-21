<template>
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
            <div v-if="branches?.data?.length > 0" class="">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Branch') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('City') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Status') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Actions') }}
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="branch in branches.data" :key="branch.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center gap-2">
                        <div class="flex-shrink-0 h-10 w-10">
                          <img
                            v-if="branch.images?.length > 0"
                            :src="'/storage/' + branch.images[0].path"
                            class="h-10 w-10 rounded-full object-cover"
                            alt="Branch image"
                          />
                          <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <BuildingOfficeIcon class="h-5 w-5 text-gray-400" />
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">{{ branch.name }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ branch.city?.name || 'N/A' }}
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
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="relative inline-block text-left">
                        <button
                          type="button"
                          @click.stop="toggleDropdown(branch.id)"
                          class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-150"
                          :aria-expanded="activeDropdown === branch.id"
                          aria-haspopup="true"
                        >
                          {{ $t('Actions') }}
                          <ChevronDownIcon class="w-4 h-4 ml-2 -mr-1" />
                        </button>
                        <transition
                          enter-active-class="transition ease-out duration-100"
                          enter-from-class="transform opacity-0 scale-95"
                          enter-to-class="transform opacity-100 scale-100"
                          leave-active-class="transition ease-in duration-75"
                          leave-from-class="transform opacity-100 scale-100"
                          leave-to-class="transform opacity-0 scale-95"
                        >
                          <div
                            v-if="activeDropdown === branch.id"
                            ref="dropdown"
                            class="absolute right-0 z-20 w-48 mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            role="menu"
                            aria-orientation="vertical"
                            :aria-labelledby="'menu-button-' + branch.id"
                          >
                            <div class="py-1">
                              <Link
                                :href="route('vendor.branches.edit', branch.id)"
                                class="flex items-center justify-center w-full px-2 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 font-medium transition-colors duration-150"
                                role="menuitem"
                              >
                                <PencilIcon class="w-4 h-4 mr-3 text-gray-500" />
                                {{ $t('Edit') }}
                              </Link>
                              <button
                                @click="toggleStatus(branch)"
                                class="flex items-center justify-center border-none bg-white w-full px-2 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 font-medium transition-colors duration-150"
                                :class="branch.is_active ? 'text-red-700 hover:bg-red-50 hover:text-red-900' : 'text-green-700 hover:bg-green-50 hover:text-green-900'"
                                role="menuitem"
                              >
                                <ArrowPathIcon class="w-4 h-4 mr-3" :class="branch.is_active ? 'text-red-500' : 'text-green-500'" />
                                {{ branch.is_active ? $t('Deactivate') : $t('Activate') }}
                              </button>
                              <button
                                @click="deleteBranch(branch)"
                                class="flex items-center justify-center w-full px-2 py-2 border-none bg-white text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 font-medium transition-colors duration-150"
                                role="menuitem"
                              >
                                <TrashIcon class="w-4 h-4 mr-3 text-red-500" />
                                {{ $t('Delete') }}
                              </button>
                            </div>
                          </div>
                        </transition>
                      </div>
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
              <BuildingOfficeIcon class="w-16 h-16 mb-4 text-gray-400" />
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
              v-if="branches?.data?.length > 0"
              :links="branches?.links || []"
              class="mt-6"
            />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import {
  PlusIcon,
  ChevronDownIcon,
  BuildingOfficeIcon,
  PencilIcon,
  ArrowPathIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline';
import debounce from 'lodash/debounce';
import Swal from 'sweetalert2';

const { t } = useI18n();

const props = defineProps({
  branches: {
    type: Object,
    default: () => ({ data: [], links: [] }),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
});

const form = useForm({
  search: props.filters.search || '',
});

const activeDropdown = ref(null);
const dropdown = ref(null);

const toggleDropdown = (branchId) => {
  activeDropdown.value = activeDropdown.value === branchId ? null : branchId;
};

const deleteBranch = (branch) => {
  Swal.fire({
    title: t('are_your_sure'),
    text: t('You_will_not_be_able_to_revert_this'),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: t('yes'),
    cancelButtonText: t('cancel'),
  }).then((result) => {
    if (result.isConfirmed) {
      form.delete(route('vendor.branches.destroy', branch.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          Swal.fire({
            title: t('data_deleted_successfully'),
            icon: 'success',
          });
          activeDropdown.value = null;
        },
        onError: () => {
          Swal.fire({
            title: 'Error!',
            text: 'There was an issue deleting the branch.',
            icon: 'error',
          });
        },
      });
    }
  });
};

const toggleStatus = (branch) => {
  form.patch(route('vendor.branches.toggle-status', branch.id), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      activeDropdown.value = null;
      form.get(route('vendor.branches.index'), {
        preserveState: true,
        preserveScroll: true,
      });
    },
    onError: (errors) => {
      console.log('Toggle status error:', errors);
    },
  });
};

const handleClickOutside = (event) => {
  const dropdownElement = Array.isArray(dropdown.value) ? dropdown.value[0] : dropdown.value;
  if (dropdownElement && typeof dropdownElement.contains === 'function' && 
      !dropdownElement.contains(event.target) && 
      !event.target.closest('[aria-haspopup="true"]')) {
    activeDropdown.value = null;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const debouncedSearch = debounce(() => {
  form.get(route('vendor.branches.index'), {
    preserveState: true,
    preserveScroll: true,
  });
}, 300);

// Debug branches
watch(
  () => props.branches,
  (newBranches) => {
    console.log('Branches:', newBranches);
  },
  { immediate: true }
);
</script>