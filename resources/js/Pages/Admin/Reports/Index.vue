<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Admin Reports') }}
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
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Report Type') }}</label>
                                        <select v-model="form.type" class="w-full mt-1 rounded-md type">
                                            <option value="users_summary">{{ $t('Users Summary') }}</option>
                                            <option value="users_details">{{ $t('Users Details') }}</option>
                                            <option value="vendors_summary">{{ $t('Vendors Summary') }}</option>
                                            <option value="vendors_details">{{ $t('Vendors Details') }}</option>
                                            <option value="Contacts_summary">{{ $t('Complaints Summary') }}</option>
                                            <option value="Contacts_details">{{ $t('Complaints Details') }}</option>
                                            <option value="ratings_summary">{{ $t('Ratings Summary') }}</option>
                                            <option value="ratings_details">{{ $t('Ratings Details') }}</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Start Date') }}</label>
                                        <input v-model="form.start_date" type="date" class="w-full mt-1 rounded-md start-date">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('End Date') }}</label>
                                        <input v-model="form.end_date" type="date" class="w-full mt-1 rounded-md end-date">
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
    type: 'users_summary',
    start_date: new Date().toISOString().split('T')[0],
    end_date: new Date().toISOString().split('T')[0],
});

const generateReport = async (format) => {
    form.processing = true;
    try {
        const response = await axios.post(route('admin.reports.generate'), {
            type: form.type,
            start_date: form.start_date,
            end_date: form.end_date,
            format: format
        }, {
            responseType: 'blob',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (response.status === 200) {
            const fileExtension = format === 'excel' ? 'xlsx' : 'pdf';
            const fileName = `${form.type}_report_${form.start_date}_to_${form.end_date}.${fileExtension}`;

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', fileName);
            document.body.appendChild(link);
            link.click();
            link.remove();
        }
    } catch (error) {
        console.error('Full error:', error);

        if (error.response) {
            console.error('Response data:', error.response.data);
            console.error('Response status:', error.response.status);
            console.error('Response headers:', error.response.headers);

            if (error.response.status === 500) {
                alert('Server error occurred. Please check logs for details.');
            }
        } else if (error.request) {
            console.error('No response received:', error.request);
            alert('No response from server. Please check your connection.');
        } else {
            console.error('Request setup error:', error.message);
            alert('Error setting up request: ' + error.message);
        }
    } finally {
        form.processing = false;
    }
};
</script>

<style scoped>
.actionButtonExcel {
    background-color: #d8c113 !important;
    color: white !important;
    @apply px-4 py-2 rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 !important;
}

.actionButtonPDF {
    background-color: #d8c113 !important;
    color: white !important;
    @apply px-4 py-2 rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 !important;
}

.start-date,
.end-date,
.type {
    @apply px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
}
</style>
