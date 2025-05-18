<template>
    <AuthenticatedLayout>
        <div class="pagetitle">
            <h1>{{ $t("notification.edit") }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link :href="route('dashboard')">{{
                            $t("dashboard")
                        }}</Link>
                    </li>
                    <li class="breadcrumb-item">
                        <Link :href="route('notifications.index')">{{
                            $t("notification.notifications")
                        }}</Link>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $t("notification.edit") }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="card">
            <div class="card-body">
                <form @submit.prevent="submit">
                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label required">{{
                            $t("notification.title")
                        }}</label>
                        <el-input
                            v-model="form.title"
                            :placeholder="$t('notification.title')"
                            :class="{ 'is-invalid': form.errors.title }"
                        />
                        <div class="invalid-feedback">
                            {{ form.errors.title }}
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="mb-3">
                        <label class="form-label required">{{
                            $t("notification.message")
                        }}</label>
                        <el-input
                            v-model="form.message"
                            type="textarea"
                            :rows="4"
                            :placeholder="$t('notification.message')"
                            :class="{ 'is-invalid': form.errors.message }"
                        />
                        <div class="invalid-feedback">
                            {{ form.errors.message }}
                        </div>
                    </div>

                    <!-- Recipient Type -->
                    <div class="mb-3">
                        <label class="form-label required">{{
                            $t("notification.recipient_type")
                        }}</label>
                        <el-select
                            v-model="form.recipient_type"
                            class="w-100"
                            :placeholder="$t('notification.recipient_type')"
                            :class="{
                                'is-invalid': form.errors.recipient_type,
                            }"
                        >
                            <el-option
                                value="all"
                                :label="$t('notification.all_users')"
                            />
                            <!-- <el-option
                                value="service_providers"
                                :label="$t('notification.service_providers')"
                            />
                            <el-option
                                value="individual"
                                :label="$t('notification.individual')"
                            /> -->
                        </el-select>
                        <div class="invalid-feedback">
                            {{ form.errors.recipient_type }}
                        </div>
                    </div>

                    <!-- Individual Recipient -->
                    <div
                        v-if="form.recipient_type === 'individual'"
                        class="mb-3"
                    >
                        <label class="form-label required">{{
                            $t("notification.select_recipient")
                        }}</label>
                        <el-select
                            v-model="form.recipient_ids"
                            multiple
                            filterable
                            class="w-100"
                            :placeholder="$t('notification.select_recipients')"
                            :class="{ 'is-invalid': form.errors.recipient_ids }"
                        >
                            <el-option
                                v-for="user in props.users"
                                :key="user.id"
                                :label="user.name + ' - ' + user.email"
                                :value="user.id"
                            >
                                <div class="user-option">
                                    <div class="user-name">{{ user.name }}  &nbsp;  &nbsp; &nbsp; <span class="user-email text-muted">{{ user.email }}</span> </div>
                                </div>
                            </el-option>
                        </el-select>
                        <div class="invalid-feedback">
                            {{ form.errors.recipient_ids }}
                        </div>
                    </div>

                    <!-- Schedule -->
                    <div class="mb-3">
                        <el-checkbox v-model="isScheduled">{{
                            $t("notification.schedule")
                        }}</el-checkbox>
                    </div>

                    <!-- Schedule DateTime -->
                    <div v-if="isScheduled" class="mb-3">
                        <label class="form-label required">{{
                            $t("notification.schedule_time")
                        }}</label>
                        <el-date-picker
                            v-model="form.scheduled_at"
                            type="datetime"
                            :placeholder="$t('notification.schedule_time')"
                            format="YYYY-MM-DD HH:mm"
                            value-format="YYYY-MM-DD HH:mm"
                            :class="{ 'is-invalid': form.errors.scheduled_at }"
                            class="w-100"
                        />
                        <div class="invalid-feedback">
                            {{ form.errors.scheduled_at }}
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2 justify-content-end">
                        <el-button
                            type="info"
                            @click="saveAsDraft"
                            :loading="form.processing"
                        >
                            {{ $t("notification.save_draft") }}
                        </el-button>
                        <el-button
                            type="primary"
                            @click="sendNow"
                            :loading="form.processing"
                        >
                            {{ $t("notification.send_now") }}
                        </el-button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    notification: Object,
    users: Array,
});

const isScheduled = ref(!!props.notification.scheduled_at);
const loading = ref(false);

const form = useForm({
    title: props.notification.title,
    message: props.notification.message || props.notification.data?.message,
    recipient_type: props.notification.recipient_type,
    recipient_ids:
        props.notification.recipient_type === "individual"
            ? props.notification.recipient_id
            : [],
    scheduled_at: props.notification.scheduled_at,
    status: props.notification.status,
});

const sendNow = () => {
    form.status = "sent";
    form.post(route("notifications.update", props.notification.id));
};

const saveAsDraft = () => {
    form.status = "draft";
    form.post(route("notifications.update", props.notification.id));
};
</script>

<style scoped>
.required:after {
    content: " *";
    color: var(--el-color-danger);
}

:deep(.el-input__wrapper),
:deep(.el-textarea__wrapper),
:deep(.el-select) {
    width: 100%;
}

:deep(.el-select .el-input) {
    width: 100%;
}

.is-invalid :deep(.el-input__wrapper),
.is-invalid :deep(.el-textarea__wrapper),
.is-invalid :deep(.el-select .el-input__wrapper) {
    box-shadow: 0 0 0 1px var(--el-color-danger) inset;
}

.el-select :deep(.el-select-dropdown__item) {
    padding: 8px 12px;
}

.user-option {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.user-name {
    font-weight: 600;
    font-size: 14px;
}

.user-email {
    font-size: 12px;
    color: #909399;
}
</style>
