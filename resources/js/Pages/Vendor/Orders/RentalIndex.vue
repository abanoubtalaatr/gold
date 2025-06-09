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
                                    <option value="pending_approval">{{ $t('Pending Approval') }}</option>
                                    <option value="rejected">{{ $t('Rejected') }}</option>
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
                                                    {{ order.user?.name || '--' }}<br />
                                                    {{ order.user?.mobile || '--' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <strong @click="showDetails(order)"
                                                class="text-yellow-600 hover:text-yellow-200 hover:underline cursor-pointer transition-colors duration-200">
                                                {{ order.gold_piece && order.gold_piece.name ? order.gold_piece.name :
                                                    '--' }}
                                            </strong>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.branch?.name || '--' }}
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
                                                <!-- Accept button - only show for pending_approval orders -->
                                                <button
                                                    v-if="order.status === 'pending_approval'"
                                                    @click="openAcceptModal(order)" :disabled="accepting"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:opacity-50 transition-colors duration-200">
                                                    {{ accepting && selectedOrderId === order.id ? $t('Processing...') :
                                                        $t('Accept') }}
                                                </button>

                                                <!-- Reject button - only show for pending_approval orders -->
                                                <button
                                                    v-if="order.status === 'pending_approval'"
                                                    @click="rejectOrderDirect(order)" :disabled="rejecting"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50 transition-colors duration-200">
                                                    {{ rejecting && selectedOrderId === order.id ? $t('Processing...') :
                                                        $t('Reject') }}
                                                </button>
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

        <!-- Accept Order Modal -->
        <Modal :show="showAcceptModal" @close="closeAcceptModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg w-full">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Accept Rental Order') }}</h2>
                        <p class="mb-4 text-sm text-gray-600">{{ $t('Select a branch for this rental order.') }}</p>

                        <form @submit.prevent="acceptOrder">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Branch') }}</label>
                                <select v-model="acceptForm.branch_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="" disabled>{{ $t('Select a branch') }}</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        {{ branch.name }}
                                    </option>
                                </select>
                                <p v-if="acceptForm.errors.branch_id" class="mt-1 text-sm text-red-600">
                                    {{ acceptForm.errors.branch_id }}
                                </p>
                            </div>

                            <div class="flex justify-end space-x-3 pt-4">
                                <button type="button" @click="closeAcceptModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200">
                                    {{ $t('Cancel') }}
                                </button>
                                <button type="submit" :disabled="acceptForm.processing"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                    {{ acceptForm.processing ? $t('Processing...') : $t('Accept') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Order Details Modal -->
        <Modal :show="showDetailsModal" @close="closeDetailsModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div
                    class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Gold Piece Details') }}</h2>
                        <div v-if="selectedOrder" class="mt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                <p><strong>{{ $t('id') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.id ?
                                            selectedOrder.gold_piece.id :
                                        '--' }}
                                    </p>
                                    <p><strong>{{ $t('Name') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.name ?
                                            selectedOrder.gold_piece.name :
                                        '--' }}
                                    </p>
                                    <p><strong>{{ $t('Description') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.description ?
                                            selectedOrder.gold_piece.description :
                                        '--' }}
                                    </p>
                                    <p><strong>{{ $t('Type') }}:</strong> {{ selectedOrder.goldPiece?.type === 'rent' ?
                                        $t('Rental') : $t('Sale') }}</p>
                                    <p><strong>{{ $t('Price') }}:</strong> {{ selectedOrder.total_price }} {{ $t('SAR')
                                        }}</p>
                                    <p><strong>{{ $t('Weight') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.weight ?
                                            selectedOrder.gold_piece.weight :
                                        '--' }}
                                        {{
                                            $t('grams') }}</p>
                                    <p><strong>{{ $t('User') }}:</strong> {{ selectedOrder.user?.name || '--' }}</p>
                                    <p><strong>{{ $t('Status') }}:</strong> {{ getDisplayStatus(selectedOrder.status) }}
                                    </p>
                                </div>
                                <div>
                                    <p><strong>{{ $t('Images') }}:</strong></p>
                                    <div v-if="selectedOrder.gold_piece?.media?.length" class="flex flex-wrap gap-2">
                                        <img v-for="media in selectedOrder.gold_piece.media.filter(m => m.collection_name === 'images')" :key="media.id"
                                            :src="media.original_url" class="h-20 w-20 object-cover rounded"
                                            alt="Gold piece image" />
                                    </div>
                                    <p v-else>{{ $t('No images available') }}</p>
                                    <p class="mt-4"><strong>{{ $t('QR Code') }}:</strong></p>
                                    <img v-if="selectedOrder.gold_piece?.qr_code"
                                        :src="selectedOrder.gold_piece.qr_code" class="h-24 w-24" alt="QR Code" />
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button @click="closeDetailsModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200">
                                {{ $t('Close') }}
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
        'pending_approval': t('Pending Approval'),
        'approved': t('Approved'),
        'piece_sent': t('Piece Sent'),
        'rented': t('Rented'),
        'rejected': t('Rejected'),
    };
    return statusMap[status] || status;
};

const getStatusClass = (status) => {
    const statusClasses = {
        'pending_approval': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-blue-100 text-blue-800',
        'piece_sent': 'bg-purple-100 text-purple-800',
        'rented': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
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

    selectedOrderId.value = orderId;
    updating.value = true;

    router.patch(route('vendor.orders.rental.updateStatus', orderId), {
        status: status
    }, {
        preserveScroll: true,
        onSuccess: () => {
            updating.value = false;
            selectedOrderId.value = null;
        },
        onError: (errors) => {
            updating.value = false;
            selectedOrderId.value = null;
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

// Add these new refs
const accepting = ref(false);
const rejecting = ref(false);
const updating = ref(false);
const selectedOrderId = ref(null);

// Update the rejectOrderDirect function
const rejectOrderDirect = async (order) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('You are about to reject this order.'),
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
                status: 'rejected' // Changed from 'available' to 'rejected'
            }, {
                preserveScroll: true
            });

            Swal.fire(
                t('Rejected!'),
                t('The order has been rejected.'),
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

onMounted(() => {
    // Remove dropdown event listener
    // document.addEventListener('click', onClickOutside);
});

onUnmounted(() => {
    // Remove dropdown event listener
    // document.removeEventListener('click', onClickOutside);
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
