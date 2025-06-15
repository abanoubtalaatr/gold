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
                        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 w-full">
                                <div class="md:w-full">
                                    <select v-model="form.branch_id"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters">
                                        <option value="">{{ $t('All Branches') }}</option>
                                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                            {{ branch.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="md:w-full">
                                    <select v-model="form.status"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters">
                                        <option value="">{{ $t('All Statuses') }}</option>
                                        <option v-for="status in statuses" :key="status" :value="status">
                                            {{ formatStatus(status) }}
                                        </option>
                                    </select>
                                </div>
                                <div class="md:w-full">
                                    <select v-model="form.date_filter"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters">
                                        <option value="">{{ $t('All Dates') }}</option>
                                        <option value="today">{{ $t('Today') }}</option>
                                        <option value="week">{{ $t('This Week') }}</option>
                                        <option value="custom">{{ $t('Custom Range') }}</option>
                                    </select>
                                </div>
                                <div v-if="form.date_filter === 'custom'" class="md:w-full">
                                    <input v-model="form.date_from" type="date"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters" />
                                </div>
                                <div v-if="form.date_filter === 'custom'" class="md:w-full">
                                    <input v-model="form.date_to" type="date"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        @change="applyFilters" />
                                </div>
                                <div class="md:w-full">
                                    <button @click="resetFilters"
                                        class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        {{ $t('Reset') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <div v-if="orders?.data?.length > 0" class="overflow-x-auto">
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
                                            {{ $t('Start Date') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('End Date') }}
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
                                    <tr v-for="order in orders.data" :key="order.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center">
                                                <img v-if="order.user?.avatar" :src="order.user.avatar"
                                                    class="h-8 w-8 rounded-full object-cover mr-2" alt="User avatar" />
                                                <div>
                                                    {{ order.user?.name || 'N/A' }}<br />
                                                    {{ order.user?.mobile || 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <button @click="showGoldPieceDetails(order.gold_piece)"
                                                class="text-indigo-600 p-2 rounded hover:text-indigo-800 hover:underline font-medium">
                                                {{ order.gold_piece?.name || 'N/A' }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.branch?.name || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.start_date ? formatDate(order.start_date, true) : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.end_date ? formatDate(order.end_date, true) : 'N/A' }}
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
                                            <div class=" items-center space-x-2 flex-wrap">
                                                <button v-if="order.allowed_actions?.includes('approve')"
                                                    @click="acceptOrder(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors duration-200">
                                                    {{ $t('Accept') }}
                                                </button>
                                                <button v-if="order.allowed_actions?.includes('reject')"
                                                    @click="openRejectModal(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors duration-200">
                                                    {{ $t('Reject') }}
                                                </button>
                                                <button v-if="order.allowed_actions?.includes('mark_as_sent')"
                                                    @click="markAsSent(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                                    {{ $t('Mark as Sent') }}
                                                </button>
                                                <button v-if="order.allowed_actions?.includes('confirm_rental')"
                                                    @click="confirmRental(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700 transition-colors duration-200">
                                                    {{ $t('Confirm Rental') }}
                                                </button>
                                            </div>
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

        <!-- Reject Modal -->
        <Modal :show="showRejectModal" @close="closeRejectModal">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
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
                            <p><strong>{{ $t('User') }}:</strong> {{ selectedOrder.user?.name || 'N/A' }}</p>
                            <p><strong>{{ $t('Gold Piece') }}:</strong> {{ selectedOrder.gold_piece?.name || 'N/A' }}
                            </p>
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
        </Modal>

        <!-- Gold Piece Details Modal -->
        <Modal :show="showGoldPieceModal" @close="closeGoldPieceModal" max-width="4xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-medium text-gray-900">{{ $t('Gold Piece Details') }}</h2>
                    <button @click="closeGoldPieceModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div v-if="selectedGoldPiece" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Basic Information') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $t('Name') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.name || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $t('Weight') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.weight || 'N/A' }} {{ $t('grams')
                                    }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $t('Carat') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.carat || 'N/A' }}</p>
                            </div>
                            <div v-if="selectedGoldPiece.price_per_day" class="md:col-span-2">
                                <p class="text-sm font-medium text-gray-700">{{ $t('Daily Rental Price') }}</p>
                                <p class="text-sm text-gray-900">{{ selectedGoldPiece.price_per_day }} {{ $t('SAR')
                                    }}/{{
                                    $t('day') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div v-if="selectedGoldPiece.description" class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Description') }}</h3>
                        <p class="text-sm text-gray-900">{{ selectedGoldPiece.description }}</p>
                    </div>

                    <!-- Images Gallery -->
                    <div v-if="selectedGoldPiece.media?.length" class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Images Gallery') }}</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div v-for="media in selectedGoldPiece.media.filter(m => m.collection_name === 'images')"
                                :key="media.id" class="relative group cursor-pointer" @click="openImageModal(media)">
                                <img :src="media.original_url"
                                    class="w-full h-32 object-cover rounded-lg border hover:border-indigo-500 transition-colors duration-200"
                                    :alt="`${selectedGoldPiece.name} image`" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="closeGoldPieceModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
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
import { useI18n } from 'vue-i18n';
import Swal from 'sweetalert2';

const { t } = useI18n();

const props = defineProps({
    orders: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    branches: {
        type: Array,
        default: () => [],
    },
    statuses: {
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
    date_filter: props.filters.date_filter || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const showRejectModal = ref(false);
const showGoldPieceModal = ref(false);
const selectedOrder = ref(null);
const selectedGoldPiece = ref(null);

const applyFilters = () => {
    form.get(route('admin.rental-requests.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.branch_id = '';
    form.status = '';
    form.date_filter = '';
    form.date_from = '';
    form.date_to = '';

    router.get(route('admin.rental-requests.index'), {}, {
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
        case 'rented':
            return 'bg-purple-100 text-purple-800';
        case 'rejected':
            return 'bg-red-100 text-red-800';
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
        'rejected': t('Rejected'),
    };
    return statusMap[status] || status;
};

const formatDate = (date, dateOnly = false) => {
    if (!date) return 'N/A';
    const dateObj = new Date(date);
    if (dateOnly) {
        return dateObj.toLocaleDateString();
    }
    return dateObj.toLocaleString();
};

const acceptOrder = async (order) => {
    const result = await Swal.fire({
        title: t('Accept Order'),
        text: t('Are you sure you want to accept this order?'),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: t('Yes, accept it!'),
        cancelButtonText: t('Cancel')
    });

    if (result.isConfirmed) {
        router.post(route('admin.rental-requests.accept', order.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire(
                    t('Accepted!'),
                    t('The order has been accepted.'),
                    'success'
                );
            },
            onError: () => {
                Swal.fire(
                    t('Error!'),
                    t('Something went wrong.'),
                    'error'
                );
            }
        });
    }
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
    router.post(route('admin.rental-requests.reject', selectedOrder.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            closeRejectModal();
            Swal.fire(
                t('Rejected!'),
                t('The order has been rejected.'),
                'success'
            );
        },
        onError: () => {
            Swal.fire(
                t('Error!'),
                t('Something went wrong.'),
                'error'
            );
        }
    });
};

const markAsSent = async (order) => {
    const result = await Swal.fire({
        title: t('Mark as Sent'),
        text: t('Are you sure you want to mark this order as sent?'),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: t('Yes, mark as sent!'),
        cancelButtonText: t('Cancel')
    });

    if (result.isConfirmed) {
        router.post(route('admin.rental-requests.mark-sent', order.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire(
                    t('Marked as Sent!'),
                    t('The order has been marked as sent.'),
                    'success'
                );
            },
            onError: () => {
                Swal.fire(
                    t('Error!'),
                    t('Something went wrong.'),
                    'error'
                );
            }
        });
    }
};

const confirmRental = async (order) => {
    const result = await Swal.fire({
        title: t('Confirm Rental'),
        text: t('Are you sure you want to confirm this rental?'),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: t('Yes, confirm rental!'),
        cancelButtonText: t('Cancel')
    });

    if (result.isConfirmed) {
        router.post(route('admin.rental-requests.confirm-rental', order.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire(
                    t('Confirmed!'),
                    t('The rental has been confirmed.'),
                    'success'
                );
            },
            onError: () => {
                Swal.fire(
                    t('Error!'),
                    t('Something went wrong.'),
                    'error'
                );
            }
        });
    }
};

const showGoldPieceDetails = (goldPiece) => {
    selectedGoldPiece.value = goldPiece;
    showGoldPieceModal.value = true;
};

const closeGoldPieceModal = () => {
    showGoldPieceModal.value = false;
    selectedGoldPiece.value = null;
};
</script>
