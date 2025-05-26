<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, watch } from 'vue'

// Define props
const props = defineProps<{
    auth: {
        user: {
            name: string
            email: string
        }
    }
    roles: number
    admins: number
    branches: number
    rentalRequests: number
    salesOrders: number
    rentalOrders: number
    filters: {
        period: string
        from_date: string | null
        to_date: string | null
    }
}>()

// Filter state
const period = ref(props.filters.period || 'all')
const fromDate = ref(props.filters.from_date || '')
const toDate = ref(props.filters.to_date || '')

// Watch for filter changes
watch([period, fromDate, toDate], ([newPeriod, newFromDate, newToDate]) => {
    router.get(route('vendor.dashboard'), {
        period: newPeriod,
        from_date: newFromDate,
        to_date: newToDate
    }, {
        preserveState: true,
        replace: true
    })
})

// Reset all filters
const resetFilters = () => {
    period.value = 'all'
    fromDate.value = ''
    toDate.value = ''
}
</script>

<template>

    <Head title="Vendor Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Vendor Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters Section -->
                <div class="bg-white mb-6 p-6 shadow-sm sm:rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">{{ $t("Filters") }}</h3>

                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="annual">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t("Time Period") }}</label>
                            <select v-model="period" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="all">{{ $t("All Time") }}</option>
                                <option value="daily">{{ $t("Daily") }}</option>
                                <option value="weekly">{{ $t("Weekly") }}</option>
                                <option value="monthly">{{ $t("Monthly") }}</option>
                                <option value="quarterly">{{ $t("Quarterly") }}</option>
                                <option value="semi-annually">{{ $t("Semi-Annually") }}</option>
                                <option value="annually">{{ $t("Annually") }}</option>
                            </select>
                        </div>
                        <div class="srart-date">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t("From Date") }}</label>
                            <input type="date" v-model="fromDate" class="w-full border-gray-300 rounded-md shadow-sm"
                                :max="toDate || new Date().toISOString().split('T')[0]" />
                        </div>
                        <div class="end-date">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t("To Date") }}</label>
                            <input type="date" v-model="toDate" class="w-full border-gray-300 rounded-md shadow-sm"
                                :min="fromDate" :max="new Date().toISOString().split('T')[0]" />
                        </div>
                        <div class="flex justify-end rest-button">
                            <button @click="resetFilters"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                                {{ $t("Reset Filters") }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">{{ $t("Welcome to your vendor dashboard!") }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-blue-100 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-blue-800">{{ $t("Total Roles") }}</h4>
                                <p class="text-2xl font-bold text-blue-900">{{ roles }}</p>
                            </div>
                            <div class="bg-green-100 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-green-800">{{ $t("Total Admins") }}</h4>
                                <p class="text-2xl font-bold text-green-900">{{ admins }}</p>
                            </div>
                            <div class="bg-purple-100 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-purple-800">{{ $t("Total Branches") }}</h4>
                                <p class="text-2xl font-bold text-purple-900">{{ branches }}</p>
                            </div>
                            <div class="bg-yellow-100 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-yellow-800">{{ $t("Rental Requests") }}</h4>
                                <p class="text-2xl font-bold text-yellow-900">{{ rentalRequests }}</p>
                            </div>
                            <div class="bg-red-100 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-red-800">{{ $t("Sales Orders") }}</h4>
                                <p class="text-2xl font-bold text-red-900">{{ salesOrders }}</p>
                            </div>
                            <div class="bg-indigo-100 p-4 rounded-lg">
                                <h4 class="text-sm font-semibold text-indigo-800">{{ $t("Rental Orders") }}</h4>
                                <p class="text-2xl font-bold text-indigo-900">{{ rentalOrders }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.rest-button button {
    height: 40px;
    margin-left: 70px !important;
    background-color: rgb(238, 233, 233) !important;
}

.rest-button {
    display: flex;
    justify-content: flex-start;
    margin-top: 10px !important;

}
</style>