<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                إدارة محفظة العميل - {{ props.client.name }}
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Client Info Card -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium mb-4">معلومات العميل</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">الاسم</label>
                                <p class="mt-1 text-sm text-gray-900">{{ props.client.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                                <p class="mt-1 text-sm text-gray-900">{{ props.client.email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">الموقع</label>
                                <p class="mt-1 text-sm text-gray-900">{{ props.client.city?.name || 'غير محدد' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">رقم الآيبان</label>
                                <p class="mt-1 text-sm text-gray-900 font-mono">{{ props.client.iban }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Balance Card -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">الرصيد الحالي للعميل</h3>
                            <button @click="showBalanceModal = true"
                                class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                تعديل الرصيد
                            </button>
                        </div>
                        <div class="text-3xl font-bold text-green-600">
                            {{ formatCurrency(props.currentBalance) }} ريال
                        </div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium mb-4">سجل المعاملات</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                            التاريخ
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                            النوع
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                            المبلغ
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                            الوصف
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                            الحالة
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">
                                            الإجراءات
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="transaction in props.transactions.data" :key="transaction.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(transaction.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-green-100 text-green-800': transaction.type === 'credit',
                                                'bg-red-100 text-red-800': transaction.type === 'debit',
                                            }" class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full">
                                                {{ transaction.type === 'credit' ? 'إضافة' : 'خصم' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <span :class="{
                                                'text-green-600': transaction.type === 'credit',
                                                'text-red-600': transaction.type === 'debit',
                                            }">
                                                {{ transaction.type === 'credit' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }} ريال
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ transaction.description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-green-100 text-green-800': transaction.status === 'completed',
                                                'bg-yellow-100 text-yellow-800': transaction.status === 'pending',
                                                'bg-red-100 text-red-800': transaction.status === 'failed',
                                            }" class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full">
                                                {{ getStatusText(transaction.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div v-if="transaction.status === 'pending'" class="flex space-x-2">
                                                <button @click="approveTransaction(transaction)"
                                                    class="text-green-600 hover:text-green-900">
                                                    قبول
                                                </button>
                                                <button @click="showRejectModal(transaction)"
                                                    class="text-red-600 hover:text-red-900">
                                                    رفض
                                                </button>
                                            </div>
                                            <span v-else class="text-gray-500">-</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="props.transactions.links" class="mt-4" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Balance Update Modal -->
        <div v-if="showBalanceModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeBalanceModal">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <!-- Modal Content -->
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" @click.stop>
                    <form @submit.prevent="updateBalance">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 text-right">
                                تعديل رصيد العميل
                            </h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">نوع العملية</label>
                                <select v-model="balanceForm.type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-right">
                                    <option value="add">إضافة رصيد</option>
                                    <option value="subtract">خصم رصيد</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">المبلغ</label>
                                <input type="number" step="0.01" min="0" v-model="balanceForm.amount" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-right">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">الوصف</label>
                                <textarea v-model="balanceForm.description" required rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-right"
                                    placeholder="أدخل وصف العملية..."></textarea>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" @click="testButtonClick"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                تحديث الرصيد
                            </button>
                            <button type="button" @click="closeBalanceModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Transaction Modal -->
        <div v-if="showRejectTransactionModal" class="fixed inset-0 z-50 overflow-y-auto" @click.self="closeRejectModal">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <!-- Modal Content -->
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg" @click.stop>
                    <form @submit.prevent="rejectTransaction">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 text-right">
                                رفض المعاملة
                            </h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2 text-right">سبب الرفض</label>
                                <textarea v-model="rejectForm.rejection_reason" required rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-right"
                                    placeholder="يرجى إدخال سبب رفض المعاملة..."></textarea>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                رفض المعاملة
                            </button>
                            <button type="button" @click="closeRejectModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref, reactive } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    client: Object,
    transactions: Object,
    currentBalance: Number,
});

const showBalanceModal = ref(false);
const showRejectTransactionModal = ref(false);
const selectedTransaction = ref(null);

const balanceForm = reactive({
    type: 'add',
    amount: '',
    description: '',
});

const rejectForm = reactive({
    rejection_reason: '',
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('ar-SA', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusText = (status) => {
    const statusMap = {
        'completed': 'مكتملة',
        'pending': 'في الانتظار',
        'failed': 'مرفوضة',
    };
    return statusMap[status] || status;
};

const updateBalance = () => {
    console.log('updateBalance function called');
    console.log('Form data:', balanceForm);
    console.log('Client:', props.client);
    console.log('Client ID:', props.client.id);
    
    // Test route generation
    try {
        const routeUrl = route('admin.wallet.update-balance', props.client.id);
        console.log('Generated route URL:', routeUrl);
    } catch (error) {
        console.error('Route generation error:', error);
        Swal.fire('خطأ!', 'خطأ في إنشاء الرابط', 'error');
        return;
    }
    
    // Validate form data
    if (!balanceForm.amount || !balanceForm.description) {
        Swal.fire('خطأ!', 'يرجى ملء جميع الحقول المطلوبة', 'error');
        return;
    }
    
    if (parseFloat(balanceForm.amount) <= 0) {
        Swal.fire('خطأ!', 'يجب أن يكون المبلغ أكبر من صفر', 'error');
        return;
    }
    
    console.log('Validation passed, sending request...');
    
    router.post(route('admin.wallet.update-balance', props.client.id), balanceForm, {
        onStart: () => {
            console.log('Request started');
        },
        onSuccess: (page) => {
            console.log('Request successful:', page);
            closeBalanceModal();
            Swal.fire('نجح!', 'تم تحديث رصيد العميل بنجاح', 'success');
        },
        onError: (errors) => {
            console.log('Request failed with errors:', errors);
            Swal.fire('خطأ!', 'حدث خطأ أثناء تحديث الرصيد', 'error');
        },
        onFinish: () => {
            console.log('Request finished');
        }
    });
};

// Test function to debug button click
const testButtonClick = () => {
    console.log('Submit button clicked!');
    updateBalance();
};

const approveTransaction = (transaction) => {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: 'هل تريد قبول هذه المعاملة؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، قبول',
        cancelButtonText: 'إلغاء',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('admin.wallet.approve-transaction', transaction.id), {}, {
                onSuccess: () => {
                    Swal.fire('تم!', 'تم قبول المعاملة بنجاح', 'success');
                },
                onError: () => {
                    Swal.fire('خطأ!', 'حدث خطأ أثناء قبول المعاملة', 'error');
                }
            });
        }
    });
};

const showRejectModal = (transaction) => {
    selectedTransaction.value = transaction;
    showRejectTransactionModal.value = true;
};

const rejectTransaction = () => {
    router.post(route('admin.wallet.reject-transaction', selectedTransaction.value.id), rejectForm, {
        onSuccess: () => {
            closeRejectModal();
            Swal.fire('تم!', 'تم رفض المعاملة بنجاح', 'success');
        },
        onError: () => {
            Swal.fire('خطأ!', 'حدث خطأ أثناء رفض المعاملة', 'error');
        }
    });
};

const closeBalanceModal = () => {
    showBalanceModal.value = false;
    balanceForm.type = 'add';
    balanceForm.amount = '';
    balanceForm.description = '';
};

const closeRejectModal = () => {
    showRejectTransactionModal.value = false;
    selectedTransaction.value = null;
    rejectForm.rejection_reason = '';
};
</script> 