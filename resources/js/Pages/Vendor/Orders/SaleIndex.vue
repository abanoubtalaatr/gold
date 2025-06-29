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
                                    <option value="approved">{{ $t('Approved') }}</option>
                                    <option value="vendor_already_take_the_piece">{{ $t('Vendor Already Take The Piece') }}</option>
                                    <option value="piece_sent">{{ $t('Piece Sent') }}</option>
                                    <option value="sold">{{ $t('Sold') }}</option>
                                    <option value="rejected">{{ $t('Rejected') }}</option>
                                </select>
                                <button @click="resetFilters"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    {{ $t('Reset') }}
                                </button>
                            </div>
                        </div>

                        <!-- Sale Orders -->
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Sale Orders') }}</h3>
                        <div v-if="saleOrders?.data?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Order ID') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('User') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Gold Piece') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Branch') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Price') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="order in saleOrders.data" :key="order.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.id }}</td>
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
                                                {{ order.gold_piece && order.gold_piece.name ? order.gold_piece.name : '--' }}
                                            </strong>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.branch?.name || '--' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.total_price }} {{ $t('SAR') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(order.status)]">
                                                {{ formatStatus(order.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <button v-if="order.status === 'pending_approval'" @click="openAcceptModal(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors duration-200">
                                                    {{ $t('Accept') }}
                                                </button>
                                                <button v-if="order.status === 'pending_approval'" @click="openRejectModal(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors duration-200">
                                                    {{ $t('Reject') }}
                                                </button>
                                                <button v-if="order.status === 'approved' || order.status === 'sold'" @click="markAsSold(order.id)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700 transition-colors duration-200">
                                                    {{ $t('confirm_sold_from_vendor') }}
                                                </button>
                                                <!-- Edit Price Button -->
                                                <button v-if="order.status !== 'sold' && order.status !== 'rejected'" @click="openEditPriceModal(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                                    {{ $t('Edit Price') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <Pagination :links="saleOrders.links" class="mt-6" />
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <p class="text-xl font-semibold">{{ $t('No sale orders found.') }}</p>
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
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Accept Order') }}</h2>
                        <p class="mb-4 text-sm text-gray-600">{{ $t('Select a branch for this order.') }}</p>

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

        <!-- Reject Order Modal -->
        <Modal :show="showRejectModal" @close="closeRejectModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg w-full">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-center">
                            <h2 class="text-lg font-medium text-gray-900 mb-2">{{ $t('Reject Order') }}</h2>
                            <p class="mb-4 text-sm text-gray-600">
                                {{ $t('Are you sure you want to reject this order? This action cannot be undone.') }}
                            </p>
                            <div v-if="selectedOrder" class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="text-sm text-gray-600">
                                    <p><strong>{{ $t('Order ID') }}:</strong> {{ selectedOrder.id }}</p>
                                    <p><strong>{{ $t('User') }}:</strong> {{ selectedOrder.user?.name || '--' }}</p>
                                    <p><strong>{{ $t('Gold Piece') }}:</strong> {{ selectedOrder.gold_piece?.name || '--' }}</p>
                                    <p><strong>{{ $t('Price') }}:</strong> {{ selectedOrder.total_price }} {{ $t('SAR') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" @click="closeRejectModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200">
                                {{ $t('Cancel') }}
                            </button>
                            <button @click="rejectOrder"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors duration-200">
                                {{ $t('Reject Order') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Piece Details Modal -->
        <Modal :show="showDetailsModal" @close="closeDetailsModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Gold Piece Details') }}</h2>
                        <div v-if="selectedOrder" class="mt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p><strong>{{ $t('Name') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.name ? selectedOrder.gold_piece.name : '--' }}
                                    </p>
                                    <p v-if="selectedOrder.gold_piece && selectedOrder.gold_piece.description">
                                        <strong>{{ $t('Description') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.description ? selectedOrder.gold_piece.description : '--' }}
                                    </p>
                                    <p>
                                        <strong>{{ $t('Carat') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.carat ? selectedOrder.gold_piece.carat : '--' }}
                                    </p>
                                    <p v-if="selectedOrder && selectedOrder.gold_piece && selectedOrder.gold_piece.is_including_lobes">
                                        <strong>{{ $t('Is Including Lobe') }}:</strong>
                                        {{ selectedOrder.gold_piece.is_including_lobes ? $t('Yes') : $t('No') }}
                                    </p>
                                    <p v-if="selectedOrder && selectedOrder.status">
                                        <strong>{{ $t('Status') }}:</strong>
                                        {{ $t(selectedOrder.status) }}
                                    </p>
                                    <p v-if="selectedOrder.gold_piece && selectedOrder.gold_piece.sale_price">
                                        <strong>{{ $t('Sale Price') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.sale_price ? selectedOrder.gold_piece.sale_price : '--' }}
                                    </p>
                                    <p>
                                        <strong>{{ $t('Weight') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.weight ? selectedOrder.gold_piece.weight : '--' }} {{ $t('grams') }}
                                    </p>
                                    <p>
                                        <strong>{{ $t('User') }}:</strong> {{ selectedOrder.user?.name || '--' }}
                                    </p>
                                </div>
                                <div>
                                    <p><strong>{{ $t('Images') }}:</strong></p>
                                    <div v-if="selectedOrder.gold_piece?.media?.length" class="flex flex-wrap gap-2">
                                        <img v-for="media in selectedOrder.gold_piece.media.filter(m => m.collection_name === 'images')" :key="media.id"
                                            :src="media.original_url" class="h-20 w-20 object-cover rounded" alt="Gold piece image" />
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

        <!-- Edit Price Modal -->
        <Modal :show="showEditPriceModal" @close="closeEditPriceModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg w-full">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Edit Order Price') }}</h2>
                        <p class="mb-4 text-sm text-gray-600">{{ $t('Update the price for the selected order.') }}</p>

                        <form @submit.prevent="updatePrice">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Price (SAR)') }}</label>
                                <input v-model="editPriceForm.total_price" type="number" step="0.01" min="0"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Enter new price" />
                                <p v-if="editPriceForm.errors.total_price" class="mt-1 text-sm text-red-600">
                                    {{ editPriceForm.errors.total_price }}
                                </p>
                            </div>

                            <div class="flex justify-end space-x-3 pt-4">
                                <button type="button" @click="closeEditPriceModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200">
                                    {{ $t('Cancel') }}
                                </button>
                                <button type="submit" :disabled="editPriceForm.processing"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                    {{ editPriceForm.processing ? $t('Processing...') : $t('Update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce';
import { useI18n } from 'vue-i18n';

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
const showRejectModal = ref(false);
const showDetailsModal = ref(false);
const showEditPriceModal = ref(false);
const selectedOrder = ref(null);

const acceptForm = useForm({
    branch_id: '',
});

const editPriceForm = useForm({
    total_price: '',
});

const debouncedSearch = debounce(() => {
    form.get(route('vendor.orders.sale.index'), {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);

const applyFilters = () => {
    form.get(route('vendor.orders.sale.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.branch_id = '';
    form.status = '';

    router.get(route('vendor.orders.sale.index'), {}, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const getStatusClass = (status) => {
    switch (status) {
        case 'pending_approval':
            return 'bg-yellow-100 text-yellow-800';
        case 'approved':
            return 'bg-green-100 text-green-800';
        case 'vendor_already_take_the_piece':
            return 'bg-orange-100 text-orange-800';
        case 'piece_sent':
            return 'bg-blue-100 text-blue-800';
        case 'confirm_sold_from_vendor':
            return 'bg-gray-100 text-gray-800';
        case 'rejected':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const formatStatus = (status) => {
    const statusMap = {
        pending_approval: t('Pending Approval'),
        approved: t('Approved'),
        vendor_already_take_the_piece: t('Vendor Already Take The Piece'),
        piece_sent: t('Piece Sent'),
        sold: t('Sold'),
        rejected: t('Rejected'),
        confirm_sold_from_vendor: t('Confirm Sold From Vendor'),
    };

    return statusMap[status] || status
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
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
    acceptForm.post(route('vendor.orders.sales.accept', selectedOrder.value.id), {
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
    router.post(route('vendor.orders.sales.reject', selectedOrder.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            closeRejectModal();
        },
        onError: (errors) => {
            console.error('Error rejecting order:', errors);
        },
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

const openEditPriceModal = (order) => {
    selectedOrder.value = order;
    editPriceForm.total_price = order.total_price;
    showEditPriceModal.value = true;
};

const closeEditPriceModal = () => {
    showEditPriceModal.value = false;
    selectedOrder.value = null;
    editPriceForm.reset();
};

const updatePrice = () => {
    editPriceForm.post(route('vendor.orders.sales.update-price', selectedOrder.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditPriceModal();
        },
        onError: (errors) => {
            console.error('Error updating price:', errors);
        },
    });
};

const markAsSent = (orderId) => {
    router.post(route('vendor.orders.sales.mark-sent', orderId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        },
        onError: (errors) => {
            console.error('Error marking order as sent:', errors);
        },
    });
};

const markAsSold = (orderId) => {
    router.post(route('vendor.orders.sales.mark-sold', orderId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        },
        onError: (errors) => {
            console.error('Error marking order as sold:', errors);
        },
    });
};

const markAsTaken = (orderId) => {
    router.post(route('vendor.orders.sales.mark-taken', orderId), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        },
        onError: (errors) => {
            console.error('Error marking order as taken:', errors);
        },
    });
};
</script>

<style>
/* Ensure no duplicate overlays */
.modal-overlay {
    display: none !important;
}
</style>