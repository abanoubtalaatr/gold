<template>

    <Head title="Wallet Transactions" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Wallet Transactions') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Vendor Debt -->
                        
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body text-center py-3">
                                        <h5 class="card-title">{{ $t('Outstanding Debt') }}: {{ vendorDebt }} 
                                        </h5>
                                        <button class="btn btn-primary" @click="initiatePayment"
                                            :disabled="isProcessing">
                                            {{ isProcessing ? $t('Processing...') : $t('Pay Debt') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transactions Table -->
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Transactions') }}</h3>
                        <div v-if="transactions?.data?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Transaction ID') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Description') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Amount') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Type') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Order ID') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="transaction in transactions.data" :key="transaction.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ transaction.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ transaction.description || '--' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ transaction.amount }} {{ $t('SAR') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getTypeClass(transaction.type)]">
                                                {{ formatType(transaction.order_sale_id ? $t("sale") : $t("rental")) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ transaction.order_sale_id || transaction.order_rental_id || '--' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ new Date(transaction.created_at).toLocaleDateString() }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <Pagination :links="transactions.links" class="mt-6" />
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <p class="text-xl font-semibold">{{ $t('No transactions found.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({
    transactions: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    vendorDebt: {
        type: [Number, String],
        default: 0,
    },
});

const form = useForm({
    type: props.filters.type || '',
});

const applyFilters = () => {
    form.get(route('vendor.transactions.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};
const initiatePayment = async () => {
    isProcessing.value = true;

    const response = await axios.post(route('vendor.payment.initiate'), {
        amount: props.vendorDebt,
    });
    console.log(response.data);
    const checkoutUrl = response.data.checkout_url;
    window.location.href = checkoutUrl; // Redirect to Paymob checkout

};
const resetFilters = () => {
    form.type = '';
    router.get(route('vendor.transactions.index'), {}, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const getTypeClass = (type) => {
    switch (type) {
        case 'credit':
            return 'bg-green-100 text-green-800';
        case 'debit':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const formatType = (type) => {
    const typeMap = {
        credit: t('Credit'),
        debit: t('Debit'),
    };
    return typeMap[type] || type.charAt(0).toUpperCase() + type.slice(1);
};
</script>

<style>
/* Ensure consistent styling */
</style>
