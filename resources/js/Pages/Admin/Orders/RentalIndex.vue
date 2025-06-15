<template>

    <Head title="Rental Orders" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Rental Orders') }}
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
                                    <option value="rejected">{{ $t('Rejected') }}</option>
                                    <option value="rented">{{ $t('rented') }}</option>
                                </select>
                                <button @click="resetFilters"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    {{ $t('Reset') }}
                                </button>
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
                                            {{ order.gold_piece?.name || 'N/A' }}
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
                                                    @click="rejectOrder(order)"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors duration-200">
                                                    {{ $t('Reject') }}
                                                </button>
                                                <!-- <button v-if="order.status === 'approved'"
                                                    @click="updateStatus(order.id, 'piece_sent')"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                                    {{ $t('Mark as Sent') }}
                                                </button> -->
                                                <!-- <button v-if="order.status === 'piece_sent'"
                                                    @click="updateStatus(order.id, 'rented')"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700 transition-colors duration-200">
                                                    {{ $t('Mark as Rented') }}
                                                </button> -->
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
                                <select v-model="acceptForm.branch_id" required
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
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import Swal from 'sweetalert2';

import { useI18n } from 'vue-i18n';

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

const applyFilters = () => {
    form.get(route('admin.orders.rental.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.branch_id = '';
    form.status = '';

    router.get(route('admin.orders.rental.index'), {}, {
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
    if (!selectedOrder.value) return;

    acceptForm.post(route('admin.orders.rental.accept', selectedOrder.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeAcceptModal();
        },
    });
};

const rejectOrder = async (order) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('You are about to reject this rental order. This action cannot be undone.'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: t('Yes, reject it!'),
        cancelButtonText: t('Cancel'),
        reverseButtons: true,
    });

    if (result.isConfirmed) {
        try {
            await router.post(route('admin.orders.rental.reject', order.id), {}, {
                preserveScroll: true,
            });

            Swal.fire({
                title: t('Rejected!'),
                text: t('The rental order has been rejected.'),
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
            });
        } catch (error) {
            Swal.fire({
                title: t('Error!'),
                text: t('Something went wrong while rejecting the order.'),
                icon: 'error',
            });
        }
    }
};

const updateStatus = (orderId, status) => {
    if (!confirm(t('Are you sure you want to update this order status?'))) return;

    router.patch(route('admin.orders.rental.update-status', orderId), {
        status: status
    }, {
        preserveScroll: true,
    });
};
</script>
