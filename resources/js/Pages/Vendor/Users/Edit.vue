<template>
    <AuthenticatedLayout>
        <div class="page-content">
            <el-card class="box-card">
                <template #header>
                    <div class="card-header">
                        <h3>{{ $t("edit_user") }}</h3>
                    </div>
                </template>

                <el-form :model="form" @submit.prevent="update" label-position="top" class="row g-3">
                    <!-- Avatar Upload -->
                    <el-form-item class="col-md-12">
                        <div class="avatar-upload">
                            <img :src="avatarPreview ||
                                props.user.avatar ||
                                '/dashboard-assets/img/default-avatar.png'
                                " class="avatar-preview" />
                            <el-upload class="upload-btn" accept="image/*" :auto-upload="false" :show-file-list="false"
                                @change="handleAvatarChange">
                                <el-button type="primary">
                                    <i class="bi bi-camera"></i>
                                    {{ $t("change_avatar") }}
                                </el-button>
                            </el-upload>
                            <div v-if="form.errors.avatar" class="error-message">
                                {{ form.errors.avatar }}
                            </div>
                        </div>
                    </el-form-item>

                    <!-- Name Translations -->
                    <div class="col-md-4">
                        <el-form-item :label="$t('name')">
                            <el-input v-model="form.name" :placeholder="$t('name')
                                " />
                            <div v-if="form.errors[`name`]" class="error-message">
                                {{ form.errors[`name`] }}
                            </div>
                        </el-form-item>
                    </div>

                    <!-- Email -->
                    <div class="col-md-4">
                        <el-form-item :label="$t('email')">
                            <el-input v-if="isSuperAdmin(props.user)" v-model="form.email" type="email" disabled
                                :placeholder="$t('enter_email')" />
                            <el-input v-else v-model="form.email" type="email" :placeholder="$t('enter_email')" />
                            <div v-if="form.errors.email" class="error-message">
                                {{ form.errors.email }}
                            </div>
                        </el-form-item>
                    </div>

                    <!-- Password -->
                    <div class="col-md-4" v-if="!isSuperAdmin(props.user)">
                        <el-form-item :label="$t('password')">
                            <el-input v-model="form.password" type="password" :placeholder="$t('enter_new_password')"
                                show-password />
                            <div v-if="form.errors.password" class="error-message">
                                {{ form.errors.password }}
                            </div>
                        </el-form-item>
                    </div>

                    <!-- Password -->
                    <div class="col-md-4" v-if="!isSuperAdmin(props.user)">
                        <el-form-item :label="$t('password_confirmation')">
                            <el-input v-model="form.password_confirmation" type="password"
                                :placeholder="$t('password_confirmation')" show-password />
                            <div v-if="form.errors.password_confirmation" class="error-message">
                                {{ form.errors.password_confirmation }}
                            </div>
                        </el-form-item>
                    </div>

                    <!-- Roles -->
                    <div class="col-md-4" v-if="!isSuperAdmin(props.user)">
                        <el-form-item :label="$t('roles')">
                            <el-select v-model="form.selectedRoles" multiple filterable
                                :placeholder="$t('select_roles')" class="w-100">
                                <el-option v-for="role in validRoles" :key="role" :label="role" :value="role" />
                            </el-select>
                            <div v-if="form.errors.selectedRoles" class="error-message">
                                {{ form.errors.selectedRoles }}
                            </div>
                        </el-form-item>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12">
                        <el-button type="primary" :loading="show_loader" @click="update" class="submit-btn">
                            {{ $t("update") }}
                        </el-button>
                    </div>
                </el-form>
            </el-card>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.page-content {
    padding: 20px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.avatar-upload {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.avatar-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--el-border-color);
}

.upload-btn {
    display: flex;
    justify-content: center;
}

.error-message {
    color: var(--el-color-danger);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.submit-btn {
    min-width: 120px;
}

:deep(.el-upload) {
    width: auto;
}

:deep(.el-select) {
    width: 100%;
}
</style>

<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import settings from "@/src/config/settings";

const show_loader = ref(false);
const avatarPreview = ref(null);

const props = defineProps({
    user: Object,
    userRoles: Array,
    roles: Object,
});

const form = useForm({
    avatar: null,
    name: props.user.name,
    email: props.user.email,
    password: "",
    password_confirmation: "",
    selectedRoles: props.userRoles,
});

const validRoles = computed(() => {
    console.log("Roles:", props.roles);
    if (!props.roles) return [];

    if (Array.isArray(props.roles)) {
        return props.roles.filter((role) => role != null && role !== "");
    }

    return Object.values(props.roles).filter(
        (role) => role != null && role !== ""
    );
});

const handleAvatarChange = (file) => {
    if (file && file.raw) {
        avatarPreview.value = URL.createObjectURL(file.raw);
        form.avatar = file.raw;
    }
};

const isSuperAdmin = (user) => {
    return user.email === "admin@admin.com" || user.role === "superadmin";
};

const update = () => {
    show_loader.value = true;
    form.put(route("vendor.users.update", { user: props.user.id }), {
        onSuccess: () => {
            show_loader.value = false;
        },
        onError: () => {
            show_loader.value = false;
        },
        preserveScroll: true,
    });
};
</script>
