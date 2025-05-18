<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">
                    {{ $t("reports.user_activity.title") }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                    <el-card
                        v-for="(value, key) in statistics"
                        :key="key"
                        :class="statisticsClasses[key]"
                    >
                        <template #header>
                            <div class="flex items-center justify-between">
                                <span>{{
                                    $t(
                                        `reports.user_activity.summary.${key}`
                                    )
                                }}</span>
                                <el-icon :class="statisticsClasses[key]">
                                    <component :is="statisticsIcons[key]" />
                                </el-icon>
                            </div>
                        </template>
                        <div class="text-2xl font-bold">{{ value }}</div>
                    </el-card>
                </div>

                <!-- Filters Card -->
                <el-card class="mb-6">
                    <template #header>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <el-icon><Filter /></el-icon>
                                <span class="mr-2">{{
                                    $t("reports.user_activity.filters.title")
                                }}</span>
                            </div>
                            <div class="flex gap-2">
                                <el-button
                                type="success"
                                @click="exportReport('excel')"
                                :icon="Document"
                                class="flex items-center gap-2"
                            >
                                <span> {{ $t('reports.export.excel') }}</span>
                            </el-button>
                                  <el-button
                                type="primary"
                                @click="exportReport('pdf')"
                                :icon="Printer"
                                class="flex items-center gap-2"
                            >
                                <span> {{ $t('reports.export.pdf') }}</span>
                            </el-button>
                            </div>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <el-form-item
                            :label="
                                $t('reports.user_activity.filters.user_type')
                            "
                        >
                            <el-select v-model="filters.type" clearable :placeholder="$t('reports.user_activity.filters.all_types')">
                                <el-option
                                    :label="$t('reports.user_activity.filters.all_types')"
                                    value=""
                                />
                                <el-option
                                    v-for="type in userTypes"
                                    :key="type.value"
                                    :label="type.label"
                                    :value="type.value"
                                />
                            </el-select>
                        </el-form-item>

                        <el-form-item
                            :label="
                                $t('reports.user_activity.filters.activity')
                            "
                        >
                            <el-select v-model="filters.activity" clearable :placeholder="$t('reports.user_activity.filters.all_statuses')">
                                <el-option
                                    :label="$t('reports.user_activity.filters.all_statuses')"
                                    value=""
                                />
                                <el-option
                                    v-for="status in activityStatuses"
                                    :key="status.value"
                                    :label="status.label"
                                    :value="status.value"
                                />
                            </el-select>
                        </el-form-item>

                        <el-form-item
                            :label="
                                $t('reports.user_activity.filters.date_from')
                            "
                        >
                            <el-date-picker
                                v-model="filters.date_from"
                                type="date"
                            />
                        </el-form-item>

                        <el-form-item
                            :label="$t('reports.user_activity.filters.date_to')"
                        >
                            <el-date-picker
                                v-model="filters.date_to"
                                type="date"
                            />
                        </el-form-item>
                    </div>

                    <div
                        class="flex justify-end space-x-3 rtl:space-x-reverse mt-4"
                    >
                        <el-button @click="resetFilters">
                            {{ $t("commons.reset") }}
                        </el-button>
                        <el-button type="primary" @click="applyFilters">
                            {{ $t("commons.apply") }}
                        </el-button>
                    </div>
                </el-card>

                <!-- Results Table -->

                    <UserActivityTable
                        :users="users"
                        :pagination="pagination"
                        @size-change="handleSizeChange"
                        @current-change="handleCurrentChange"
                    />
           
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import {
    Filter,
    Document,
    Printer,
    User,
    Timer,
    Connection,
    HomeFilled,
    Shop,
} from "@element-plus/icons-vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UserActivityTable from "@/Components/Reports/UserActivityTable.vue";

const props = defineProps({
    users: Array,
    statistics: Object,
    filters: Object,
    pagination: Object,
});

const statisticsClasses = {
    total_users: "bg-blue-50",
    active_users: "bg-green-50",
    online_users: "bg-yellow-50",
    providers: "bg-pink-50",
};

const statisticsIcons = {
    total_users: "User",
    active_users: "Timer",
    online_users: "Connection",
    providers: "Shop",
};

const userTypes = [
    { value: "provider", label: "مزود خدمة" },
];

const activityStatuses = [
    { value: "online", label: "متصل الآن" },
    { value: "active", label: "نشط" },
    { value: "inactive", label: "غير نشط" },
];

const filters = ref({
    date_from: props.filters.date_from,
    date_to: props.filters.date_to,
    type: props.filters.type,
    activity: props.filters.activity,
});

const resetFilters = () => {
    filters.value = {
        date_from: "",
        date_to: "",
        type: "",
        activity: "",
    };
    applyFilters();
};

const applyFilters = () => {
    router.get(route("reports.user-activity"), filters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};



const exportReport = (type) => {
    window.location.href = route("reports.user-activity", {
        ...filters.value,
        export: type,
    });
};

const handleSizeChange = (val) => {
    router.get(
        route("reports.user-activity"),
        {
            ...filters.value,
            per_page: val,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const handleCurrentChange = (val) => {
    router.get(
        route("reports.user-activity"),
        {
            ...filters.value,
            page: val,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

// تحديث التنسيق للتاريخ في العربية
const formatLastSeen = (date) => {
    if (!date || date === "-") return "لم يسجل دخول بعد";

    const now = new Date();
    const lastSeen = new Date(date);
    const diffInMinutes = Math.floor((now - lastSeen) / (1000 * 60));

    if (diffInMinutes < 1) return "الآن";
    if (diffInMinutes < 60) return `منذ ${diffInMinutes} دقيقة`;

    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `منذ ${diffInHours} ساعة`;

    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 30) return `منذ ${diffInDays} يوم`;

    return lastSeen.toLocaleDateString("ar-SA");
};
</script>

<style scoped>
.el-card {
    transition: all 0.3s ease;
}

.el-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.statistics-icon {
    font-size: 24px;
    opacity: 0.7;
}

.el-switch {
    margin-right: 1rem;
}

@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;
    }
}

.auto-refresh-indicator {
    animation: pulse 2s infinite;
}

.online-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 6px;
}

.online {
    background-color: #10b981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
}

.offline {
    background-color: #9ca3af;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.8;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.online {
    animation: pulse 2s infinite;
}
</style>
