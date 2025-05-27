<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import SwitchLang from '@/Components/SwitchLang.vue';
import { Link } from '@inertiajs/vue3';
defineProps({
    status: {
        type: String,
    },
    locale:{
        type: String
    }
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <div class="login-container">
        <!-- Left Side - Form -->
        <div class="login-form-container">
            <div class="form-wrapper">
                <SwitchLang class="lang-switch" />

                <h1 class="login-title">{{ $t('forgot_password') }}</h1>

                <div class="mb-4 text-sm text-gray-600">
                    {{ $t('forgot_password_text') }}
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="login-form">
                    <div class="form-group">
                        <label for="email" class="form-label">{{ $t('email') }}</label>
                        <div class="input-wrapper">
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                class="input-field"
                                required
                                autofocus
                                :placeholder="$t('email')"
                                autocomplete="username"
                            />
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <button type="submit" class="login-button text-center" :disabled="form.processing">
                        {{ $t('reset_password_link') }}
                    </button>

                    <Link :href="route('login')" class="back-to-login">
                    <u>{{ $t('back_to_login') }}</u>
                </Link>
                </form>
            </div>
        </div>

        <!-- Right Side - Image -->
        <div class="login-image">
            <div class="image-content">
                <img src="/dashboard-assets/img/logo2.png" alt="Logo" class="logo" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.login-container {
    display: flex;
    min-height: 100vh;
    background: var(--bg-primary);
}

.login-form-container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}

.form-wrapper {
    width: 100%;
    max-width: 440px;
    position: relative;
}

.lang-switch {
    position: absolute;
    top: -60px;
    right: 0;
}

[dir="rtl"] .lang-switch {
    right: auto;
    left: 0;
}

.login-title {
    font-size: 28px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    color: var(--text-primary);
    font-weight: 500;
}

.input-wrapper {
    position: relative;
}

.input-field {
    width: 100%;
    height: 48px;
    padding: 0.75rem 2.5rem 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: var(--input-radius, 8px);
    background: var(--bg-primary);
    color: var(--text-primary);
    transition: var(--transition);
}

.input-field:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(var(--primary-color-rgb), 0.1);
}

.input-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
}

[dir="rtl"] .input-field {
    padding: 0.75rem 1rem 0.75rem 2.5rem;
}

[dir="rtl"] .input-icon {
    right: auto;
    left: 1rem;
}

.login-button {
    height: 48px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: var(--button-radius);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.login-button:hover:not(:disabled) {
    opacity: 0.9;
}

.login-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.back-to-login {
    text-align: center;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
}

.back-to-login:hover {
    opacity: 0.8;
}

.login-image {
    flex: 1;
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.image-content {
    text-align: center;
    color: white;
}

.image-content .logo {
    width: 200px;
}

@media (max-width: 1024px) {
    .login-image {
        display: none;
    }

    .login-form-container {
        padding: 1.5rem;
    }
}
</style>
