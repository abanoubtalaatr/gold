<template>
    <AuthenticatedLayout>

        <div class="pagetitle row">
            <BreadcrumbComponent
                :pageTitle="$t('notifications')"
                :homeLabel="$t('home')"
            />
        </div>


        <section class="section dashboard">
            <div class="row justify-center">
                <div class="col-lg-12">
                    <el-card shadow="hover" class="w-full">
                        <h5 class="card-title text-lg font-semibold mb-4">
                            {{ $t("recent_notifications") }}
                        </h5>
                        <el-divider></el-divider>

                        <div v-if="notifications.data.length" class="activity">
                            <div
                                v-for="notification in notifications.data"
                                :key="notification.id"
                                class="notification-item flex justify-between items-center p-4 mb-2 bg-gray-50 rounded-md shadow-sm"
                            >
                                <div
                                    class="notification-content flex items-center"
                                >
                                    <el-icon class="text-blue-500 mr-4">
                                        <i class="bi bi-bell-fill"></i>
                                    </el-icon>
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{
                                                ($page.props.locale === "ar" &&
                                                    notification.data.ar
                                                        ?.body) ||
                                                ($page.props.locale === "en" &&
                                                    notification.data.en
                                                        ?.body) ||
                                                ($page.props.locale === "ar" &&
                                                    notification.data.message
                                                        ?.ar) ||
                                                ($page.props.locale === "en" &&
                                                    notification.data.message
                                                        ?.en) ||
                                                notification.data.message
                                            }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{
                                                formatDate(
                                                    notification.created_at
                                                )
                                            }}
                                        </p>
                                        <div>
                                            <el-link
                                                v-if="
                                                    notification.data.file_path
                                                "
                                                :href="
                                                    notification.data.file_path
                                                "
                                                target="_blank"
                                                type="primary"
                                                class="mr-2"
                                            >
                                                {{ $t("download_file") }}
                                            </el-link>
                                            <el-link
                                                v-if="notification.data.url"
                                                :href="notification.data.url"
                                                target="_blank"
                                                type="primary"
                                            >
                                                {{ $t("show") }}
                                            </el-link>
                                        </div>
                                    </div>
                                </div>
                                <el-badge
                                    v-if="!notification.read_at"
                                    :value="$t('new')"
                                    class="text-red-500"
                                ></el-badge>
                            </div>
                        </div>

                        <el-empty
                            v-else
                            :description="$t('no_notifications_found')"
                        ></el-empty>
                    </el-card>
                </div>
            </div>

            <Pagination :links="notifications.links" />
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Link } from "@inertiajs/vue3";
import { usePage } from "@inertiajs/vue3";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";

import {
    ElBadge,
    ElCard,
    ElDivider,
    ElEmpty,
    ElIcon,
    ElBreadcrumb,
    ElBreadcrumbItem,
} from "element-plus";
import { computed } from "vue";

const props = defineProps({
    notifications: Object,
});

const page = usePage();

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleString("ar-EG", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
    });
};

const arrowDirection = computed(
    () => `el-icon-arrow-${page.props.locale === "ar" ? "right" : "left"}`
);
</script>

<style scoped>
.notification-item {
    transition: background-color 0.3s;
}
.notification-item:hover {
    background-color: #f0f9ff;
}

:deep(.el-breadcrumb) {
    direction: rtl;
}

[dir="rtl"] :deep(.el-breadcrumb__separator) {
    transform: rotate(180deg);
}
</style>
