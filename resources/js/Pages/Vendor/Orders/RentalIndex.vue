<template>

    <Head title="Vendor Orders" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Vendor Orders') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Filters -->
                        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                            <div class="flex-1 min-w-0">
                                <input v-model="form.search" type="text"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :placeholder="$t('Search by user or piece name...')" @input="debouncedSearch" />
                            </div>
                            <div class="flex items-center gap-4">
                                <select v-model="form.branch_id"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    @change="applyFilters">
                                    <option value="">{{ $t('All Branches') }}</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        {{ branch.name }}
                                    </option>
                                </select>
                                <select v-model="form.status"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    @change="applyFilters">
                                    <option value="">{{ $t('All Statuses') }}</option>
                                    <option value="pending_approval">{{ $t('Future') }}</option>
                                    <option value="rented">{{ $t('Current') }}</option>
                                    <option value="available">{{ $t('Finished') }}</option>
                                </select>
                                <button @click="resetFilters"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    {{ $t('Reset') }}
                                </button>
                            </div>
                        </div>

                        <!-- Rental Orders -->
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Rental Orders') }}</h3>
                        <div v-if="rentalOrders?.data?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Order ID') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('User') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Gold Piece') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Branch') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Price') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Status') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="order in rentalOrders.data" :key="order.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <img v-if="order.user?.avatar" :src="order.user.avatar"
                                                    class="h-8 w-8 rounded-full object-cover mr-2" alt="User avatar" />
                                                <div>
                                                    {{ order.user?.name || 'N/A' }}<br />
                                                    {{ order.user?.email || 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <strong @click="showDetails(order)"
                                                class="text-yellow-600 hover:text-yellow-200 hover:underline cursor-pointer transition-colors duration-200">
                                                {{ order.gold_piece && order.gold_piece.name ? order.gold_piece.name :
                                                    'N/A' }}
                                            </strong>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.branch?.name || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.total_price }} {{ $t('SAR') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(order.status)]">
                                                {{ getDisplayStatus(order.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- Accept button - only show for Future orders -->
                                                <button
                                                    v-if="order.status === 'pending_approval' || order.status === 'rejected'"
                                                    @click="acceptOrderDirect(order)" :disabled="accepting"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:opacity-50 transition-colors duration-200">
                                                    {{ accepting && selectedOrderId === order.id ? $t('Processing...') :
                                                        $t('Accept') }}
                                                </button>

                                                <!-- Reject button - only show for Future orders -->
                                                <button
                                                    v-if="order.status === 'pending_approval' || order.status === 'rejected'"
                                                    @click="rejectOrderDirect(order)" :disabled="rejecting"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50 transition-colors duration-200">
                                                    {{ rejecting && selectedOrderId === order.id ? $t('Processing...') :
                                                        $t('Reject') }}
                                                </button>
                                                <!-- Updated Dropdown Menu with simplified status options -->
                                                <div class="relative inline-block text-left"
                                                    v-if="['rented', 'available', 'approved'].includes(order.status)">
                                                    <button type="button" @click.stop="toggleOrderDropdown(order.id)"
                                                        class="inline-flex items-center justify-center w-full px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-150"
                                                        :aria-expanded="activeDropdown === order.id"
                                                        aria-haspopup="true">
                                                        {{ $t('Change Status') }}
                                                        <svg class="w-4 h-4 ml-2 -mr-1"
                                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    <div enter-active-class="transition ease-out duration-100"
                                                        enter-from-class="transform opacity-0 scale-95"
                                                        enter-to-class="transform opacity-100 scale-100"
                                                        leave-active-class="transition ease-in duration-75"
                                                        leave-from-class="transform opacity-100 scale-100"
                                                        leave-to-class="transform opacity-0 scale-95">
                                                        <div v-if="activeDropdown === order.id" ref="dropdown"
                                                            class="absolute right-0 z-20 w-48 mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                                            role="menu" aria-orientation="vertical"
                                                            :aria-labelledby="'menu-button-' + order.id">
                                                            <div class="py-1">
                                                                <!-- Future -->
                                                                <button v-if="order.status !== 'pending_approval'"
                                                                    @click="updateStatus(order.id, 'pending_approval')"
                                                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 font-medium transition-colors duration-150 menue"
                                                                    role="menuitem">
                                                                    <svg class="w-4 h-4 mr-3 text-yellow-500"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 1 1 0 001.415 1.414z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    {{ $t('Mark as Future') }}
                                                                </button>

                                                                <!-- Current -->
                                                                <button
                                                                    v-if="order.status !== 'rented' && order.status !== 'approved'"
                                                                    @click="updateStatus(order.id, 'rented')"
                                                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 font-medium transition-colors duration-150 menue"
                                                                    role="menuitem">
                                                                    <svg class="w-4 h-4 mr-3 text-green-500"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    {{ $t('Mark as Current') }}
                                                                </button>

                                                                <!-- Finished -->
                                                                <button v-if="order.status !== 'available'"
                                                                    @click="updateStatus(order.id, 'available')"
                                                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-900 font-medium transition-colors duration-150 menue"
                                                                    role="menuitem">
                                                                    <svg class="w-4 h-4 mr-3 text-gray-500"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 20 20" fill="currentColor">
                                                                        <path fill-rule="evenodd"
                                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    {{ $t('Mark as Finished') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <Pagination :links="rentalOrders.links" class="mt-6" />
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <p class="text-xl font-semibold">{{ $t('No rental orders found.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import debounce from 'lodash/debounce';
import { useI18n } from 'vue-i18n';
import Swal from 'sweetalert2';

const { t } = useI18n();
const props = defineProps({
    rentalOrders: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    saleOrders: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    branches: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    search: props.filters.search || '',
    branch_id: props.filters.branch_id || '',
    status: props.filters.status || '',
});

const showAcceptModal = ref(false);
const selectedOrder = ref(null);
const acceptForm = useForm({
    branch_id: '',
});

const showRejectModal = ref(false);
const showDetailsModal = ref(false);
const activeDropdown = ref(null);

const debouncedSearch = debounce(() => {
    form.get(route('vendor.orders.rental.index'), {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);

const applyFilters = () => {
    form.get(route('vendor.orders.rental.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.branch_id = '';
    form.status = '';

    router.get(route('vendor.orders.rental.index'), {}, {
        preserveState: false,
        preserveScroll: true,
        replace: true
    });
};

const getDisplayStatus = (status) => {
    const statusMap = {
        'pending_approval': t('Future'),
        'rejected': t('Future'),
        'rented': t('Current'),
        "approved": t('Current'),
        'available': t('Finished'),
    };
    return statusMap[status] || status;
};

const getStatusClass = (status) => {
    const statusClasses = {
        'pending_approval': 'bg-yellow-100 text-yellow-800', // Future
        'rented': 'bg-green-100 text-green-800', // Current
        "approved": 'bg-green-100 text-green-800',
        'available': 'bg-gray-100 text-gray-800', // Finished
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
};

const openAcceptModal = (order) => {
    selectedOrder.value = order;
    acceptForm.reset();
    showAcceptModal.value = true;
};

const closeAcceptModal = () => {
    showAcceptModal.value = false;
    selectedOrder.value = null;
    acceptForm.reset();
};

const acceptOrder = () => {
    acceptForm.post(route('vendor.orders.rental.accept', selectedOrder.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeAcceptModal();
        },
    });
};

const openRejectModal = (order) => {
    selectedOrder.value = order;
    showRejectModal.value = true;
};

const closeRejectModal = () => {
    showRejectModal.value = false;
    selectedOrder.value = null;
};

const rejectOrder = () => {
    router.post(route('vendor.orders.rental.reject', selectedOrder.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            closeRejectModal();
        },
        onError: (errors) => {
            console.error('Error rejecting order:', errors);
        }
    });
};

const updateStatus = (orderId, status) => {
    if (!status) return;

    // Close the dropdown after selection
    activeDropdown.value = null;

    router.patch(route('vendor.orders.rental.updateStatus', orderId), {
        status: status
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Add any success handling
        },
        onError: (errors) => {
            console.error('Error updating status:', errors);
        }
    });
};

const showDetails = (order) => {
    selectedOrder.value = order;
    showDetailsModal.value = true;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedOrder.value = null;
};

const toggleOrderDropdown = (orderId) => {
    if (activeDropdown.value === orderId) {
        activeDropdown.value = null;
    } else {
        activeDropdown.value = orderId;
    }
};

// Add these new refs
const accepting = ref(false);
const rejecting = ref(false);
const selectedOrderId = ref(null);

// Direct accept function - Update to set status to 'rented' (Current)
const acceptOrderDirect = (order) => {
    if (!order.branch_id) {
        alert('Please assign this order to a branch first');
        return;
    }

    selectedOrderId.value = order.id;
    accepting.value = true;

    router.post(route('vendor.orders.rental.accept', order.id), {
        branch_id: order.branch_id,
        status: 'rented' // Add this to set status to Current
    }, {
        preserveScroll: true,
        onSuccess: () => {
            accepting.value = false;
            selectedOrderId.value = null;
        },
        onError: (errors) => {
            accepting.value = false;
            selectedOrderId.value = null;
            console.error('Error accepting order:', errors);
        }
    });
};



// Update the rejectOrderDirect function
const rejectOrderDirect = async (order) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('You are about to reject this order and mark it as Finished.'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: t('Yes, reject it!'),
        cancelButtonText: t('Cancel')
    });

    if (result.isConfirmed) {
        selectedOrderId.value = order.id;
        rejecting.value = true;

        try {
            await router.post(route('vendor.orders.rental.reject', order.id), {
                status: 'available'
            }, {
                preserveScroll: true
            });

            Swal.fire(
                t('Rejected!'),
                t('The order has been marked as Finished.'),
                'success'
            );
        } catch (errors) {
            Swal.fire(
                t('Error!'),
                t('Something went wrong while rejecting the order.'),
                'error'
            );
            console.error('Error rejecting order:', errors);
        } finally {
            rejecting.value = false;
            selectedOrderId.value = null;
        }
    }
};

// Close dropdown when clicking outside
const onClickOutside = (event) => {
    if (activeDropdown.value && !event.target.closest('.relative.inline-block')) {
        activeDropdown.value = null;
    }
};

onMounted(() => {
    document.addEventListener('click', onClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', onClickOutside);
});
</script>

<style>
/* Ensure no duplicate overlays */
.modal-overlay {
    display: none !important;
}

.menue {
    background-color: inherit !important;
    border: none !important;
}
</style>
