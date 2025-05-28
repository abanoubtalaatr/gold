<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Vendor Wallet Management') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Wallet Summary -->
                        <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-4">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-500">{{ $t('Current Balance') }}</h3>
                                <div class="flex items-center justify-between">
                                    <p class="text-2xl font-bold">{{ wallet?.balance ?? 0 }} {{ $t('SAR') }}</p>
                                    <button @click="showBalanceAdjustment = true"
                                        class="p-1 text-gray-400 hover:text-gray-600">
                                        <PencilSquareIcon class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-500">{{ $t('Pending Balance') }}</h3>
                                <p class="text-2xl font-bold">{{ wallet?.pending_balance ?? 0 }} {{ $t('SAR') }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-500">{{ $t('Total Earnings') }}</h3>
                                <p class="text-2xl font-bold">{{ wallet?.total_earned ?? 0 }} {{ $t('SAR') }}</p>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-500">{{ $t('IBAN') }}</h3>
                                <p class="text-xl font-mono">{{ vendor.iban || $t('Not provided') }}</p>
                            </div>
                        </div>

                        <!-- Balance Adjustment Modal -->
                        <TransitionRoot as="template" :show="showBalanceAdjustment">
                            <Dialog as="div" class="relative z-10" @close="showBalanceAdjustment = false">
                                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                                    enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100"
                                    leave-to="opacity-0">
                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                                </TransitionChild>

                                <div class="fixed inset-0 z-10 overflow-y-auto">
                                    <div
                                        class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                        <TransitionChild as="template" enter="ease-out duration-300"
                                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                            enter-to="opacity-100 translate-y-0 sm:scale-100"
                                            leave="ease-in duration-200"
                                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                                            <DialogPanel
                                                class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                                                <div>
                                                    <div class="mt-3 text-center sm:mt-5">
                                                        <DialogTitle as="h3"
                                                            class="text-lg font-medium leading-6 text-gray-900">
                                                            {{ $t('Adjust Balance') }}
                                                        </DialogTitle>
                                                        <div class="mt-2">
                                                            <p class="text-sm text-gray-500">
                                                                {{ $t('Current balance') }}: {{ wallet.balance }} {{
                                                                    $t('SAR')
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-5 sm:mt-6 space-y-4">
                                                    <div>
                                                        <label for="adjustmentType"
                                                            class="block text-sm font-medium text-gray-700">
                                                            {{ $t('Adjustment Type') }}
                                                        </label>
                                                        <select id="adjustmentType" v-model="adjustmentType"
                                                            class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                                            <option value="credit">{{ $t('Credit') }}</option>
                                                            <option value="debit">{{ $t('Debit') }}</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label for="adjustmentAmount"
                                                            class="block text-sm font-medium text-gray-700">
                                                            {{ $t('Amount') }} ({{ $t('SAR') }})
                                                        </label>
                                                        <input type="number" id="adjustmentAmount"
                                                            v-model="adjustmentAmount"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                            min="0.01" step="0.01" required />
                                                    </div>
                                                    <div>
                                                        <label for="adjustmentReason"
                                                            class="block text-sm font-medium text-gray-700">
                                                            {{ $t('Reason') }}
                                                        </label>
                                                        <textarea id="adjustmentReason" v-model="adjustmentReason"
                                                            rows="3"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                            required></textarea>
                                                    </div>
                                                </div>
                                                <div
                                                    class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                                    <button type="button"
                                                        class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:col-start-2 sm:text-sm"
                                                        @click="submitBalanceAdjustment"
                                                        :disabled="adjustmentForm.processing">
                                                        {{ $t('Submit') }}
                                                    </button>
                                                    <button type="button"
                                                        class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm"
                                                        @click="showBalanceAdjustment = false" ref="cancelButtonRef">
                                                        {{ $t('Cancel') }}
                                                    </button>
                                                </div>
                                            </DialogPanel>
                                        </TransitionChild>
                                    </div>
                                </div>
                            </Dialog>
                        </TransitionRoot>

                        <!-- Settlement Requests Section -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">{{ $t('Settlement Requests') }}</h3>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $t('Date') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $t('Amount') }}
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
                                        <tr v-for="request in settlementRequests.data" :key="request.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ formatDateTime(request.created_at) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ request.amount }} {{ $t('SAR') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="{
                                                    'bg-yellow-100 text-yellow-800': request.status === 'pending',
                                                    'bg-green-100 text-green-800': request.status === 'approved',
                                                    'bg-red-100 text-red-800': request.status === 'rejected'
                                                }"
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                    {{ $t(request.status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button v-if="request.status === 'pending'"
                                                        @click="approveSettlement(request)"
                                                        class="text-green-600 hover:text-green-900 accept-button">
                                                        {{ $t('Approve') }}
                                                    </button>
                                                    <button v-if="request.status === 'pending'"
                                                        @click="showRejectDialog(request)"
                                                        class="text-red-600 hover:text-red-900 reject-button">
                                                        {{ $t('Reject') }}
                                                    </button>
                                                    <button v-if="request.status !== 'pending'"
                                                        @click="showRequestDetails(request)"
                                                        class="text-blue-600 hover:text-blue-900 details-button">
                                                        {{ $t('Details') }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <Pagination :links="settlementRequests.links" class="mt-4" />
                        </div>

                        <!-- Recent Transactions -->
                        <div class="mb-8" v-if="transactions.data > 0">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">{{ $t('Recent Transactions') }}</h3>
                                <Link :href="route('admin.wallet.transactions', wallet.id)"
                                    class="text-sm text-indigo-600 hover:text-indigo-900">
                                {{ $t('View All') }}
                                </Link>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $t('Date') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $t('Description') }}
                                            </th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $t('Amount') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="transaction in transactions.data" :key="transaction.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ formatDateTime(transaction.created_at) }}
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ transaction.description }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap"
                                                :class="transaction.type === 'credit' ? 'text-green-600' : 'text-red-600'">
                                                {{ transaction.type === 'credit' ? '+' : '-' }}{{ transaction.amount }}
                                                {{
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
        </div>

        <!-- Reject Settlement Dialog -->
        <TransitionRoot as="template" :show="showRejectModal">
            <Dialog as="div" class="relative z-10" @close="showRejectModal = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300"
                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel
                                class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                                <div>
                                    <div class="mt-3 text-center sm:mt-5">
                                        <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                                            {{ $t('Reject Settlement Request') }}
                                        </DialogTitle>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500">
                                                {{ $t('Request Amount') }}: {{ currentRequest?.amount }} {{ $t('SAR') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-6">
                                    <label for="rejectReason" class="block text-sm font-medium text-gray-700">
                                        {{ $t('Reason for rejection') }}
                                    </label>
                                    <textarea id="rejectReason" v-model="rejectReason" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        required></textarea>
                                </div>
                                <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                    <button type="button"
                                        class="inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:col-start-2 sm:text-sm"
                                        @click="rejectSettlement" :disabled="!rejectReason">
                                        {{ $t('Confirm Rejection') }}
                                    </button>
                                    <button type="button"
                                        class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm"
                                        @click="showRejectModal = false" ref="cancelButtonRef">
                                        {{ $t('Cancel') }}
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { PencilSquareIcon } from '@heroicons/vue/24/outline';
import Pagination from '@/Components/Pagination.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({
    wallet: {
        type: Object,
        default: () => ({
            balance: 0,
            pending_balance: 0,
            total_earned: 0,
            id: null
        })
    },
    vendor: Object,
    transactions: {
        type: Object,
        default: () => ({ data: [] })
    },
    settlementRequests: {
        type: Object,
        default: () => ({ data: [] })
    },
});

const showBalanceAdjustment = ref(false);
const adjustmentType = ref('credit');
const adjustmentAmount = ref(0);
const adjustmentReason = ref('');
const adjustmentForm = useForm({});

const showRejectModal = ref(false);
const currentRequest = ref(null);
const rejectReason = ref('');

const formatDateTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
};

const submitBalanceAdjustment = async () => {
    if (!props.wallet?.id) {
        alert(t('Wallet not available'));
        return;
    }
    if (!adjustmentAmount.value || adjustmentAmount.value <= 0) {
        alert(t('Please enter a valid amount'));
        return;
    }

    if (!adjustmentReason.value) {
        alert(t('Please enter a reason for the adjustment'));
        return;
    }

    try {
        await router.post(route('admin.wallet.adjust'), {
            wallet_id: props.wallet.id,
            type: adjustmentType.value,
            amount: adjustmentAmount.value,
            reason: adjustmentReason.value
        }, {
            preserveScroll: true,
            onSuccess: () => {
                showBalanceAdjustment.value = false;
                adjustmentAmount.value = 0;
                adjustmentReason.value = '';
            },
        });
    } catch (error) {
        console.error('Error adjusting balance:', error);
    }
};

const approveSettlement = (request) => {
    if (confirm(t('Are you sure you want to approve this settlement request?'))) {
        router.put(route('admin.settlement.approve', request.id), {}, {
            preserveScroll: true,
        });
    }
};

const showRejectDialog = (request) => {
    currentRequest.value = request;
    rejectReason.value = '';
    showRejectModal.value = true;
};

const rejectSettlement = () => {
    router.put(route('admin.settlement.reject', currentRequest.value.id), {
        reason: rejectReason.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showRejectModal.value = false;
        },
    });
};

const showRequestDetails = (request) => {
    // Implement details view logic
    alert(t('Settlement request details') + `:\n\n${request.admin_notes || t('No additional details')}`);
};
</script>

<style scoped>
.details-button {
    height: 40px;
    width: 100px;
    background-color: rgb(237, 233, 233) !important;
    border: 1px solid rgb(0, 0, 0) !important;
    border-radius: 5px;
    text-align: center !important;
    color: rgb(0, 0, 0) !important;
    font-weight: bold !important;
}

.details-button:hover {
    background-color: rgb(0, 0, 0) !important;
    color: rgb(226, 226, 226) !important;
    transform: translate(-1px, -1px) !important;
    transition: transform 0.2s ease-in-out !important;
}

.accept-button {
    height: 40px;
    width: 100px;
    background-color: rgb(5, 172, 52) !important;
    border: 1px solid rgb(0, 0, 0) !important;
    border-radius: 5px;
    text-align: center !important;
    color: rgb(0, 0, 0) !important;
    font-weight: bold !important;
}

.accept-button:hover {
    background-color: rgb(4, 115, 32) !important;
    color: rgb(0, 0, 0) !important;
    transform: translate(-1px, -1px) !important;
    transition: transform 0.2s ease-in-out !important;
}

.reject-button {
    height: 40px;
    width: 100px;
    background-color: rgb(240, 12, 12) !important;
    border: 1px solid rgb(0, 0, 0) !important;
    border-radius: 5px;
    text-align: center !important;
    color: rgb(0, 0, 0) !important;
    font-weight: bold !important;
    margin-right: 5px !important;
}

.reject-button:hover {
    background-color: rgb(169, 9, 9) !important;
    color: rgb(0, 0, 0) !important;
    transform: translate(-1px, -1px) !important;
    transition: transform 0.2s ease-in-out !important;
}
</style>
