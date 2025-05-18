<template>
    <AuthenticatedLayout>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex flex-col space-y-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-xl text-gray-800">
                        {{ $t("reports.dashboard.title") }}
                    </h2>
                </div>

                <!-- Filters Section -->
                <div class="grid grid-cols-4 gap-4 mb-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            من تاريخ
                        </label>
                        <el-date-picker
                            v-model="dateRange[0]"
                            type="date"
                            placeholder="اختر التاريخ"
                            format="YYYY/MM/DD"
                            value-format="YYYY-MM-DD"
                            class="w-full"
                            @change="updateData"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            إلى تاريخ
                        </label>
                        <el-date-picker
                            v-model="dateRange[1]"
                            type="date"
                            placeholder="اختر التاريخ"
                            format="YYYY/MM/DD"
                            value-format="YYYY-MM-DD"
                            class="w-full"
                            @change="updateData"
                        />
                    </div>
                </div>

                <!-- Quick Filters -->
                <div class="flex gap-2 mb-4">
                    <el-button size="small" @click="setDateRange('week')">
                        آخر أسبوع
                    </el-button>
                    <el-button size="small" @click="setDateRange('month')">
                        آخر شهر
                    </el-button>
                    <el-button size="small" @click="setDateRange('quarter')">
                        آخر 3 شهور
                    </el-button>
                </div>

                <!-- Rest of your content -->
                <div class="py-6">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <!-- Users Summary -->
                        <el-card
                            shadow="hover"
                            :body-style="{ padding: '20px' }"
                        >
                            <div class="flex flex-col">
                                <div
                                    class="flex justify-between items-center mb-4"
                                >
                                    <span class="text-lg font-semibold"
                                        >المستخدمين</span
                                    >
                                    <el-tag
                                        :type="
                                            summaryData?.totalUsers?.growth >= 0
                                                ? 'success'
                                                : 'danger'
                                        "
                                        size="small"
                                    >
                                        {{
                                            formatGrowth(
                                                summaryData?.totalUsers
                                                    ?.growth || 0
                                            )
                                        }}
                                    </el-tag>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    
                                    <div
                                        class="text-center p-4 bg-green-50 rounded-lg"
                                    >
                                        <div
                                            class="text-2xl font-bold text-green-600"
                                        >
                                            {{
                                                summaryData?.totalUsers
                                                    ?.providers || 0
                                            }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            مزودي خدمة
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </el-card>

                        <!-- Revenue Summary -->
                        <el-card
                            shadow="hover"
                            :body-style="{ padding: '20px' }"
                        >
                            <div class="flex flex-col">
                                <div
                                    class="flex justify-between items-center mb-4"
                                >
                                    <span class="text-lg font-semibold"
                                        >الإيرادات</span
                                    >
                                    <el-tag
                                        :type="
                                            summaryData?.revenue?.growth >= 0
                                                ? 'success'
                                                : 'danger'
                                        "
                                        size="small"
                                    >
                                        {{
                                            formatGrowth(
                                                summaryData?.revenue?.growth ||
                                                    0
                                            )
                                        }}
                                    </el-tag>
                                </div>
                                <div
                                    class="text-3xl font-bold text-indigo-600 text-center mb-4"
                                >
                                    {{
                                        formatCurrency(
                                            summaryData?.revenue?.total || 0
                                        )
                                    }}
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-center">
                                        <div class="font-semibold">
                                            {{
                                                formatCurrency(
                                                    summaryData?.revenue
                                                        ?.breakdown
                                                        ?.subscriptions || 0
                                                )
                                            }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            اشتراكات
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-semibold">
                                            {{
                                                formatCurrency(
                                                    summaryData?.revenue
                                                        ?.breakdown
                                                        ?.contracts || 0
                                                )
                                            }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            عقود
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </el-card>

                        <!-- Subscriptions Summary -->
                        <el-card
                            shadow="hover"
                            :body-style="{ padding: '20px' }"
                        >
                            <div class="flex flex-col">
                                <div
                                    class="flex justify-between items-center mb-4"
                                >
                                    <span class="text-lg font-semibold"
                                        >الاشتراكات النشطة</span
                                    >
                                    <el-tag
                                        :type="
                                            summaryData?.subscriptions
                                                ?.growth >= 0
                                                ? 'success'
                                                : 'danger'
                                        "
                                        size="small"
                                    >
                                        {{
                                            formatGrowth(
                                                summaryData?.subscriptions
                                                    ?.growth || 0
                                            )
                                        }}
                                    </el-tag>
                                </div>
                                <div
                                    class="text-3xl font-bold text-purple-600 text-center"
                                >
                                    {{
                                        summaryData?.subscriptions?.active || 0
                                    }}
                                </div>
                                <div
                                    class="text-sm text-gray-600 text-center mt-2"
                                >
                                    من إجمالي
                                    {{ summaryData?.subscriptions?.total || 0 }}
                                </div>
                            </div>
                        </el-card>

                        <!-- Contracts Summary -->
                        <el-card
                            shadow="hover"
                            :body-style="{ padding: '20px' }"
                        >
                            <div class="flex flex-col">
                                <div
                                    class="flex justify-between items-center mb-4"
                                >
                                    <span class="text-lg font-semibold"
                                        >العقود النشطة</span
                                    >
                                    <el-tag
                                        :type="
                                            summaryData?.contracts?.growth >= 0
                                                ? 'success'
                                                : 'danger'
                                        "
                                        size="small"
                                    >
                                        {{
                                            formatGrowth(
                                                summaryData?.contracts
                                                    ?.growth || 0
                                            )
                                        }}
                                    </el-tag>
                                </div>
                                <div
                                    class="text-3xl font-bold text-blue-600 text-center"
                                >
                                    {{ summaryData?.contracts?.active || 0 }}
                                </div>
                                <div
                                    class="text-sm text-gray-600 text-center mt-2"
                                >
                                    من إجمالي
                                    {{ summaryData?.contracts?.total || 0 }}
                                </div>
                            </div>
                        </el-card>
                    </div>

                    <!-- Revenue Breakdown -->
                    <el-card shadow="hover" class="mb-8">
                        <template #header>
                            <div class="font-semibold">تفاصيل الإيرادات</div>
                        </template>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div
                                class="text-center p-4 bg-purple-50 rounded-lg"
                            >
                                <div class="text-sm text-gray-600">
                                    الاشتراكات
                                </div>
                                <div class="text-lg font-bold text-purple-600">
                                    {{
                                        formatCurrency(
                                            summaryData?.revenue?.breakdown
                                                ?.subscriptions || 0
                                        )
                                    }}
                                </div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-sm text-gray-600">العقود</div>
                                <div class="text-lg font-bold text-blue-600">
                                    {{
                                        formatCurrency(
                                            summaryData?.revenue?.breakdown
                                                ?.contracts || 0
                                        )
                                    }}
                                </div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-sm text-gray-600">
                                    عمولة النظام
                                </div>
                                <div class="text-lg font-bold text-green-600">
                                    {{
                                        formatCurrency(
                                            summaryData?.revenue?.breakdown
                                                ?.commission || 0
                                        )
                                    }}
                                </div>
                            </div>
                            <div
                                class="text-center p-4 bg-orange-50 rounded-lg"
                            >
                                <div class="text-sm text-gray-600">الضرائب</div>
                                <div class="text-lg font-bold text-orange-600">
                                    {{
                                        formatCurrency(
                                            summaryData?.revenue?.breakdown
                                                ?.tax || 0
                                        )
                                    }}
                                </div>
                            </div>
                        </div>
                    </el-card>

                    <!-- Charts -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <LineChart
                            title="اتجاهات الإيرادات"
                            :data="revenueData.trends"
                        />
                        <BarChart
                            title="نمو المستخدمين"
                            :data="userGrowthData.registration"
                        />
                    </div>

                    <!-- Service Analytics -->
                    <el-card shadow="hover">
                        <template #header>
                            <div class="font-semibold">
                                الخدمات الأكثر طلباً
                            </div>
                        </template>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="overflow-x-auto">
                                <el-table
                                    :data="serviceAnalytics?.popular || []"
                                    style="width: 100%"
                                >
                                    <el-table-column
                                        label="الخدمة"
                                        prop="name"
                                    />
                                    <el-table-column
                                        label="التصنيف"
                                        prop="category"
                                    />
                                    <el-table-column
                                        label="عدد الطلبات"
                                        prop="count"
                                    />
                                </el-table>
                            </div>
                            <PieChart
                                :data="serviceAnalytics?.categories || []"
                                :height="300"
                            />
                        </div>
                    </el-card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import LineChart from "@/Components/Charts/LineChart.vue";
import BarChart from "@/Components/Charts/BarChart.vue";
import PieChart from "@/Components/Charts/PieChart.vue";
import ar from "element-plus/es/locale/lang/ar";

const props = defineProps({
    summaryData: {
        type: Object,
        default: () => ({
            totalUsers: {  providers: 0, growth: 0 },
            revenue: {
                total: 0,
                breakdown: { subscriptions: 0, contracts: 0 },
                growth: 0,
            },
            subscriptions: { active: 0, total: 0, growth: 0 },
        }),
    },
    revenueData: {
        type: Object,
        default: () => ({
            trends: { labels: [], values: [] },
        }),
    },
    userGrowthData: {
        type: Object,
        default: () => ({
            registration: { labels: [], providers: [] },
        }),
    },
    serviceAnalytics: {
        type: Object,
        default: () => ({
            popular: [],
            categories: [],
        }),
    },
});

const dateRange = ref([null, null]);

const setDateRange = (period) => {
    const end = new Date();
    const start = new Date();

    switch (period) {
        case "week":
            start.setDate(end.getDate() - 7);
            break;
        case "month":
            start.setDate(end.getDate() - 30);
            break;
        case "quarter":
            start.setDate(end.getDate() - 90);
            break;
    }

    dateRange.value = [
        start.toISOString().split("T")[0],
        end.toISOString().split("T")[0],
    ];

    updateData();
};

const updateData = () => {
    if (!dateRange.value[0] || !dateRange.value[1]) return;

    router.get(
        route("reports.index"),
        {
            start_date: dateRange.value[0],
            end_date: dateRange.value[1],
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("ar-SA", {
        style: "currency",
        currency: "SAR",
    }).format(value);
};

const formatGrowth = (value) => {
    const sign = value >= 0 ? "+" : "";
    return `${sign}${value}%`;
};

const revenueData = ref(props.revenueData);
const userGrowthData = ref(props.userGrowthData);

const updateChartData = async ({ period, chartType }) => {
    const dates = getDateRange(period);

    try {
        const response = await router.get(
            route("reports.index"),
            {
                start_date: dates.start,
                end_date: dates.end,
            },
            {
                preserveState: true,
                preserveScroll: true,
                only: ["revenueData", "userGrowthData"],
            }
        );

        // تحديث البيانات مباشرة من الاستجابة
        if (chartType === "revenue") {
            revenueData.value = response.data.revenueData;
        } else if (chartType === "users") {
            userGrowthData.value = response.data.userGrowthData;
        }
    } catch (error) {
        console.error("Error updating chart data:", error);
    }
};

const getDateRange = (period) => {
    const end = new Date();
    const start = new Date();

    switch (period) {
        case "week":
            start.setDate(start.getDate() - 7);
            break;
        case "month":
            start.setDate(start.getDate() - 30);
            break;
        case "quarter":
            start.setMonth(start.getMonth() - 3);
            break;
        case "year":
            start.setFullYear(start.getFullYear() - 1);
            break;
    }

    return {
        start: start.toISOString().split("T")[0],
        end: end.toISOString().split("T")[0],
    };
};

const arLocale = ar;

onMounted(() => {
    const end = new Date();
    const start = new Date();
    start.setMonth(start.getMonth() - 1);
    dateRange.value = [
        start.toISOString().split("T")[0],
        end.toISOString().split("T")[0],
    ];
    updateData();
});
</script>

<style scoped>
.chart-card {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
}

.period-selector {
    min-width: 120px;
}

.el-date-range-picker {
    direction: rtl !important;
}
.el-date-range-picker__header {
    text-align: right !important;
}
.el-popper.is-pure {
    direction: rtl !important;
}

/* تنسيق الـ shortcuts */
.date-picker-popper .el-picker-panel__shortcut {
    text-align: right !important;
    padding: 0 20px 0 5px !important;
    height: 30px !important;
    line-height: 30px !important;
    font-size: 14px !important;
}

.date-picker-popper .el-picker-panel__sidebar {
    width: 120px !important;
}

/* تحسين شكل القائمة */
.el-picker-panel__sidebar {
    background: #f5f7fa !important;
    border-right: 1px solid #e4e7ed !important;
}
</style>
