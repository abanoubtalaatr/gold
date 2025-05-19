<template>
    <Head title="Rental Requests" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Rental Requests') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Filters -->
                        <!-- Filters -->
<div class="flex flex-wrap items-center justify-between mb-6 gap-4">
    <!-- Search Input (Full Width) -->
    <div class="w-full">
        <input
            v-model="form.search"
            type="text"
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            :placeholder="$t('Search by user or piece name...')"
            @input="debouncedSearch"
        />
    </div>
    <!-- Other Filters (3 Columns Each on Medium Screens) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 w-full">
        <div class="md:w-full">
            <select
                v-model="form.branch_id"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
            >
                <option value="">{{ $t('All Branches') }}</option>
                <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                    {{ branch.name }}
                </option>
            </select>
        </div>
        <div class="md:w-full">
            <select
                v-model="form.status"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
            >
                <option value="">{{ $t('All Statuses') }}</option>
                <option v-for="status in statuses" :key="status" :value="status">
                    {{ formatStatus(status) }}
                </option>
            </select>
        </div>
        <div class="md:w-full">
            <input
                v-model="form.piece_name"
                type="text"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                :placeholder="$t('Piece Name')"
                @input="applyFilters"
            />
        </div>
        <div class="md:w-full">
            <input
                v-model="form.price_min"
                type="number"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                :placeholder="$t('Min Price')"
                @input="applyFilters"
            />
        </div>
        <div class="md:w-full">
            <input
                v-model="form.price_max"
                type="number"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                :placeholder="$t('Max Price')"
                @input="applyFilters"
            />
        </div>
        <div class="md:w-full">
            <select
                v-model="form.date_filter"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
            >
                <option value="">{{ $t('All Dates') }}</option>
                <option value="today">{{ $t('Today') }}</option>
                <option value="week">{{ $t('This Week') }}</option>
                <option value="custom">{{ $t('Custom Range') }}</option>
            </select>
        </div>
        <div v-if="form.date_filter === 'custom'" class="md:w-full">
            <input
                v-model="form.date_from"
                type="date"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
            />
        </div>
        <div v-if="form.date_filter === 'custom'" class="md:w-full">
            <input
                v-model="form.date_to"
                type="date"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                @change="applyFilters"
            />
        </div>
        <div class="md:w-full">
            <button
                @click="resetFilters"
                class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
                {{ $t('Reset') }}
            </button>
        </div>
    </div>
