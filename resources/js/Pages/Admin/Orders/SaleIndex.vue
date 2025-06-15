<template>

    <Head title="Sale Orders" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Sale Orders') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Filters -->
                        <div class="mb-6 space-y-4">
                            <div class="flex flex-wrap items-center gap-4">
                                <div class="flex-1 min-w-[250px]">
                                    <input v-model="form.search" type="text"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :placeholder="$t('Search by user or piece name...')" />
                                </div>

                                <select v-model="form.branch_id"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">{{ $t('All Branches') }}</option>
                                    <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                        {{ branch.name }}
                                    </option>
                                </select>

                                <select v-model="form.status"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">{{ $t('All Statuses') }}</option>
                                    <option v-for="(value, key) in statusOptions" :value="key">
                                        {{ $t(value) }}
                                    </option>
                                </select>

                                <button @click="applyFilters"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                    {{ $t('Apply') }}
                                </button>

                                <button @click="resetFilters"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                    {{ $t('Reset') }}
                                </button>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <div v-if="saleOrders?.data?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Order ID') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('User') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Gold Piece') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Branch') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Order Date') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Price') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Status') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="order in saleOrders.data" :key="order.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <div>
                                                    {{ order.user?.name || 'N/A' }}<br />
                                                    {{ order.user?.email || 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ order.gold_piece?.name || '--' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.branch?.name || '--' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(order.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.total_price }} {{ $t('SAR') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(order.status).class]">
                                                {{ $t(getStatusClass(order.status).text) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <template v-for="action in order.allowed_actions">
                                                <button v-if="action === 'accept'" @click="acceptOrder(order)"
                                                    class="px-3 py-1 text-sm text-white bg-green-600 rounded hover:bg-green-700">
                                                    {{ $t('Accept') }}
                                                </button>
                                                <button v-if="action === 'reject'" @click="rejectOrder(order)"
                                                    class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                                                    {{ $t('Reject') }}
                                                </button>
                                                <button v-if="action === 'mark_as_sent'" @click="markAsSent(order)"
                                                    class="px-3 py-1 text-sm text-white bg-blue-600 rounded hover:bg-blue-700">
                                                    {{ $t('Mark as Sent') }}
                                                </button>
                                                <button v-if="action === 'mark_as_sold'" @click="markAsSold(order)"
                                                    class="px-3 py-1 text-sm text-white bg-purple-600 rounded hover:bg-purple-700">
                                                    {{ $t('Mark as Sold') }}
                                                </button>
                                            </template>
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
        <Modal :show="showAcceptModal" @close="showAcceptModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ $t('Accept Order') }} #{{ selectedOrder?.id }}
                </h2>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Select Branch') }}</label>
                    <select v-model="acceptBranchId"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                            {{ branch.name }}
                        </option>
                    </select>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="showAcceptModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 mr-3">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="confirmAccept" :disabled="!acceptBranchId"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50">
                        {{ $t('Confirm') }}
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Reject Order Modal -->
        <Modal :show="showRejectModal" @close="showRejectModal = false">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">
                        {{ $t('Reject Order') }}
                    </h2>
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

                <div class="mt-6 flex justify-end">
                    <button @click="showRejectModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 mr-3">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="confirmReject"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                        {{ $t('Reject Order') }}
                    </button>
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
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
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

const statusOptions = {
    'pending_approval': 'Pending Approval',
    'approved': 'Approved',
    'piece_sent': 'Piece Sent',
    'sold': 'Sold',
    'rejected': 'Rejected'
};

const form = useForm({
    search: props.filters.search || '',
    branch_id: props.filters.branch_id || '',
    status: props.filters.status || '',
});

const showAcceptModal = ref(false);
const selectedOrder = ref(null);
const acceptBranchId = ref('');

const acceptOrder = (order) => {
    selectedOrder.value = order;
    showAcceptModal.value = true;
};

const confirmAccept = () => {
    if (!acceptBranchId.value) return;

    router.post(route('admin.orders.sale.accept', selectedOrder.value.id), {
        branch_id: acceptBranchId.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showAcceptModal.value = false;
            acceptBranchId.value = '';
        }
    });
};

const showRejectModal = ref(false);
// const selectedOrder = ref(null);

const rejectOrder = (order) => {
    selectedOrder.value = order;
    showRejectModal.value = true;
};

const confirmReject = () => {
    router.post(route('admin.orders.sale.reject', selectedOrder.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            showRejectModal.value = false;
        }
    });
};

const markAsSent = (order) => {
    // if (confirm(t('Are you sure you want to mark this order as sent?'))) {
    router.post(route('admin.orders.sale.markAsSent', order.id), {}, {
        preserveScroll: true
    });
    // }
};

const markAsSold = (order) => {
    // if (confirm(t('Are you sure you want to mark this order as sold?'))) {
    router.post(route('admin.orders.sale.markAsSold', order.id), {}, {
        preserveScroll: true
    });
    // }
};

const applyFilters = () => {
    form.get(route('admin.orders.sale.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.reset();
    router.get(route('admin.orders.sale.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const getStatusClass = (status) => {
    switch (status) {
        case 'pending_approval':
            return {
                class: 'bg-yellow-100 text-yellow-800',
                text: 'Pending Approval'
            };
        case 'approved':
            return {
                class: 'bg-blue-100 text-blue-800',
                text: 'Approved'
            };
        case 'piece_sent':
            return {
                class: 'bg-indigo-100 text-indigo-800',
                text: 'Piece Sent'
            };
        case 'sold':
            return {
                class: 'bg-green-100 text-green-800',
                text: 'Sold'
            };
        case 'rejected':
            return {
                class: 'bg-red-100 text-red-800',
                text: 'Rejected'
            };
        default:
            return {
                class: 'bg-gray-100 text-gray-800',
                text: status
            };
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};
</script>
