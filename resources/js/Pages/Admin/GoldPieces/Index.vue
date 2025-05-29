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
                                <input v-model="form.search" type="text"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :placeholder="$t('Search gold pieces...')" @input="debouncedSearch" />
                            </div>
                        </div>

                        <!-- Gold Pieces List -->
                        <div v-if="goldPieces?.data?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Gold Piece') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Vendor') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Weight') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Carat') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Type') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Status') }}
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="piece in goldPieces.data" :key="piece.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img v-if="piece.images?.length > 0"
                                                        :src="piece.images[0].thumb_url"
                                                        class="h-10 w-10 rounded-full object-cover"
                                                        alt="Gold piece image" />
                                                    <div v-else
                                                        class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <CubeIcon class="h-5 w-5 text-gray-400" />
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <Link :href="route('admin.gold-pieces.show', piece.id)"
                                                            class="hover:underline">
                                                        {{ piece.name }}
                                                        </Link>
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ piece.description }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img v-if="piece.user?.profile_photo_url"
                                                        :src="piece.user.profile_photo_url"
                                                        class="h-10 w-10 rounded-full object-cover"
                                                        alt="Vendor image" />
                                                    <div v-else
                                                        class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <UserIcon class="h-5 w-5 text-gray-400" />
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ piece.user?.name }}
                                                    </div>
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
                                            <span :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                piece.type === 'for_rent' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                                            ]">
                                                {{ piece.type === 'for_rent' ? $t('For Rent') : $t('For Sale') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                {
                                                    'bg-yellow-100 text-yellow-800': piece.status === 'pending',
                                                    'bg-green-100 text-green-800': piece.status === 'available',
                                                    'bg-blue-100 text-blue-800': piece.status === 'rented',
                                                    'bg-purple-100 text-purple-800': piece.status === 'sold',
                                                    'bg-gray-100 text-gray-800': piece.status === 'accepted'
                                                }
                                            ]">
                                                {{ $t(piece.status.charAt(0).toUpperCase() + piece.status.slice(1)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="relative inline-block text-left">
                                                <div>
                                                    <button type="button" @click.stop="toggleDropdown(piece.id)"
                                                        class="inline-flex justify-center items-center w-full rounded-md border border-gray-300 px-3 py-1.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                        id="menu-button" aria-expanded="true" aria-haspopup="true">
                                                        {{ $t('Actions') }}
                                                        <ChevronDownIcon class="-mr-1 ml-1.5 h-4 w-4" />
                                                    </button>
                                                </div>
                                                <transition enter-active-class="transition ease-out duration-100"
                                                    enter-from-class="transform opacity-0 scale-95"
                                                    enter-to-class="transform opacity-100 scale-100"
                                                    leave-active-class="transition ease-in duration-75"
                                                    leave-from-class="transform opacity-100 scale-100"
                                                    leave-to-class="transform opacity-0 scale-95">
                                                    <div v-if="activeDropdown === piece.id" ref="dropdown"
                                                        class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-50"
                                                        role="menu" aria-orientation="vertical"
                                                        aria-labelledby="menu-button" tabindex="-1">
                                                        <div class="py-1" role="none">
                                                            <Link :href="route('admin.gold-pieces.show', piece.id)"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                                role="menuitem" tabindex="-1">
                                                            <EyeIcon class="mr-3 h-4 w-4" />
                                                            {{ $t('View') }}
                                                            </Link>
                                                            <Link :href="route('admin.gold-pieces.edit', piece.id)"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                                role="menuitem" tabindex="-1">
                                                            <PencilIcon class="mr-3 h-4 w-4" />
                                                            {{ $t('Edit') }}
                                                            </Link>
                                                            <button @click="toggleStatus(piece)"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                                role="menuitem" tabindex="-1">
                                                                <ArrowPathIcon class="mr-3 h-4 w-4" />
                                                                {{ piece.status === 'available' ? $t('Mark as Unavailable') : $t('Mark as Available') }}
                                                            </button>
                                                        </div>
                                                        <div class="py-1" role="none">
                                                            <button @click="confirmDelete(piece)"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                                                role="menuitem" tabindex="-1">
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
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <CubeIcon class="w-16 h-16 mb-4 text-gray-400" />
                            <p class="mb-2 text-xl font-semibold">
                                {{ $t('No gold pieces found.') }}
                            </p>
                        </div>

                        <!-- Pagination -->
                        <Pagination v-if="goldPieces?.data?.length > 0" :links="goldPieces?.links || []" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"></div>
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ $t('Are you sure you want to delete this gold piece?') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ $t('This action cannot be undone.') }}
                            </p>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="deletePiece">
                                {{ $t('Delete') }}
                            </button>
                            <button type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="closeModal">
                                {{ $t('Cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import {
    CubeIcon,
    PencilIcon,
    TrashIcon,
    EyeIcon,
    UserIcon,
    ChevronDownIcon,
    ArrowPathIcon
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
    activeDropdown.value = activeDropdown.value === pieceId ? null : pieceId;
};

const confirmDelete = (piece) => {
    pieceToDelete.value = piece;
    confirmingDeletion.value = true;
    activeDropdown.value = null;
};

const deletePiece = () => {
    if (pieceToDelete.value) {
        form.delete(route('admin.gold-pieces.destroy', pieceToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const closeModal = () => {
    confirmingDeletion.value = false;
    pieceToDelete.value = null;
};

const toggleStatus = (piece) => {
    form.patch(route('admin.gold-pieces.toggle-status', piece.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            activeDropdown.value = null;
        },
    });
};

const handleClickOutside = (event) => {
    if (dropdown.value && !dropdown.value.contains(event.target) &&
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
    form.get(route('admin.gold-pieces.index'), {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);
</script>
