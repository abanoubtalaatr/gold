<template>
    <AuthenticatedLayout>
        <div class="pagetitle">
            <h1>{{ $t("notification.notifications") }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link :href="route('dashboard')">{{
                            $t("dashboard")
                        }}</Link>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $t("notification.notifications") }}
                    </li>
                </ol>
            </nav>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-3">
                <el-date-picker
                    v-model="filters.date"
                    type="date"
                    format="DD/MM/YYYY"
                    placeholder="dd/mm/yyyy"
                    class="w-100"
                />
            </div>
            <div class="col-md-3">
                <el-select
                    v-model="filters.status"
                    :placeholder="$t('all_status')"
                    clearable
                    class="w-100"
                >
                    <el-option value="draft" :label="$t('draft')" />
                    <el-option value="scheduled" :label="$t('scheduled')" />
                    <el-option value="sent" :label="$t('sent')" />
                    <el-option value="failed" :label="$t('failed')" />
                </el-select>
            </div>
            <div class="col-md-4">
                <el-input
                    v-model="filters.search"
                    :placeholder="$t('search') + '...' "
                    clearable
                >
                    <template #prefix>
                        <i class="bi bi-search"></i>
                    </template>
                </el-input>
            </div>
            <div class="col-md-2">
                <Link
                    :href="route('notifications.create')"
                    class="btn btn-primary w-100"
                >
                    {{ $t("notification.create") }}
                </Link>

            </div>
        </div>

        <!-- Notifications Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <DataTable
                        :headers="tableHeaders"
                        :data="notifications.data"
                        :pagination-links="notifications.links"
                        @update:page="handlePageChange"
                    >
                        <template #created_at="{ data }">
                            {{ formatDate(data.created_at) }}
                        </template>
                        <template #status="{ data }">
                            <el-tag :type="getStatusType(data.status)">
                                {{ $t(data.status) }}
                            </el-tag>
                        </template>
                        <template #recipient_type="{ data }">
                            {{ getRecipientTypeLabel(data.recipient_type) }}
                        </template>

                        <template #actions="{ data }">
                            <div class="d-flex gap-2">
                                <EditButton
                                    v-if="
                                        ['draft', 'scheduled'].includes(
                                            data.status
                                        )
                                    "
                                    @click="
                                        router.get(
                                            route('notifications.edit', data.id)
                                        )
                                    "
                                />

                                <DeleteAction
                                    v-if="
                                        ['draft', 'scheduled'].includes(
                                            data.status
                                        )
                                    "
                                    :id="data.id"
                                    :delete-url="
                                        route('notifications.destroy', data.id)
                                    "
                                />
                            </div>
                        </template>
                    </DataTable>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import debounce from "lodash/debounce";
import { useI18n } from "vue-i18n";
import { Edit, Delete } from "@element-plus/icons-vue";
import DataTable from "@/Components/DataTable.vue";
import ActivateToggle from "@/Components/ActivateToggle.vue";
import DeleteAction from "@/Components/DeleteAction.vue";
import EditButton from "@/Components/EditButton.vue";

const { t } = useI18n();

const props = defineProps({
    notifications: Object,
    filters: Object,
});

// Filters state
const filters = ref({
    search: props.filters.search || "",
    status: props.filters.status || "",
    date: props.filters.date || "",
});
const tableHeaders = [
    { key: "id", label: t("Id") },
    { key: "title", label: t("title") },
    { key: "message", label: t("message") },
    { key: "recipient_type", label: t("notification.recipient_type") },
    { key: "status", label: t("status") },
    { key: "created_at", label: t("created_at") },
    { key: "actions", label: t("notification.actions") },
];
// Watch filters changes
watch(
    filters.value,
    debounce((value) => {
        router.get(route("notifications.index"), value, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }, 300)
);

// Status badge classes
const getStatusClass = (status) => {
    return (
        {
            draft: "badge bg-secondary",
            scheduled: "badge bg-info",
            sent: "badge bg-success",
            failed: "badge bg-danger",
        }[status] || "badge bg-secondary"
    );
};

// Delete notification
const deleteNotification = (notification) => {
    if (confirm(t("notification.confirm_delete"))) {
        router.delete(route("notifications.destroy", notification.id));
    }
};

// Format date
const formatDate = (date) => {
    return new Date(date).toLocaleDateString("ar-SA", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

// Get status type for el-tag
const getStatusType = (status) => {
    return (
        {
            draft: "info",
            scheduled: "warning",
            sent: "success",
            failed: "danger",
        }[status] || "info"
    );
};

// Get recipient type label
const getRecipientTypeLabel = (type) => {
    return (
        {
            all: t("notification.all_users"),
            service_providers: t("notification.service_providers"),
            individual: t("notification.individual"),
        }[type] || type
    );
};

// console.log(t("notification.all_users") + " " + JSON.stringify(props.notifications));
</script>

<style scoped>
.table td {
    vertical-align: middle;
}

/* Limit message length */
td:nth-child(3) {
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.el-button.is-circle {
    padding: 6px;
}
</style>
