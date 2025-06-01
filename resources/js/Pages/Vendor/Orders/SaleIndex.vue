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
                                    <option value="pending_approval">{{ $t('Pending Approval') }}</option>
                                    <option value="approved">{{ $t('Approved') }}</option>
                                    <option value="piece_sent">{{ $t('Piece Sent') }}</option>
                                    <option value="rented">{{ $t('Rented') }}</option>
                                    <option value="available">{{ $t('Available') }}</option>
                                    <option value="sold">{{ $t('Sold') }}</option>
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
                                    <tr v-for="order in saleOrders.data" :key="order.id">
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
                                                {{ formatStatus(order.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <button v-if="order.status === 'pending_approval'"
                                                    @click="openAcceptModal(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors duration-200">
                                                    {{ $t('Accept') }}
                                                </button>
                                                <button v-if="order.status === 'pending_approval'"
                                                    @click="rejectOrder(order.id)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors duration-200">
                                                    {{ $t('Reject') }}
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

        <!-- Piece Details Modal -->
        <Modal :show="showDetailsModal" @close="closeDetailsModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div
                    class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Gold Piece Details') }}</h2>
                        <div v-if="selectedOrder" class="mt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p><strong>{{ $t('Name') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.name ?
                                            selectedOrder.gold_piece.name :
                                            'N/A' }}
                                    </p>
                                    <p><strong>{{ $t('Description') }}:</strong>
                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.description ?
                                            selectedOrder.gold_piece.description :
                                            'N/A' }}
                                    </p>
                                    <p><strong>{{ $t('Type') }}:</strong> {{ selectedOrder.goldPiece?.type === 'rent' ?
                                        $t('Rental') : $t('Sale') }}</p>
                                    <p><strong>{{ $t('Price') }}:</strong> {{ selectedOrder.total_price }} {{ $t('SAR')
                                    }}</p>
                                    <p><strong>{{ $t('Weight') }}:</strong>

                                        {{ selectedOrder.gold_piece && selectedOrder.gold_piece.weight ?
                                            selectedOrder.gold_piece.weight :
                                            'N/A' }}
                                        {{
                                            $t('grams') }}</p>
                                    <p><strong>{{ $t('User') }}:</strong> {{ selectedOrder.user?.name || 'N/A' }}</p>
                                    <p><strong>{{ $t('Status') }}:</strong> {{ formatStatus(selectedOrder.status) }}</p>
                                </div>
                                <div>
                                    <p><strong>{{ $t('Images') }}:</strong></p>
                                    <div v-if="selectedOrder.goldPiece?.images?.length" class="flex flex-wrap gap-2">
                                        <img v-for="image in selectedOrder.goldPiece.images" :key="image.id"
                                            :src="'/storage/' + image.path" class="h-20 w-20 object-cover rounded"
                                            alt="Gold piece image" />
                                    </div>
                                    <p v-else>{{ $t('No images available') }}</p>
                                    <p class="mt-4"><strong>{{ $t('QR Code') }}:</strong></p>
                                    <img v-if="selectedOrder.goldPiece?.qr_code"
                                        :src="'data:image/png;base64,' + selectedOrder.goldPiece.qr_code"
                                        class="h-24 w-24" alt="QR Code" />
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
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
const selectedOrder = ref(null);
const acceptForm = useForm({
    branch_id: '',
});

const showDetailsModal = ref(false);

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
        replace: true
    });
};

const getStatusClass = (status) => {
    switch (status) {
        case 'pending_approval':
            return 'bg-yellow-100 text-yellow-800';
        case 'approved':
            return 'bg-green-100 text-green-800';
        case 'piece_sent':
            return 'bg-blue-100 text-blue-800';
        case 'available':
            return 'bg-indigo-100 text-indigo-800';
        case 'rented':
            return 'bg-purple-100 text-purple-800';
        case 'sold':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const formatStatus = (status) => {
    const statusMap = {
        'pending_approval': t('Pending Approval'),
        'approved': t('Approved'),
        'piece_sent': t('Piece Sent'),
        'rented': t('Rented'),
        'available': t('Available'),
        'sold': t('Sold'),
        'rejected': t('rejected'),
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

// const rejectOrder = (orderId) => {
//     if (confirm($t('Are you sure you want to reject this order?'))) {
//         form.post(route('vendor.orders.sales.reject', orderId), {
//             preserveScroll: true,
//         });
//     }

const rejectOrder = (orderId) => {
    if (confirm(t('Are you sure you want to reject this order?'))) {
        router.post(route('vendor.orders.sales.reject', orderId), {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
            onError: (errors) => {
                console.error('Error rejecting order:', errors);
            }
        });
    }

};

const showDetails = (order) => {
    selectedOrder.value = order;
    showDetailsModal.value = true;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedOrder.value = null;
};
</script>

<style>
/* Ensure no duplicate overlays */
.modal-overlay {
    display: none !important;
}
</style>
