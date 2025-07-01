<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Reports') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Filters Section - Moved to Top -->
                        <div class="p-4 mb-6 bg-gray-50 rounded-lg">
                            <h3 class="mb-4 text-lg font-medium">{{ $t('Filter Orders') }}</h3>
                            <form @submit.prevent="filterOrders">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Report Type') }}</label>
                                        <select v-model="filterForm.type" @change="filterOrders" class="w-full mt-1 rounded-md type">
                                            <option value="sales">{{ $t('Sale Orders') }}</option>
                                            <!-- <option value="rentals">{{ $t('Rental Orders') }}</option> -->
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Start Date') }}</label>
                                        <input v-model="filterForm.start_date" @change="filterOrders" type="date"
                                            class="w-full mt-1 rounded-md start-date">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('End Date') }}</label>
                                        <input v-model="filterForm.end_date" @change="filterOrders" type="date"
                                            class="w-full mt-1 rounded-md end-date">
                                    </div>

                                    
                                </div>
                            </form>
                        </div>

                        <!-- Orders Display -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ filterForm.type === 'sales' ? $t('Sale Orders') : $t('Rental Orders') }}
                                    <span class="text-sm text-gray-500 ml-2">
                                        ({{ formatDateRange(filterForm.start_date, filterForm.end_date) }})
                                    </span>
                                </h3>
                                
                                <!-- Export Buttons -->
                                <div class="flex space-x-2">
                                    <button @click.prevent="generateReport('excel')"
                                        :disabled="exportForm.processing || !filteredOrders.length"
                                        class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 actionButtonExcel disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span v-if="!exportForm.processing">{{ $t('Excel') }}</span>
                                        <span v-else>{{ $t('Generating...') }}</span>
                                    </button>
                                    <button @click.prevent="generateReport('pdf')"
                                        :disabled="exportForm.processing || !filteredOrders.length"
                                        class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 actionButtonPDF disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span v-if="!exportForm.processing">{{ $t('PDF') }}</span>
                                        <span v-else>{{ $t('Generating...') }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Loading indicator for filtering -->
                            <div v-if="filterForm.processing" class="p-4 text-center">
                                <div class="flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                    <p class="ml-3 text-blue-600">{{ $t('Loading orders...') }}</p>
                                </div>
                            </div>

                            <!-- Orders Table -->
                            <div v-else-if="filteredOrders && filteredOrders.length > 0" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">{{ $t('ID') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">{{ $t('Customer') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">{{ $t('Gold Piece') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">{{ $t('Weight (g)') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">{{ $t('Carat') }}</th>
                                            <th v-if="filterForm.type === 'rentals'" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">{{ $t('Start Date') }}</th>
                                            <th v-if="filterForm.type === 'rentals'" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">{{ $t('End Date') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">{{ $t('Total Price') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r">{{ $t('Status') }}</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $t('Created At') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">{{ order.id }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">{{ order.customer_name }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">{{ order.gold_piece_name }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">{{ order.weight }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">{{ order.carat }}</td>
                                            <td v-if="filterForm.type === 'rentals'" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">{{ order.start_date }}</td>
                                            <td v-if="filterForm.type === 'rentals'" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">{{ order.end_date }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r font-medium">{{ formatPrice(order.total_price) }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap border-r">
                                                <span :class="getStatusClass(order.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                                    {{ formatStatus(order.status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ order.created_at }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- No Data Message -->
                            <div v-else class="text-center py-8">
                                <p class="text-gray-500">{{ $t('No orders found for the selected criteria') }}</p>
                            </div>
                        </div>

                        <!-- Loading indicator for export -->
                        <div v-if="exportForm.processing" class="p-4 text-center">
                            <div class="flex items-center justify-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <p class="ml-3 text-blue-600">{{ $t('Generating report...') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const { t } = useI18n();

// Get props from controller
const props = defineProps({
    todaysOrders: {
        type: Array,
        default: () => []
    }
});

// Reactive data
const filteredOrders = ref([]);

// Filter form
const filterForm = useForm({
    type: 'sales',
    start_date: new Date().toISOString().split('T')[0],
    end_date: new Date().toISOString().split('T')[0],
    processing: false
});

// Export form
const exportForm = useForm({
    processing: false
});

// Initialize with today's orders
onMounted(() => {
    filteredOrders.value = props.todaysOrders || [];
    filterOrders(); // Load initial data
});

// Filter orders function
const filterOrders = async () => {
    if (filterForm.processing) return;
    
    filterForm.processing = true;
    
    try {
        const response = await axios.post(route('vendor.reports.filter'), {
            type: filterForm.type,
            start_date: filterForm.start_date,
            end_date: filterForm.end_date
        });
        
        filteredOrders.value = response.data.orders || [];
    } catch (error) {
        console.error('Error filtering orders:', error);
        filteredOrders.value = [];
    } finally {
        filterForm.processing = false;
    }
};

// Generate report function
const generateReport = async (format) => {
    if (exportForm.processing) return;

    exportForm.processing = true;

    try {
        if (format === 'excel') {
            const response = await axios.post(route('vendor.reports.generate'), {
                type: filterForm.type,
                start_date: filterForm.start_date,
                end_date: filterForm.end_date,
                format: format
            }, {
                responseType: 'blob',
                headers: {
                    'Accept': 'application/octet-stream',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            const fileName = `${filterForm.type}_report_${filterForm.start_date}_to_${filterForm.end_date}.xlsx`;
            const blob = new Blob([response.data], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });

            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', fileName);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
        } else {
            // Redirect to the view route for PDF
            const pdfUrl = route('vendor.reports.view', {
                type: filterForm.type,
                start_date: filterForm.start_date,
                end_date: filterForm.end_date
            });
            
            // Open PDF in new window/tab
            window.open(pdfUrl, '_blank');
        }
    } catch (error) {
        console.error('Error generating report:', error);
        if (error.response) {
            console.error('Response data:', error.response.data);
            console.error('Response status:', error.response.status);
        }
        alert(t('Failed to generate report. Please try again.'));
    } finally {
        exportForm.processing = false;
    }
};

// Helper functions
const formatPrice = (price) => {
    return new Intl.NumberFormat('ar-EG', {
        style: 'currency',
        currency: 'EGP'
    }).format(price);
};

const formatDateRange = (startDate, endDate) => {
    const start = new Date(startDate).toLocaleDateString();
    const end = new Date(endDate).toLocaleDateString();
    return `${start} - ${end}`;
};

const formatStatus = (status) => {
    const statuses = {
        'pending_approval': t('Pending Approval'),
        'approved': t('Approved'),
        'piece_sent': t('Piece Sent'),
        'vendor_already_take_the_piece': t('Vendor Has Piece'),
        'sold': t('Sold'),
        'rejected': t('Rejected'),
        'canceled': t('Canceled'),
        'confirm_sold_from_vendor': t('Confirmed Sold'),
        'rented': t('Rented'),
        'available': t('Available'),
        'returned': t('Returned'),
    };
    return statuses[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        'pending_approval': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-blue-100 text-blue-800',
        'piece_sent': 'bg-purple-100 text-purple-800',
        'vendor_already_take_the_piece': 'bg-indigo-100 text-indigo-800',
        'sold': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
        'canceled': 'bg-gray-100 text-gray-800',
        'confirm_sold_from_vendor': 'bg-emerald-100 text-emerald-800',
        'rented': 'bg-green-100 text-green-800',
        'available': 'bg-blue-100 text-blue-800',
        'returned': 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};
</script>

<style scoped>
.actionButtonExcel {
    background-color: #d47b4b !important;
    color: white !important;
    @apply px-4 py-2 rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 !important;
}

.actionButtonPDF {
    background-color: #986831 !important;
    color: white !important;
    @apply px-4 py-2 rounded-md hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 !important;
}

.start-date, .end-date, .type {
    @apply px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>