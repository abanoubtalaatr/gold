<template>
    <el-card>
        <div class="overflow-x-auto">
            <el-table
                :data="providers"
                style="width: 100%"
                :default-sort="{ prop: 'bookings_count', order: 'descending' }"
                v-loading="loading"
                :empty-text="$t('common.no_data')"
            >
                <el-table-column type="expand">
                    <template #default="props">
                        <div class="p-4 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- تفاصيل الخدمات -->
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h4 class="text-lg font-semibold mb-3">
                                        {{ $t('reports.provider_performance.table.services') }}
                                    </h4>
                                    <div class="space-y-2">
                                        <div
                                            v-if="
                                                props.row.main_services?.length
                                            "
                                        >
                                            <p
                                                class="text-sm text-gray-600 mb-1"
                                            >
                                                {{ $t('reports.provider_performance.table.main_services') }}:
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                <el-tag
                                                    v-for="service in props.row
                                                        .main_services"
                                                    :key="service"
                                                    type="success"
                                                    effect="plain"
                                                >
                                                    {{ service }}
                                                </el-tag>
                                            </div>
                                        </div>
                                        <div
                                            v-if="
                                                props.row.sub_services?.length
                                            "
                                        >
                                            <p
                                                class="text-sm text-gray-600 mb-1"
                                            >
                                                {{ $t('reports.provider_performance.table.sub_services') }}:
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                <el-tag
                                                    v-for="service in props.row
                                                        .sub_services"
                                                    :key="service"
                                                    type="info"
                                                    effect="plain"
                                                >
                                                    {{ service }}
                                                </el-tag>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- إحصائيات المزود -->
                                <div class="bg-white p-4 rounded-lg shadow-sm">
                                    <h4 class="text-lg font-semibold mb-3">
                                        {{ $t('statistics') }}
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-600">
                                                {{ $t('reports.provider_performance.table.bookings_count') }}
                                            </p>
                                            <p class="text-xl font-semibold">
                                                {{ props.row.bookings_count }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">
                                                {{ $t('reports.provider_performance.table.revenue') }}
                                            </p>
                                            <p class="text-xl font-semibold">
                                                {{
                                                    formatCurrency(
                                                        props.row.revenue
                                                    )
                                                }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">
                                                {{ $t('reports.provider_performance.table.rating') }}
                                            </p>
                                            <el-rate
                                                v-model="
                                                    props.row.average_rating
                                                "
                                                disabled
                                                show-score
                                                text-color="#ff9900"
                                            />
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">
                                             {{ $t('reports.provider_performance.table.subscription_plan') }}
                                            </p>
                                            <el-tag
                                                :type="
                                                    getSubscriptionTagType(
                                                        props.row
                                                            .subscription_plan
                                                    )
                                                "
                                                class="mt-1"
                                            >
                                                {{
                                                    props.row.subscription_plan
                                                }}
                                            </el-tag>
                                        </div>
                                    </div>
                                </div>

                                <!-- معلومات التواصل -->
                                <div
                                    class="bg-white p-4 rounded-lg shadow-sm md:col-span-2"
                                >
                                    <h4 class="text-lg font-semibold mb-3">
                                        {{ $t('contact_information') }}
                                    </h4>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-4"
                                    >
                                        <div>
                                            <p class="text-sm text-gray-600">
                                                {{ $t('email') }}
                                            </p>
                                            <p class="font-medium">
                                                {{
                                                    props.row.email ||
                                                    "غير متوفر"
                                                }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">
                                                {{ $t('phone') }}
                                            </p>
                                            <p class="font-medium">
                                                {{
                                                    props.row.phone ||
                                                    "غير متوفر"
                                                }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">
                                                {{ $t('reports.provider_performance.address') }}
                                            </p>
                                            <p class="font-medium">
                                                {{
                                                    props.row.address ||
                                                    "غير متوفر"
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </el-table-column>

                <el-table-column type="index" label="#" width="60" />

                <el-table-column prop="name" :label="$t('reports.provider_performance.table.provider_name')" />

                <el-table-column
                    prop="bookings_count"
                    :label="$t('reports.provider_performance.table.bookings_count')"
                    sortable
                    width="120"
                />

                <el-table-column
                    prop="average_rating"
                    :label="$t('reports.provider_performance.table.rating')"
                    width="150"
                    sortable
                >
                    <template #default="{ row }">
                        <el-rate
                            v-model="row.average_rating"
                            disabled
                            show-score
                            text-color="#ff9900"
                        />
                    </template>
                </el-table-column>

                <el-table-column
                    prop="revenue"
                    :label="$t('reports.provider_performance.table.revenue')"
                    sortable
                    width="150"
                >
                    <template #default="{ row }">
                        {{ formatCurrency(row.revenue) }}
                    </template>
                </el-table-column>


            </el-table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-end mt-4 rtl:space-x-reverse">
            <el-config-provider :locale="currentLocale">
                <el-pagination
                    v-model:current-page="currentPage"
                    v-model:page-size="pageSize"
                    :page-sizes="[10, 20, 50, 100]"
                    :layout="paginationLayout"
                    :total="props.pagination.total"
                    :prev-text="$t('pagination.previous')"
                    :next-text="$t('pagination.next')"
                    class="pagination-wrapper"
                    @size-change="handleSizeChange"
                    @current-change="handleCurrentChange"
                />
            </el-config-provider>
        </div>
    </el-card>
</template>

<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { ElConfigProvider } from "element-plus";
import ar from "element-plus/dist/locale/ar";
import en from "element-plus/dist/locale/en";

const props = defineProps({
    providers: {
        type: Array,
        required: true,
    },
    pagination: {
        type: Object,
        required: true,
        default: () => ({
            currentPage: 1,
            perPage: 10,
            total: 0,
        }),
    },
    filters: {
        type: Object,
        required: true,
    },
});

const loading = ref(false);
const page = usePage();

const currentPage = ref(props.pagination?.currentPage || 1);
const pageSize = ref(props.pagination?.perPage || 10);

const currentLocale = computed(() => {
    return page.props.locale === "ar" ? ar : en;
});

const paginationLayout = computed(() => {
    return window.innerWidth < 640
        ? "prev, pager, next"
        : "total, sizes, prev, pager, next";
});

const handleSizeChange = (val) => {
    loading.value = true;
    pageSize.value = val;
    currentPage.value = 1;
    router.get(
        route("reports.provider-performance"),
        {
            ...props.filters,
            perPage: val,
            page: 1,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => (loading.value = false),
        }
    );
};

const handleCurrentChange = (val) => {
    loading.value = true;
    currentPage.value = val;
    router.get(
        route("reports.provider-performance"),
        {
            ...props.filters,
            page: val,
            perPage: pageSize.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => (loading.value = false),
        }
    );
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("ar-SA", {
        style: "currency",
        currency: "SAR",
    }).format(value);
};

const getSubscriptionTagType = (plan) => {
    const types = {
        Free: "info",
        Basic: "success",
        Premium: "warning",
    };
    return types[plan] || "info";
};
</script>

<style scoped>
.el-table :deep(.el-table__expanded-cell) {
    padding: 0;
}

.el-table :deep(.el-table__expand-icon) {
    transform: rotate(90deg);
}

.el-table :deep(.el-table__expand-icon--expanded) {
    transform: rotate(180deg);
}

.pagination-wrapper {
    @apply flex flex-wrap justify-end gap-2;
    direction: ltr;
}

.pagination-wrapper :deep(.el-pagination__total),
.pagination-wrapper :deep(.el-pagination__sizes) {
    @apply mb-2 sm:mb-0;
}

@media (max-width: 640px) {
    .pagination-wrapper {
        @apply justify-center;
    }
}

:deep([dir="rtl"]) .el-pagination .btn-prev .el-icon,
:deep([dir="rtl"]) .el-pagination .btn-next .el-icon {
    transform: rotate(180deg);
}
</style>
