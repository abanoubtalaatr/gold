<template>

    <Head title="Orders Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Orders Management') }}
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

                                <!-- <select v-model="form.type"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">{{ $t('All Types') }}</option>
                                    <option value="rent">{{ $t('Rental') }}</option>
                                    <option value="sale">{{ $t('Sale') }}</option>
                                </select> -->

                                <select v-model="form.status"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    v-if="form.type === 'rent' || !form.type">
                                    <option value="">{{ $t('All Statuses') }}</option>
                                    <option value="active">{{ $t('Active') }}</option>
                                    <option value="future">{{ $t('Future') }}</option>
                                    <option value="completed">{{ $t('Completed') }}</option>
                                </select>

                                <select v-model="form.time_range"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">{{ $t('All Time') }}</option>
                                    <option value="today">{{ $t('Today') }}</option>
                                    <option value="week">{{ $t('This Week') }}</option>
                                    <option value="custom">{{ $t('Custom Range') }}</option>
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

                            <div v-if="form.time_range === 'custom'" class="flex flex-wrap items-center gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('From Date')
                                    }}</label>
                                    <input v-model="form.date_from" type="date"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('To Date')
                                    }}</label>
                                    <input v-model="form.date_to" type="date"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Service Name')
                                    }}</label>
                                    <input v-model="form.service_name" type="text"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :placeholder="$t('Filter by service name...')" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Service Description')}}</label>
                                    <input v-model="form.service_description" type="text"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :placeholder="$t('Filter by description...')" />
                                </div>

                                <div v-if="form.type === 'rent' || !form.type">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Rental Days')
                                    }}</label>
                                    <input v-model="form.days" type="number"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :placeholder="$t('Filter by days...')" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Min Price')
                                    }}</label>
                                    <input v-model="form.price_min" type="number"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :placeholder="$t('Minimum price...')" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Max Price')
                                    }}</label>
                                    <input v-model="form.price_max" type="number"
                                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        :placeholder="$t('Maximum price...')" />
                                </div>
                            </div>
                        </div>

                        <!-- Rental Orders -->
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Rental Orders') }}</h3>
                        <div v-if="rentalOrders?.data?.length > 0" class="overflow-x-auto mb-8">
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
                                            {{ $t('Rental Period') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Days') }}</th>
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
                                                <div>
                                                    {{ order.user?.name || 'N/A' }}<br />
                                                    {{ order.user?.email || 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <button @click="showDetails(order, 'rental')" class="order-name">
                                                {{ order.gold_piece?.name || 'N/A' }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(order.start_date) }} - {{ formatDate(order.end_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ order.rental_days }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                            order.total_price }} {{
                                                $t('SAR') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(order.status).class]">
                                                {{ getStatusClass(order.status).text }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button @click="showDetails(order, 'rental')"
                                                class="show-button py-2 px-4 rounded">
                                                {{ $t('View') }}
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <Pagination :links="rentalOrders.links" class="mt-6" />
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <p class="text-xl font-semibold">{{ $t('No rental orders found.') }}</p>
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
                                            {{ $t('Order Date') }}</th>
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

                                                <div>
                                                    {{ order.user?.name || 'N/A' }}<br />
                                                    {{ order.user?.email || 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <button @click="showDetails(order, 'sale')" class="order-name ">
                                                {{ order.gold_piece?.name || 'N/A' }}
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(order.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                            order.total_price }} {{
                                                $t('SAR') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                             <span
                                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(order.status).class]">
                                                {{ getStatusClass(order.status).text }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button @click="showDetails(order, 'sale')"
                                                class="show-button  py-2 px-4 rounded">
                                                {{ $t('View') }}
                                            </button>
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
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
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
    filters: {
        type: Object,
        default: () => ({}),
    },
    statusOptions: {
        type: Object,
        default: () => ({}),
    },
    timeRangeOptions: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    search: props.filters.search || '',
    type: props.filters.type || '',
    status: props.filters.status || '',
    service_name: props.filters.service_name || '',
    service_description: props.filters.service_description || '',
    days: props.filters.days || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    price_min: props.filters.price_min || '',
    price_max: props.filters.price_max || '',
    time_range: props.filters.time_range || '',
});

const applyFilters = () => {
    form.get(route('admin.orders.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.reset();
    router.get(route('admin.orders.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const getStatusClass = (status) => {
    switch (status) {
        case 'pending_approval':
            return {
                class: 'bg-yellow-100 text-yellow-800',
                text: t('pending_approval')
            };
        case 'approved':
            return {
                class: 'bg-green-100 text-green-800',
                text: t('approved')
            };
        case 'piece_sent':
            return {
                class: 'bg-blue-100 text-blue-800',
                text: t('piece_sent')
            };
        case 'available':
            return {
                class: 'bg-indigo-100 text-indigo-800',
                text: t('available')
            };
        case 'rented':
            return {
                class: 'bg-purple-100 text-purple-800',
                text: t('rented')
            };
        case 'sold':
            return {
                class: 'bg-gray-100 text-gray-800',
                text: t('sold')
            };
        case 'rejected':
            return {
                class: 'bg-red-100 text-red-800',
                text: t('rejected')
            };
        default:
            return {
                class: 'bg-gray-100 text-gray-800',
                text: t(status) // fallback to translation if exists
            };
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const showDetails = (order, type) => {
    router.get(route('admin.orders.show', { id: order.id, type }));
};

// Watch for time_range changes to handle custom date range
watch(() => form.time_range, (newValue) => {
    if (newValue !== 'custom') {
        form.date_from = '';
        form.date_to = '';
    }
});
</script>

<style scoped>
/* Add your custom styles here */
.order-name {
    border: none;
    background-color: transparent;
    text-align: center;
    color: darkgoldenrod;
    text-decoration: underline;
}

.show-button {
    background-color: #f5eb62;
    text-align: center;
}

.show-button:hover {
    background-color: #d8c113;
}
</style>