</div>

                        <!-- Orders Table -->
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Orders') }}</h3>
                        <div v-if="orders?.data?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Order ID') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Type') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('User') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Gold Piece') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Branch') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Price') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Status') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="order in orders.data" :key="order.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.order_type === 'rental' ? $t('Rental') : $t('Sale') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <img
                                                    v-if="order.user?.avatar"
                                                    :src="order.user.avatar"
                                                    class="h-8 w-8 rounded-full object-cover mr-2"
                                                    alt="User avatar"
                                                />
                                                <div>
                                                    {{ order.user?.name || 'N/A' }}<br />
                                                    {{ order.user?.email || 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <button @click="showDetails(order)" class="text-indigo-600 hover:text-indigo-800">
                                                {{ order.goldPiece?.name || 'N/A' }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.branch?.name || 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.total_price }} {{ $t('SAR') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(order.status)]">
                                                {{ formatStatus(order.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button
                                                v-if="order.invoice"
                                                @click="showInvoice(order)"
                                                class="text-blue-600 hover:text-blue-800"
                                            >
                                                {{ $t('View Invoice') }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <Pagination :links="orders.links" class="mt-6" />
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <p class="text-xl font-semibold">{{ $t('No orders found.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <Modal :show="showDetailsModal" @close="closeDetailsModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ $t('Booking Details') }}</h2>
                <div v-if="selectedOrder" class="mt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>{{ $t('Order ID') }}:</strong> {{ selectedOrder.id }}</p>
                            <p><strong>{{ $t('Order Type') }}:</strong> {{ selectedOrder.order_type === 'rental' ? $t('Rental') : $t('Sale') }}</p>
                            <p><strong>{{ $t('Created At') }}:</strong> {{ formatDate(selectedOrder.created_at) }}</p>
                            <p><strong>{{ $t('Service Type') }}:</strong> {{ selectedOrder.goldPiece?.type === 'rent' ? $t('Rental') : $t('Sale') }}</p>
                            <p><strong>{{ $t('Piece Name') }}:</strong> {{ selectedOrder.goldPiece?.name || 'N/A' }}</p>
                            <p><strong>{{ $t('Description') }}:</strong> {{ selectedOrder.goldPiece?.description || 'N/A' }}</p>
                            <p><strong>{{ $t('Weight') }}:</strong> {{ selectedOrder.goldPiece?.weight || 'N/A' }} {{ $t('grams') }}</p>
                            <p><strong>{{ $t('Carat') }}:</strong> {{ selectedOrder.goldPiece?.carat || 'N/A' }}</p>
                            <p v-if="selectedOrder.start_date"><strong>{{ $t('Rental Period') }}:</strong> {{ formatDate(selectedOrder.start_date) }} - {{ formatDate(selectedOrder.end_date) }}</p>
                            <p v-if="selectedOrder.rental_days"><strong>{{ $t('Rental Days') }}:</strong> {{ selectedOrder.rental_days }}</p>
                            <p><strong>{{ $t('Price') }}:</strong> {{ selectedOrder.total_price }} {{ $t('SAR') }}</p>
                        </div>
                        <div>
                            <p><strong>{{ $t('Status') }}:</strong> {{ formatStatus(selectedOrder.status) }}</p>
                            <p v-if="selectedOrder.pickup_date"><strong>{{ $t('Pickup Date') }}:</strong> {{ formatDate(selectedOrder.pickup_date) }}</p>
                            <p v-if="selectedOrder.delivery_date"><strong>{{ $t('Delivery Date') }}:</strong> {{ formatDate(selectedOrder.delivery_date) }}</p>
                            <p><strong>{{ $t('Branch') }}:</strong> {{ selectedOrder.branch?.name || 'N/A' }}</p>
                            <p><strong>{{ $t('User') }}:</strong> {{ selectedOrder.user?.name || 'N/A' }}</p>
                            <p><strong>{{ $t('Images') }}:</strong></p>
                            <div v-if="selectedOrder.goldPiece?.images?.length" class="flex flex-wrap gap-2">
                                <img
                                    v-for="image in selectedOrder.goldPiece.images"
                                    :key="image.id"
                                    :src="'/storage/' + image.path"
                                    class="h-20 w-20 object-cover rounded"
                                    alt="Gold piece image"
                                />
                            </div>
                            <p v-else>{{ $t('No images available') }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button
                        @click="closeDetailsModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        {{ $t('Close') }}
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Invoice Modal -->
        <Modal :show="showInvoiceModal" @close="closeInvoiceModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ $t('Electronic Invoice') }}</h2>
                <div v-if="selectedOrder?.invoice" class="mt-4">
                    <p><strong>{{ $t('Order ID') }}:</strong> {{ selectedOrder.invoice.order_id }}</p>
                    <p><strong>{{ $t('User Name') }}:</strong> {{ selectedOrder.invoice.user_name }}</p>
                    <p><strong>{{ $t('Piece Name') }}:</strong> {{ selectedOrder.invoice.piece_name }}</p>
                    <p><strong>{{ $t('Service Type') }}:</strong> {{ selectedOrder.invoice.service_type === 'rent' ? $t('Rental') : $t('Sale') }}</p>
                    <p v-if="selectedOrder.invoice.rental_days"><strong>{{ $t('Rental Days') }}:</strong> {{ selectedOrder.invoice.rental_days }}</p>
                    <p v-if="selectedOrder.invoice.start_date"><strong>{{ $t('Start Date') }}:</strong> {{ formatDate(selectedOrder.invoice.start_date) }}</p>
                    <p v-if="selectedOrder.invoice.end_date"><strong>{{ $t('End Date') }}:</strong> {{ formatDate(selectedOrder.invoice.end_date) }}</p>
                    <p><strong>{{ $t('Total Price') }}:</strong> {{ selectedOrder.invoice.total_price }} {{ $t('SAR') }}</p>
                    <p><strong>{{ $t('Branch') }}:</strong> {{ selectedOrder.invoice.branch_name }}</p>
                    <p><strong>{{ $t('Created At') }}:</strong> {{ formatDate(selectedOrder.invoice.created_at) }}</p>
                    <p><strong>{{ $t('Status') }}:</strong> {{ formatStatus(selectedOrder.invoice.status) }}</p>
                </div>
                <div class="mt-6 flex justify-end">
                    <button
                        @click="closeInvoiceModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                    >
                        {{ $t('Close') }}
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
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    orders: {
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
    statuses: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    search: props.filters.search || '',
    service_type: props.filters.service_type || '',
    branch_id: props.filters.branch_id || '',
    status: props.filters.status || '',
    piece_name: props.filters.piece_name || '',
    description: props.filters.description || '',
    price_min: props.filters.price_min || '',
    price_max: props.filters.price_max || '',
    date_filter: props.filters.date_filter || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const showDetailsModal = ref(false);
const showInvoiceModal = ref(false);
const selectedOrder = ref(null);

const debouncedSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    form.get(route('vendor.rental-requests.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.reset();
    router.visit(route('vendor.rental-requests.index'), {
        method: 'get',
        data: {},
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
    window.location.reload();
};

const getStatusClass = (status) => {
    switch (status) {
        case 'pending_approval':
            return 'bg-yellow-100 text-yellow-800';
        case 'approved':
            return 'bg-green-100 text-green-800';
        case 'piece_sent':
            return 'bg-blue-100 text-blue-800';
        case 'rented':
            return 'bg-purple-100 text-purple-800';
        case 'available':
            return 'bg-gray-100 text-gray-800';
        case 'sold':
            return 'bg-red-100 text-red-800';
        case 'payment_confirmed':
            return 'bg-blue-100 text-blue-800';
        case 'rejected':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const formatStatus = (status) => {
    
    return status
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const formatDate = (date) => {
    return date ? new Date(date).toLocaleString() : 'N/A';
};

const showDetails = (order) => {
    selectedOrder.value = order;
    showDetailsModal.value = true;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedOrder.value = null;
};

const showInvoice = (order) => {
    selectedOrder.value = order;
    showInvoiceModal.value = true;
};

const closeInvoiceModal = () => {
    showInvoiceModal.value = false;
    selectedOrder.value = null;
};
</script>