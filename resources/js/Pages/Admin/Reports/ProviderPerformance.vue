<template>
    <AuthenticatedLayout>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $t('reports.provider_performance.title') }}
            </h2>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters Section -->
                <el-card class="mb-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <!-- Date Range -->
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span>{{ $t('reports.provider_performance.from') }}:</span>
                                <el-date-picker
                                    v-model="filters.dateRange.start"
                                    type="date"
                                    :placeholder="$t('reports.provider_performance.from')"
                                    format="YYYY/MM/DD"
                                    value-format="YYYY-MM-DD"
                                    @change="applyFilters"
                                />
                            </div>
                            <div class="flex items-center gap-2">
                                <span>{{ $t('reports.provider_performance.to') }}:</span>
                                <el-date-picker
                                    v-model="filters.dateRange.end"
                                    type="date"
                                    :placeholder="$t('reports.provider_performance.to')"
                                    format="YYYY/MM/DD"
                                    value-format="YYYY-MM-DD"
                                    @change="applyFilters"
                                />
                            </div>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex items-center gap-2">
                            <el-button
                                type="primary"
                                @click="exportReport('pdf')"
                                :icon="Printer"
                                class="flex items-center gap-2"
                            >
                                <span>{{ $t('reports.provider_performance.export_pdf') }}</span>
                            </el-button>
                            <el-button
                                type="success"
                                @click="exportReport('excel')"
                                :icon="Document"
                                class="flex items-center gap-2"
                            >
                                <span>{{ $t('reports.provider_performance.export_excel') }}</span>
                            </el-button>
                        </div>
                    </div>

                    <!-- Other Filters -->
                    <div class="mt-4">
                        <el-select
                            v-model="filters.mainService"
                            :placeholder="$t('reports.provider_performance.main_service')"
                            class="w-full md:w-auto"
                            @change="applyFilters"
                        >
                            <el-option :label="$t('reports.provider_performance.all_services')" value="" />
                            <el-option
                                v-for="service in mainServices"
                                :key="service.id"
                                :label="service.name"
                                :value="service.id"
                            />
                        </el-select>
                    </div>
                </el-card>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <SummaryCard
                        :title="$t('reports.provider_performance.total_providers')"
                        :value="report.summary.total_providers"
                        color="blue"
                        icon="UserFilled"
                    />
                    <SummaryCard
                        :title="$t('reports.provider_performance.total_revenue')"
                        :value="formatCurrency(report.summary.total_revenue)"
                        color="green"
                        icon="Money"
                    />
                    <SummaryCard
                        :title="$t('reports.provider_performance.average_rating')"
                        :value="report.summary.average_rating"
                        color="yellow"
                        icon="StarFilled"
                    />
                    <SummaryCard
                        :title="$t('reports.provider_performance.total_bookings')"
                        :value="report.summary.total_bookings"
                        color="purple"
                        icon="ShoppingCartFull"
                    />
                </div>

                <!-- Providers Table -->
                <ProvidersTable
                    :providers="report.providers"
                    :pagination="{
                        currentPage: currentPage,
                        perPage: perPage,
                        total: total,
                    }"
                    :filters="filters"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import SummaryCard from "@/Components/Reports/SummaryCard.vue";
import ProvidersTable from "@/Components/Reports/ProvidersTable.vue";
import { Download, Printer, Document } from "@element-plus/icons-vue";

const props = defineProps({
    report: Object,
    filters: Object,
    mainServices: Array,
    pagination: Object,
});

const currentPage = computed(() => props.pagination?.currentPage || 1);
const perPage = computed(() => props.pagination?.perPage || 10);
const total = computed(() => props.pagination?.total || 0);

const filters = ref({
    dateRange: {
        start: null,
        end: null
    },
    mainService: ''
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("ar-SA", {
        style: "currency",
        currency: "SAR",
    }).format(value);
};

const applyFilters = () => {
    router.get(
        route("reports.provider-performance"),
        {
            dateRange: {
                start: filters.value.dateRange.start,
                end: filters.value.dateRange.end,
            },
            mainService: filters.value.mainService,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const exportReport = (type) => {
    window.location.href = route("reports.provider-performance", {
        ...filters.value,
        export: type,
    });
};
</script>

<style>
.el-date-editor.el-input,
.el-date-editor.el-input__wrapper {
    width: 100%;
}
</style>
