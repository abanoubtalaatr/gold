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
                        <!-- Report Form -->
                        <div class="p-4 mb-6 bg-gray-50 rounded-lg">
                            <h3 class="mb-4 text-lg font-medium">{{ $t('Generate Report') }}</h3>
                            <form @submit.prevent>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Report Type')
                                            }}</label>
                                        <select v-model="form.type" class="w-full mt-1 rounded-md type">
                                            <option value="rentals">{{ $t('Rental Orders') }}</option>
                                            <option value="sales">{{ $t('Sale Orders') }}</option>
                                            <option value="payments">{{ $t('Payments') }}</option>
                                            <option value="ratings">{{ $t('Ratings') }}</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Start Date')
                                            }}</label>
                                        <input v-model="form.start_date" type="date"
                                            class="w-full mt-1 rounded-md start-date">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('End Date')
                                            }}</label>
                                        <input v-model="form.end_date" type="date"
                                            class="w-full mt-1 rounded-md end-date">
                                    </div>

                                    <div class="flex items-end space-x-2">
                                        <button @click.prevent="generateReport('excel')"
                                            class="w-full px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 actionButtonExcel">
                                            {{ $t('Excel') }}
                                        </button>
                                        <button @click.prevent="generateReport('pdf')"
                                            class="w-full px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 actionButtonPDF">
                                            {{ $t('PDF') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Loading indicator -->
                        <div v-if="form.processing" class="p-4 text-center">
                            <p class="text-blue-600">{{ $t('Generating report...') }}</p>
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

const { t } = useI18n();

const form = useForm({
    type: 'rentals',
    start_date: new Date().toISOString().split('T')[0],
    end_date: new Date().toISOString().split('T')[0],
});

const generateReport = async (format) => {
    try {
        const formData = new FormData();
        formData.append('type', form.type);
        formData.append('start_date', form.start_date);
        formData.append('end_date', form.end_date);
        formData.append('format', format);

        const response = await axios.post(route('vendor.reports.generate'), formData, {
            responseType: 'blob',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        // Get the correct file extension
        const fileExtension = format === 'excel' ? 'xlsx' : 'pdf';
        const fileName = `${form.type}_report_${form.start_date}_to_${form.end_date}.${fileExtension}`;

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', fileName);
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error generating report:', error);
    } finally {
        form.processing = false;
    }
};

const formatType = (type) => {
    const types = {
        rentals: t('Rental Orders'),
        sales: t('Sale Orders'),
        payments: t('Payments'),
        ratings: t('Ratings'),
    };
    return types[type] || type;
};
</script>

<style scoped>
.actionButtonExcel {
    background-color: #d47b4b !important;
    /* Warm orange-600 */
    color: white !important;
    @apply px-4 py-2 rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 !important;
}

.actionButtonPDF {
    background-color: #986831 !important;
    /* Warm amber-600 */
    color: white !important;
    @apply px-4 py-2 rounded-md hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 !important;
}

.start-date {
    @apply px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
}

.end-date {
    @apply px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
}

.type {
    @apply px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
}
</style>