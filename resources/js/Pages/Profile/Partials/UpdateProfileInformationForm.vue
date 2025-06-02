<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { ref } from 'vue';
defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const avatarPreview = ref(null);

const form = useForm({
    name: user.name,
    email: user.email,
    mobile: user.mobile,
    avatar: null,
});

console.log(user);
const handleAvatarChange = (file) => {
    if (file && file.raw) {
        avatarPreview.value = URL.createObjectURL(file.raw);
        form.avatar = file.raw;
    }
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                {{ $t("my_profile") }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{
                    $t(
                        "Update_your_accounts_profile_information_and_email_address"
                    )
                }}
            </p>
        </header>

        <form @submit.prevent="form.post(route('profile.update'))" class="mt-6 space-y-6">
            <div>
                <!-- Avatar Upload -->
                <el-form-item class="col-md-12">
                    <div class="avatar-upload">
                        <img :src="avatarPreview || user.avatar || '/dashboard-assets/img/default-avatar.png'"
                            class="avatar-preview" />
                        <el-upload class="upload-btn" accept="image/*" :auto-upload="false" :show-file-list="false"
                            @change="handleAvatarChange">
                            <el-button type="primary">
                                <i class="bi bi-camera"></i>
                                {{ $t('change_avatar') }}
                            </el-button>
                        </el-upload>
                    </div>
                </el-form-item>
                <InputLabel for="name" :value="$t('name')" />

                <TextInput id="name" type="text" class="mt-1 block w-full form-control" v-model="form.name" required
                    autofocus autocomplete="name" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" :value="$t('email')" />

                <TextInput id="email" type="email" class="mt-1 block w-full form-control" v-model="form.email" required
                    autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="mobile" :value="$t('mobile')" />

                <TextInput id="mobile" type="text" class="mt-1 block w-full form-control" v-model="form.mobile"
                    autocomplete="tel" />

                <InputError class="mt-2" :message="form.errors.mobile" />
            </div>

            <div>
                <InputLabel for="role" :value="$t('role')" />

                <TextInput id="role" type="text" class="mt-1 block w-full form-control" :value="user.roles[0].name"
                    disabled autocomplete="role" />
            </div>
            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="form-control underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Click here to re-send the verification email.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'" class="mt-2 font-medium text-sm text-green-600">
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">{{
                    $t("save")
                    }}</PrimaryButton>

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                        {{ $t("data_updated_successfully") }}
                    </p>
                </Transition>
            </div>
        </form>
    </section>
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
