<template>
    <el-card>
        <div class="overflow-x-auto">
            <el-table
                :data="users"
                style="width: 100%"
                :default-sort="{ prop: 'activity_date', order: 'descending' }"
                v-loading="loading"
                :empty-text="$t('common.no_data')"
            >

                <el-table-column
                    prop="full_name"
                    :label="$t('reports.user_activity.table.user_name')"
                />
                <el-table-column
                    prop="email"
                    :label="$t('reports.user_activity.table.email')"
                />
                <el-table-column
                    prop="registration_date"
                    :label="$t('reports.user_activity.table.registration_date')"
                    width="150"
                    sortable
                />

                <el-table-column
                            prop="status"
                            label="الحالة"
                            width="100"
                        >
                            <template #default="{ row }">
                                <el-tag
                                    :type="
                                        row.status === 'نشط'
                                            ? 'success'
                                            : 'danger'
                                    "
                                    size="small"
                                >
                                    {{ row.status }}
                                </el-tag>
                            </template>
                        </el-table-column>

                        <el-table-column label="حالة المستخدم" width="200">
                            <template #default="{ row }">
                                <div class="flex flex-col gap-2">
                                    <!-- حالة الاتصال -->
                                    <div class="flex items-center">
                                        <span
                                            :class="[
                                                'w-2 h-2 rounded-full mr-2',
                                                row.is_online
                                                    ? 'bg-green-500'
                                                    : 'bg-gray-400',
                                            ]"
                                        ></span>
                                        <span
                                            :class="
                                                row.is_online
                                                    ? 'text-green-600'
                                                    : 'text-gray-600'
                                            "
                                        >
                                            {{
                                                row.is_online
                                                    ? "متصل الآن"
                                                    : "غير متصل"
                                            }}
                                        </span>
                                    </div>

                                    <!-- آخر دخول -->
                                    <div class="text-sm text-gray-500">
                                        <template v-if="row.last_login !== '-'">
                                            <el-tooltip
                                                :content="row.last_login"
                                                placement="top"
                                            >
                                                <span
                                                    >آخر دخول:
                                                    {{
                                                        row.activity_status
                                                    }}</span
                                                >
                                            </el-tooltip>
                                        </template>
                                        <template v-else>
                                            لم يسجل دخول بعد
                                        </template>
                                    </div>
                                </div>
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
import { usePage, router } from "@inertiajs/vue3"; // تأكد من استيراد usePage و router
import ar from "element-plus/dist/locale/ar";
import en from "element-plus/dist/locale/en";
const props = defineProps({
    users: {
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

console.log(props.users);

const currentPage = ref(props.pagination?.currentPage || 1);
const pageSize = ref(props.pagination?.perPage || 10);

// تحديد اللغة الحالية
const currentLocale = computed(() => {
    return page.props.locale === "ar" ? ar : en;
});

// تحديد شكل الباجينيشن حسب حجم الشاشة
const paginationLayout = computed(() => {
    return window.innerWidth < 640
        ? "prev, pager, next"
        : "total, sizes, prev, pager, next";
});

const handleSizeChange = (val) => {
    loading.value = true;
    pageSize.value = val; // تحديث حجم الصفحة
    currentPage.value = 1; // إعادة تعيين الصفحة الحالية إلى 1
    router.get(
        route("reports.user-activity"),
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

const handleCurrentChange = (val) => {
    loading.value = true;
    currentPage.value = val; // تحديث الصفحة الحالية
    router.get(
        route("reports.user-activity"),
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

<style scoped>
/* أضف أي أنماط إضافية هنا إذا لزم الأمر */
</style>
