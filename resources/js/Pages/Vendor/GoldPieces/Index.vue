<template>
  <Head title="Manage Gold Pieces" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ $t('Manage Gold Pieces') }}
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
                  :placeholder="$t('Search gold pieces...')"
                  @input="debouncedSearch"
                />
              </div>
              <Link
                :href="route('vendor.gold-pieces.create')"
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <PlusIcon class="w-5 h-5 mr-2" />
                {{ $t('Add New Gold Piece') }}
              </Link>
            </div>

            <!-- Gold Pieces List -->
            <div v-if="goldPieces?.data?.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Gold Piece') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Weight') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Carat') }}
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      {{ $t('Type') }}
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
                  <tr v-for="piece in goldPieces.data" :key="piece.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <img
                            v-if="piece.images?.length > 0"
                            :src="'/storage/' + piece.images[0].path"
                            class="h-10 w-10 rounded-full object-cover"
                            alt="Gold piece image"
                          />
                          <div v-else class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <CubeIcon class="h-5 w-5 text-gray-400" />
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">{{ piece.name }}</div>
                          <div class="text-sm text-gray-500">{{ piece.description }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ piece.weight }}g
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ piece.carat }}K
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <span
                        :class="[
                          'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                          piece.type === 'for_rent' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                        ]"
                      >
                        {{ piece.type === 'for_rent' ? $t('For Rent') : $t('For Sale') }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="[
                          'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                          {
                            'bg-yellow-100 text-yellow-800': piece.status === 'pending',
                            'bg-green-100 text-green-800': piece.status === 'available',
                            'bg-blue-100 text-blue-800': piece.status === 'rented',
                            'bg-purple-100 text-purple-800': piece.status === 'sold',
                            'bg-gray-100 text-gray-800': piece.status === 'accepted'
                          }
                        ]"
                      >
                        {{ $t(piece.status.charAt(0).toUpperCase() + piece.status.slice(1)) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="relative inline-block text-left">
                        <div>
                          <button
                            type="button"
                            @click.stop="toggleDropdown(piece.id)"
                            class="inline-flex justify-center items-center w-full rounded-md border border-gray-300 px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            id="menu-button"
                            aria-expanded="true"
                            aria-haspopup="true"
                          >
                            {{ $t('Actions') }}
                            <ChevronDownIcon class="-mr-1 ml-1.5 h-4 w-4" />
                          </button>
                        </div>
                        <transition
                          enter-active-class="transition ease-out duration-100"
                          enter-from-class="transform opacity-0 scale-95"
                          enter-to-class="transform opacity-100 scale-100"
                          leave-active-class="transition ease-in duration-75"
                          leave-from-class="transform opacity-100 scale-100"
                          leave-to-class="transform opacity-0 scale-95"
                        >
                          <div
                            v-if="activeDropdown === piece.id"
                            ref="dropdown"
                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-10"
                            role="menu"
                            aria-orientation="vertical"
                            aria-labelledby="menu-button"
                            tabindex="-1"
                          >
                            <div class="py-1" role="none">
                              <Link
                                :href="route('vendor.gold-pieces.edit', piece.id)"
                                class="flex items-center w-full px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-t-md transition-all duration-200"
                                role="menuitem"
                                tabindex="-1"
                              >
                                <PencilIcon class="mr-3 h-4 w-4" />
                                {{ $t('Edit') }}
                              </Link>
                              <button
                                @click="toggleStatus(piece)"
                                class="flex items-center w-full px-4 py-2 text-sm font-semibold text-white"
                                :class="piece.status === 'available' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                                role="menuitem"
                                tabindex="-1"
                              >
                                <ArrowPathIcon class="mr-3 h-4 w-4" />
                                {{ piece.status === 'available' ? $t('Mark as Unavailable') : $t('Mark as Available') }}
                              </button>
                            </div>
                            <div class="py-1" role="none">
                              <button
                                @click="confirmDelete(piece)"
                                class="flex items-center w-full px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-b-md transition-all duration-200"
                                role="menuitem"
                                tabindex="-1"
                              >
                                <TrashIcon class="mr-3 h-4 w-4" />
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
              <CubeIcon class="w-16 h-16 mb-4 text-gray-400" />
              <p class="mb-2 text-xl font-semibold">
                {{ $t('No gold pieces registered yet.') }}
              </p>
              <p class="mb-4">
                {{ $t('Start by adding your first gold piece.') }}
              </p>
              <Link
                :href="route('vendor.gold-pieces.create')"
                class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              >
                {{ $t('Add New Gold Piece') }}
              </Link>
            </div>

            <!-- Pagination -->
            <Pagination
              v-if="goldPieces?.data?.length > 0"
              :links="goldPieces?.links || []"
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
          {{ $t('Are you sure you want to delete this gold piece?') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
          {{ $t('This action cannot be undone.') }}
        </p>
        <div class="mt-6 flex justify-end space-x-3">
          <button
            type="button"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
            @click="closeModal"
          >
            {{ $t('Cancel') }}
          </button>
          <button
            type="button"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200"
            @click="deletePiece"
          >
            {{ $t('Delete Gold Piece') }}
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import {
  PlusIcon,
  ChevronDownIcon,
  CubeIcon,
  PencilIcon,
  ArrowPathIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline';
import debounce from 'lodash/debounce';

const props = defineProps({
  goldPieces: {
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
const confirmingDeletion = ref(false);
const pieceToDelete = ref(null);
const dropdown = ref(null);

const toggleDropdown = (pieceId) => {
  console.log('Toggling dropdown for piece:', pieceId);
  activeDropdown.value = activeDropdown.value === pieceId ? null : pieceId;
};

const confirmDelete = (piece) => {
  console.log('Confirm delete triggered for piece:', piece.id);
  pieceToDelete.value = piece;
  confirmingDeletion.value = true;
  activeDropdown.value = null;
};

const closeModal = () => {
  console.log('Closing modal');
  confirmingDeletion.value = false;
  pieceToDelete.value = null;
};

const deletePiece = () => {
  if (pieceToDelete.value) {
    console.log('Deleting piece:', pieceToDelete.value.id);
    form.delete(route('vendor.gold-pieces.destroy', pieceToDelete.value.id), {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        console.log('Gold piece deleted successfully');
        closeModal();
      },
      onError: (errors) => {
        console.log('Delete error:', errors);
        closeModal();
      },
    });
  }
};

const toggleStatus = (piece) => {
  console.log('Toggling status for piece:', piece.id);
  form.patch(route('vendor.gold-pieces.toggle-status', piece.id), {
    preserveScroll: true,
    preserveState: true,
    onSuccess: () => {
      console.log('Status toggled successfully');
      activeDropdown.value = null;
      // Refresh the gold pieces list
      form.get(route('vendor.gold-pieces.index'), {
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
    console.log('Closing dropdown due to outside click');
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
  form.get(route('vendor.gold-pieces.index'), {
    preserveState: true,
    preserveScroll: true,
  });
}, 300);

// Debug modal and gold pieces
watch(
  () => confirmingDeletion.value,
  (newValue) => {
    console.log('confirmingDeletion:', newValue);
  }
);
watch(
  () => props.goldPieces,
  (newPieces) => {
    console.log('Gold Pieces:', newPieces);
  },
  { immediate: true }
);
</script> 