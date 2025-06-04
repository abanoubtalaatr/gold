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
                        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                            <div class="flex-1 min-w-0">
                                <input v-model="form.search" type="text"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :placeholder="$t('Search gold pieces...')" @input="debouncedSearch" />
                            </div>

                            <div class="flex items-center space-x-4">
                                <select v-model="form.branch_id" @change="applyFilters"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Branches</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        {{ branch.name }}
                                    </option>
                                </select>

                                <select v-model="form.status" @change="applyFilters"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending Approval</option>
                                    <option value="available">Available</option>
                                    <option value="rented">Rented</option>
                                    <option value="sold">Sold</option>
                                    <option value="sent_to_store">Sent to Store</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
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
                                            {{ $t('Branch') }}
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
                                                        <Link :href="route('vendor.gold-pieces.show', piece.id)"
                                                            class="hover:underline">
                                                        {{ piece.name }}
                                                        </Link>
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ piece.description }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ piece.branch?.name || 'N/A' }}
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
                                                    'bg-gray-100 text-gray-800': piece.status === 'sent_to_store',
                                                    'bg-red-100 text-red-800': piece.status === 'rejected'
                                                }
                                            ]">
                                                {{ $t(piece.status.charAt(0).toUpperCase() + piece.status.slice(1).replace(/_/g, ' ')) }}
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
                                                        class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-10"
                                                        role="menu" aria-orientation="vertical"
                                                        aria-labelledby="menu-button" tabindex="-1">
                                                        <div class="py-1" role="none">
                                                            <Link :href="route('vendor.gold-pieces.show', piece.id)"
                                                                class="flex items-center w-full px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-t-md transition-all duration-200"
                                                                role="menuitem" tabindex="-1">
                                                            <EyeIcon class="mr-3 h-4 w-4" />
                                                            {{ $t('View') }}
                                                            </Link>
                                                            
                                                            <button v-if="piece.status === 'pending'"
                                                                @click="approvePiece(piece)"
                                                                class="flex items-center w-full px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 transition-all duration-200"
                                                                role="menuitem" tabindex="-1">
                                                                <CheckIcon class="mr-3 h-4 w-4" />
                                                                {{ $t('Approve') }}
                                                            </button>
                                                            
                                                            <button v-if="piece.status === 'pending'"
                                                                @click="rejectPiece(piece)"
                                                                class="flex items-center w-full px-4 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition-all duration-200"
                                                                role="menuitem" tabindex="-1">
                                                                <XMarkIcon class="mr-3 h-4 w-4" />
                                                                {{ $t('Reject') }}
                                                            </button>
                                                            
                                                            <button v-if="piece.status === 'available' && piece.type === 'for_rent'"
                                                                @click="markAsSent(piece)"
                                                                class="flex items-center w-full px-4 py-2 text-sm font-semibold text-white bg-yellow-600 hover:bg-yellow-700 transition-all duration-200"
                                                                role="menuitem" tabindex="-1">
                                                                <TruckIcon class="mr-3 h-4 w-4" />
                                                                {{ $t('Mark as Sent') }}
                                                            </button>
                                                            
                                                            <button v-if="piece.status === 'available' && piece.type === 'for_sale'"
                                                                @click="markAsSold(piece)"
                                                                class="flex items-center w-full px-4 py-2 text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 transition-all duration-200"
                                                                role="menuitem" tabindex="-1">
                                                                <ShoppingCartIcon class="mr-3 h-4 w-4" />
                                                                {{ $t('Mark as Sold') }}
                                                            </button>
                                                            
                                                            <button v-if="['available', 'rented', 'sent_to_store'].includes(piece.status)"
                                                                @click="updateStatus(piece)"
                                                                class="flex items-center w-full px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all duration-200"
                                                                role="menuitem" tabindex="-1">
                                                                <ArrowPathIcon class="mr-3 h-4 w-4" />
                                                                {{ $t('Update Status') }}
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
                            <p class="mb-4">
                                {{ $t('Try adjusting your search or filter criteria.') }}
                            </p>
                        </div>

                        <!-- Pagination -->
                        <Pagination v-if="goldPieces?.data?.length > 0" :links="goldPieces?.links || []" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <Modal :show="showApproveModal" @close="closeApproveModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ $t('Approve Gold Piece') }}
                            </h2>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $t('Select Branch') }}
                                </label>
                                <select v-model="selectedBranch"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        {{ branch.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="confirmApprove">
                                {{ $t('Approve') }}
                            </button>
                            <button type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="closeApproveModal">
                                {{ $t('Cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Status Update Modal -->
        <Modal :show="showStatusModal" @close="closeStatusModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ $t('Update Gold Piece Status') }}
                            </h2>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $t('New Status') }}
                                </label>
                                <select v-model="selectedStatus"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="available">Available</option>
                                    <option value="rented">Rented</option>
                                    <option value="sold">Sold</option>
                                </select>
                                
                                <div v-if="selectedStatus === 'sold'" class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ $t('Sale Price') }}
                                    </label>
                                    <input v-model="salePrice" type="number" step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter sale price">
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="confirmStatusUpdate">
                                {{ $t('Update') }}
                            </button>
                            <button type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                @click="closeStatusModal">
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
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import Swal from 'sweetalert2';

import {
    PlusIcon,
    ChevronDownIcon,
    CubeIcon,
    PencilIcon,
    ArrowPathIcon,
    TrashIcon,
    EyeIcon,
    CheckIcon,
    XMarkIcon,
    TruckIcon,
    ShoppingCartIcon
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
    branches: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    search: props.filters.search || '',
    branch_id: props.filters.branch_id || '',
    status: props.filters.status || '',
});

