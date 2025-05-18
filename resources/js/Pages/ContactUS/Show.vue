<template>
    <AuthenticatedLayout>
        <!-- Breadcrumb -->
        <div class="pagetitle mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1>{{ $t("details") }}</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <Link :href="route('dashboard')">{{
                                    $t("home")
                                }}</Link>
                            </li>
                            <li class="breadcrumb-item">
                                <Link :href="route('contacts.index')">{{
                                    $t("contacts")
                                }}</Link>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ $t("details") }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <Link
                        class="btn btn-outline-secondary"
                        :href="route('contacts.index')"
                    >
                        <el-icon class="me-1"><Back /></el-icon>
                        {{ $t("back") }}
                    </Link>
                    <DeleteAction
                        :id="contact.id"
                        :delete-url="route('contacts.destroy', contact.id)"
                    >
                        <template #default="{ handleClick }">
                            <button class="btn btn-danger" @click="handleClick">
                                <el-icon class="me-1"><Delete /></el-icon>
                                {{ $t("delete") }}
                            </button>
                        </template>
                    </DeleteAction>
                </div>
            </div>
        </div>

        <!-- Main Section -->
        <div class="row">
            <!-- Contact Info Card -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="card-title mb-0">
                            <el-icon class="me-2"><User /></el-icon>
                            {{ $t("contact_information") }}
                        </h5>
                        <span
                            :class="[
                                'status-badge',
                                contact.read ? 'read' : 'unread',
                            ]"
                        >
                            <el-icon class="me-1">
                                <component
                                    :is="contact.read ? 'Check' : 'Close'"
                                />
                            </el-icon>
                            {{ contact.read ? $t("read") : $t("not_read") }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">
                                <el-icon><Avatar /></el-icon>
                                {{ $t("name") }}:
                            </div>
                            <div class="info-value">{{ contact.name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <el-icon><Message /></el-icon>
                                {{ $t("email") }}:
                            </div>
                            <div class="info-value">
                                <a :href="'mailto:' + contact.email">{{
                                    contact.email
                                }}</a>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <el-icon><Phone /></el-icon>
                                {{ $t("phone") }}:
                            </div>
                            <div class="info-value">
                                <a :href="'tel:' + contact.phone" dir="ltr">{{
                                    contact.phone
                                }}</a>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <el-icon><Calendar /></el-icon>
                                {{ $t("created_at") }}:
                            </div>
                            <div class="info-value">
                                {{ formatDate(contact.created_at) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Card -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <el-icon class="me-2"><ChatDotRound /></el-icon>
                            {{ $t("message") }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="message-content">{{ contact.message }}</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import DeleteAction from "@/Components/DeleteAction.vue";
import {
    Back,
    Delete,
    User,
    Avatar,
    Message,
    Phone,
    Calendar,
    ChatDotRound,
    Check,
    Close,
} from "@element-plus/icons-vue";

const { t } = useI18n();
const props = defineProps({ contact: Object });

const formatDate = (date) => {
    return new Date(date).toLocaleString(undefined, {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>

<style scoped>
.card {
    border: 1px solid #eee;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
}

.status-badge.read {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.status-badge.unread {
    background-color: #ffebee;
    color: #c62828;
}

.info-item {
    padding: 12px 0;
    border-bottom: 1px solid #f5f5f5;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    color: #666;
    font-size: 0.95rem;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-value {
    color: #333;
    font-size: 1rem;
    padding-inline-start: 28px;
}

.info-value a {
    color: #333;
    text-decoration: none;
}

.info-value a:hover {
    text-decoration: underline;
}

.message-content {
    font-size: 1rem;
    line-height: 1.6;
    color: #333;
    white-space: pre-line;
}

.btn {
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

:deep(.el-icon) {
    font-size: 1.1em;
}
</style>
