<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Wallet') }}
            </h2>

        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-3">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-500">{{ $t('Current Balance') }}</h3>
                                <p class="text-2xl font-bold">{{ wallet.balance }} {{ $t('SAR') }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-500">{{ $t('Pending Balance') }}</h3>
                                <p class="text-2xl font-bold">{{ wallet.pending_balance }} {{ $t('SAR') }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-500">{{ $t('Total Earnings') }}</h3>
                                <p class="text-2xl font-bold">{{ wallet.total_earned }} {{ $t('SAR') }}</p>
                            </div>
                        </div>


                        <div class="mb-8">
                            <h3 class="mb-4 text-lg font-medium">{{ $t('Request Settlement') }}</h3>
                            <form @submit.prevent="submitSettlementRequest" class="flex">
                                <input v-model="settlementAmount" type="number"
                                    class="w-full px-4 py-2 border rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :placeholder="$t('Amount')" min="100" step="0.01" required>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-r-lg hover:bg-indigo-700">
                                    {{ $t('Request') }}
                                </button>
                            </form>
                        </div>

                        <!-- Transactions Icon Button -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">{{ $t('Recent Transactions') }}</h3>
                            <Link :href="route('vendor.wallet.transactions')"
                                class="p-2 text-gray-500 hover:text-gray-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="sr-only">{{ $t('View All Transactions') }}</span>
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Date') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Operation') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Amount') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="transaction in transactions.data" :key="transaction.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ transaction.created_at }}
                                        </td>
                                        <!-- <td class="px-6 py-4">
                                            {{ transaction.description }}
                                        </td> -->
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <!-- Operation Icon -->
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <OperationIcon :type="getOperationType(transaction)" />
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ getOperationName(transaction) }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ transaction.description }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap"
                                            :class="transaction.type === 'credit' ? 'text-green-600' : 'text-red-600'">
                                            {{ transaction.type === 'credit' ? '+' : '-' }}{{ transaction.amount }} {{
                                                $t('SAR')
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="transactions.links" class="mt-4" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';
import OperationIcon from '@/Components/Wallet/OperationIcon.vue';

const { t } = useI18n();
const props = defineProps({
    wallet: Object,
    transactions: Object,
});

const settlementAmount = ref(100);

const submitSettlementRequest = () => {
    router.post(route('vendor.wallet.settlement.request'), {
        amount: settlementAmount.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            settlementAmount.value = 100;
        },
    });
};
const getTransactionType = (transaction) => {
    if (transaction.description.includes('Earnings from order')) {
        return t('Order Earnings');
    } else if (transaction.description.includes('Settlement payout')) {
        return t('Settlement');
    } else if (transaction.description.includes('Admin adjustment')) {
        return t('Admin Adjustment');
    }
    return t('Transaction');
};

const getTransactionIconClass = (transaction) => {
    return transaction.type === 'credit' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600';
};

const getOperationType = (transaction) => {
    if (transaction.description.includes('Earnings from order')) {
        return 'wallet_charge';
    } else if (transaction.description.includes('Settlement payout')) {
        return 'settlement';
    } else if (transaction.description.includes('Admin adjustment')) {
        return 'admin_adjustment';
    }
    return 'other';
};

const getOperationName = (transaction) => {
    switch (getOperationType(transaction)) {
        case 'wallet_charge': return t('Wallet Charge');
        case 'settlement': return t('Settlement');
        case 'admin_adjustment': return t('Admin Adjustment');
        default: return t('Transaction');
    }
};

const formatDateTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
};

</script>
