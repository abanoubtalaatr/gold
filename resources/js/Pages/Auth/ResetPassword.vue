<template>
    <div class="login-container">
        <!-- Left Side - Form -->
        <div class="login-form-container">
            <div class="form-wrapper">
                <SwitchLang class="lang-switch" />
                
                <h1 class="login-title">{{ $t('reset_password') }}</h1>

                <!-- إضافة رسالة الخطأ العامة -->
                <div v-if="form.errors.token" class="alert alert-danger">
                    {{ form.errors.token }}
                </div>

                <form @submit.prevent="submit" class="login-form">
                    <div class="form-group">
                        <label for="email" class="form-label">{{ $t('email') }}</label>
                        <div class="input-wrapper">
                            <input
                                id="email"
                                type="email"
                                class="input-field"
                                v-model="form.email"
                                required
                                readonly
                                :placeholder="$t('enter_email')"
                            />
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">{{ $t('password') }}</label>
                        <div class="input-wrapper">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                id="password"
                                class="input-field"
                                v-model="form.password"
                                required
                                :placeholder="$t('enter_password')"
                                autocomplete="new-password"
                            />
                            <i 
                                class="input-icon bi" 
                                :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"
                                @click="showPassword = !showPassword"
                                style="cursor: pointer;"
                            ></i>
                        </div>
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">{{ $t('confirm_password') }}</label>
                        <div class="input-wrapper">
                            <input
                                :type="showConfirmPassword ? 'text' : 'password'"
                                id="password_confirmation"
                                class="input-field"
                                v-model="form.password_confirmation"
                                required
                                :placeholder="$t('confirm_password')"
                            />
                            <i 
                                class="input-icon bi" 
                                :class="showConfirmPassword ? 'bi-eye-slash' : 'bi-eye'"
                                @click="showConfirmPassword = !showConfirmPassword"
                                style="cursor: pointer;"
                            ></i>
                        </div>
                        <InputError :message="form.errors.password_confirmation" />
                    </div>

                    <button 
                        type="submit" 
                        class="login-button" 
                        :disabled="form.processing"
                    >
                        {{ form.processing ? $t('processing') : $t('reset_password') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Side - Image -->
        <div class="login-image">
            <div class="image-content">
                <img src="/dashboard-assets/img/logos/logo.svg" alt="Logo" class="logo" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import SwitchLang from '@/Components/SwitchLang.vue';

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

// تأكد من أن البيانات تصل بشكل صحيح
console.log('Email:', props.email);
console.log('Token:', props.token);

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onSuccess: () => {
            // إعادة التوجيه إلى صفحة تسجيل الدخول عند النجاح
            window.location.href = route('login');
        },
        onError: (errors) => {
            console.error('Reset Password Errors:', errors);
        },
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

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
