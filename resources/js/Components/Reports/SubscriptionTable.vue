<template>
    <el-card>
        <div class="overflow-x-auto">
            <el-table
                :data="subscriptions"
                style="width: 100%"
                :default-sort="{ prop: 'start_date', order: 'descending' }"
                v-loading="loading"
                :empty-text="$t('common.no_data')"
            >
                <el-table-column
                    prop="subscription_id"
                    :label="$t('reports.subscription.table.subscription_id')"
                    width="120"
                />
                <el-table-column
                    prop="provider_name"
                    :label="$t('reports.subscription.table.provider_name')"
                />
                <el-table-column
                    prop="plan"
                    :label="$t('reports.subscription.table.plan')"
                />
                <el-table-column
                    :label="$t('reports.subscription.table.amount')"
                    width="150"
                >
                    <template #default="{ row }">
                        {{ formatCurrency(row.amount) }}
                    </template>
                </el-table-column>
                <el-table-column
                    prop="start_date"
                    :label="$t('reports.subscription.table.start_date')"
                    width="120"
                />
                <el-table-column
                    prop="end_date"
                    :label="$t('reports.subscription.table.end_date')"
                    width="120"
                />
                <el-table-column
                    :label="$t('reports.subscription.table.status')"
                    width="120"
                >
                    <template #default="{ row }">
                        <el-tag :type="getStatusType(row.status)" size="small">
                            {{ getStatusLabel(row.status) }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column
                    :label="$t('reports.subscription.table.renewal_status')"
                    width="120"
                >
                    <template #default="{ row }">
                        <el-tag
                            :type="getRenewalStatusType(row.renewal_status)"
                            size="small"
                        >
                            {{ getRenewalStatusLabel(row.renewal_status) }}
                        </el-tag>
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
                    :total="pagination.total"
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
import { usePage, router } from "@inertiajs/vue3";
import ar from "element-plus/dist/locale/ar";
import en from "element-plus/dist/locale/en";
import { useI18n } from "vue-i18n";
const props = defineProps({
    subscriptions: {
        type: Array,
        required: true,
    },
    pagination: {
        type: Object,
        required: true,
    },
});

const loading = ref(false);
const page = usePage();
const { t } = useI18n();
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
        route("reports.subscription"),
        {
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

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "SAR",
    }).format(amount);
};

const getStatusType = (status) => {
    const types = {
        expired: "danger",
        active: "success",
        canceled: "info",
    };
    return types[status] || "info";
};

const getStatusLabel = (status) => {
    const labels = {
        expired: t("expired"),
        active: t("active"),
        canceled: t("canceled"),
    };
    return labels[status] || status;
};

const getRenewalStatusLabel = (status) => {
    const labels = {
        pending_renewal: t("pending_renewal"),
        active: t("active"),
        canceled: t("canceled"),
    };
    return labels[status] || status;
};

const getRenewalStatusType = (status) => {
    const types = {
        pending_renewal: "warning",
        active: "success",
        canceled: "info",
    };
    return types[status] || "info";
};

const handleCurrentChange = (val) => {
    loading.value = true;
    currentPage.value = val;
    router.get(
        route("reports.subscription"),
        {
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
</script>

<style scoped></style>