const activeDropdown = ref(null);
const showApproveModal = ref(false);
const showStatusModal = ref(false);
const pieceToApprove = ref(null);
const pieceToUpdateStatus = ref(null);
const selectedBranch = ref('');
const selectedStatus = ref('available');
const salePrice = ref(0);
const dropdown = ref(null);

const toggleDropdown = (pieceId) => {
    activeDropdown.value = activeDropdown.value === pieceId ? null : pieceId;
};

const applyFilters = () => {
    form.get(route('vendor.gold-pieces.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const approvePiece = (piece) => {
    pieceToApprove.value = piece;
    showApproveModal.value = true;
    activeDropdown.value = null;
};

const confirmApprove = () => {
    if (!selectedBranch.value) {
        Swal.fire('Error', 'Please select a branch', 'error');
        return;
    }

    router.put(route('vendor.gold-pieces.approve', pieceToApprove.value.id), {
        branch_id: selectedBranch.value,
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire('Success', 'Gold piece approved successfully', 'success');
            closeApproveModal();
        },
        onError: () => {
            Swal.fire('Error', 'Failed to approve gold piece', 'error');
        }
    });
};

const closeApproveModal = () => {
    showApproveModal.value = false;
    pieceToApprove.value = null;
    selectedBranch.value = '';
};

const rejectPiece = (piece) => {
    Swal.fire({
        title: 'Reject Gold Piece',
        text: 'Are you sure you want to reject this gold piece?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, reject it',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(route('vendor.gold-pieces.reject', piece.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Rejected!', 'Gold piece has been rejected.', 'success');
                },
                onError: () => {
                    Swal.fire('Error!', 'There was an issue rejecting the item.', 'error');
                },
            });
        }
    });
    activeDropdown.value = null;
};

const markAsSent = (piece) => {
    Swal.fire({
        title: 'Mark as Sent',
        text: 'Are you sure you want to mark this piece as sent to the store?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, mark as sent',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(route('vendor.gold-pieces.mark-sent', piece.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Success!', 'Piece marked as sent.', 'success');
                },
                onError: () => {
                    Swal.fire('Error!', 'There was an issue updating the status.', 'error');
                },
            });
        }
    });
    activeDropdown.value = null;
};

const markAsSold = (piece) => {
    Swal.fire({
        title: 'Mark as Sold',
        html: `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sale Price</label>
                <input id="sale-price" type="number" step="0.01" value="${piece.sale_price || ''}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Mark as Sold',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const price = document.getElementById('sale-price').value;
            if (!price || isNaN(price)) {
                Swal.showValidationMessage('Please enter a valid sale price');
                return false;
            }
            return { price: parseFloat(price) };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.put(route('vendor.gold-pieces.mark-sold', piece.id), {
                sale_price: result.value.price,
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Success!', 'Piece marked as sold.', 'success');
                },
                onError: () => {
                    Swal.fire('Error!', 'There was an issue updating the status.', 'error');
                },
            });
        }
    });
    activeDropdown.value = null;
};

const updateStatus = (piece) => {
    pieceToUpdateStatus.value = piece;
    selectedStatus.value = piece.status;
    salePrice.value = piece.sale_price || 0;
    showStatusModal.value = true;
    activeDropdown.value = null;
};

const confirmStatusUpdate = () => {
    const data = {
        status: selectedStatus.value
    };
    
    if (selectedStatus.value === 'sold') {
        data.sale_price = salePrice.value;
    }

    router.put(route('vendor.gold-pieces.update-status', pieceToUpdateStatus.value.id), data, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire('Success!', 'Status updated successfully.', 'success');
            closeStatusModal();
        },
        onError: () => {
            Swal.fire('Error!', 'There was an issue updating the status.', 'error');
    },
    });
};

const closeStatusModal = () => {
    showStatusModal.value = false;
    pieceToUpdateStatus.value = null;
    selectedStatus.value = 'available';
    salePrice.value = 0;
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
    form.get(route('vendor.gold-pieces.index'), {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);
</script>