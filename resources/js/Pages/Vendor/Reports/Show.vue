<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Report Details') }}
                </h2>
                <Link :href="route('vendor.reports.index')"
                    class="flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                </svg>
                {{ $t('Back to Reports') }}
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Report Info -->
                        <div class="p-4 mb-6 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">{{ $t('Report Type') }}</h4>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ formatReportType(report.type)
                                        }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">{{ $t('Date Range') }}</h4>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ report.date_range }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">{{ $t('Status') }}</h4>
                                    <p class="mt-1">
                                        <span :class="statusClasses(report.status)">
                                            {{ report.status }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a v-if="report.download_url" :href="report.download_url"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    {{ $t('Download Report') }}
                                </a>
                            </div>
                        </div>

                        <!-- Report Data Preview -->
                        <h3 class="mb-4 text-lg font-medium">{{ $t('Report Data') }}</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th v-for="(header, index) in headers" :key="index"
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ header }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(item, index) in data" :key="index">
                                        <td v-for="(value, key) in item" :key="key"
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ value }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({
    report: Object,
    data: Array,
});

const headers = computed(() => {
    if (props.data.length === 0) return [];
    return Object.keys(props.data[0]).map(key => {
        const translations = {
            id: t('ID'),
            service: t('Service'),
            customer: t('Customer'),
            date: t('Date'),
            time: t('Time'),
            status: t('Status'),
            amount: t('Amount'),
            method: t('Method'),
            rating: t('Rating'),
            review: t('Review'),
        };
        return translations[key] || key;
    });
});

const formatReportType = (type) => {
    const types = {
        bookings: t('Bookings'),
        payments: t('Payments'),
        ratings: t('Ratings'),
    };
    return types[type] || type;
};

const statusClasses = (status) => {
    const classes = {
        pending: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800',
        processing: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800',
        completed: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800',
        failed: 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800',
    };
    return classes[status] || '';
};
</script>