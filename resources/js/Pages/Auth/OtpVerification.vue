<script setup>
import { ElMessage } from "element-plus";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import SwitchLang from "@/Components/SwitchLang.vue";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

// إعداد النموذج
const props = defineProps({
    email: { type: String, default: "" }, // استلام البريد الإلكتروني من الخادم
});

const form = useForm({
    otp: Array(6).fill(""), // كود مكون من 6 أرقام
    email: props.email, // البريد الإلكتروني المرسل
});

const counter = ref(30); // عداد لإعادة الإرسال

const startCounter = () => {
    counter.value = 30;
    const interval = setInterval(() => {
        if (counter.value > 0) {
            counter.value -= 1;
        } else {
            clearInterval(interval);
        }
    }, 1000);
};

const submitOtp = () => {
    const otp = form.otp.join(""); // دمج الكود المكون من 6 أرقام
    form.post("/auth/verify-otp", {
        onSuccess: () => {
            ElMessage.success(t("otp_verified"));
        },
        onError: (errors) => {
            ElMessage.error(
                errors.otp ? errors.otp[0] : t("verification_failed")
            );
        },
    });
};

// إعادة إرسال OTP
const resendOtp = () => {
    form.post("/auth/resend-otp", {
        onSuccess: () => {
            ElMessage.success(t("otp_resent"));
            startCounter(); // بدء العداد
        },
        onError: (errors) => {
            ElMessage.error(
                errors.email ? errors.email[0] : t("otp_resend_failed")
            );
        },
    });
};

startCounter(); // بدء العداد فور تحميل الصفحة

// تحديث الكود عند إدخال كل رقم
const updateOtp = (index, value) => {
    // تنظيف القيمة للتأكد من أنها أرقام إنجليزية فقط
    const cleanValue = value.replace(/[^0-9]/g, '').slice(0, 1);
    form.otp[index] = cleanValue;

    // التنقل لليمين فقط إذا كان هناك قيمة وليس آخر مربع
    if (cleanValue && index < 5) {
        const nextInput = document.querySelector(`#otp-input-${index + 1}`);
        if (nextInput) nextInput.focus();
    }
};

// التعامل مع مفتاح Backspace
const handleKeyDown = (index, event) => {
    if (event.key === 'Backspace') {
        if (form.otp[index]) {
            form.otp[index] = '';
        } else if (index > 0) {
            const prevInput = document.querySelector(`#otp-input-${index - 1}`);
            if (prevInput) prevInput.focus();
        }
    }
};
</script>

<template>
    <div class="login-container">
        <!-- Left Side - Form -->
        <div class="login-form-container">
            <div class="form-wrapper">
                <SwitchLang class="lang-switch" />

                <h1 class="login-title">{{ $t('verify_otp') }}</h1>

                <div class="mb-4 text-sm text-secondary">
                    {{ $t('otp_verification_text') }}
                </div>

                <form @submit.prevent="submitOtp" class="login-form" novalidate>
                    <div class="form-group">
                        <label class="form-label">{{ $t('enter_otp') }}</label>
                        <div class="otp-input-container">
                            <input
                                v-for="(_, index) in form.otp"
                                :key="index"
                                :id="'otp-input-' + index"
                                maxlength="1"
                                class="otp-input"
                                type="text"
                                v-model="form.otp[index]"
                                @input="updateOtp(index, $event.target.value)"
                                @keydown="handleKeyDown(index, $event)"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                @paste.prevent="handlePaste"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.otp" />
                    </div>

                    <button type="submit" class="login-button text-center" :disabled="form.processing">
                        {{ $t('verify_otp') }}
                    </button>

                    <button
                        type="button"
                        class="resend-button "
                        :disabled="counter > 0"
                        @click="resendOtp"
                    >
                    <u>{{ counter > 0 ? `${t('resend_otp_in')} ${counter}s` : t('resend_otp') }}</u>
                    </button>
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

.otp-input-container {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin: 1rem 0;
    direction: ltr;
}

.otp-input {
    width: 50px;
    height: 50px;
    text-align: center !important;
    font-size: 1.5rem;
    font-weight: bold;
    border: 1px solid var(--border-color);
    border-radius: var(--input-radius, 8px);
    background: var(--bg-primary);
    color: var(--text-primary);
    transition: var(--transition);
    direction: ltr; /* دائماً من اليسار لليمين */

}

/* Remove arrows for number input */
.otp-input::-webkit-outer-spin-button,
.otp-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.otp-input::placeholder {
    direction: ltr;
}

.otp-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(var(--primary-color-rgb), 0.1);
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

.resend-button {
    height: 48px;
    background: transparent;
    color: var(--primary-color);
    border: none;
    border-radius: var(--button-radius);
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
}

.resend-button:disabled {
    color: var(--text-secondary);
    cursor: not-allowed;
}

.resend-button:hover:not(:disabled) {
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

.text-secondary {
    color: var(--text-secondary);
}

@media (max-width: 1024px) {
    .login-image {
        display: none;
    }

    .login-form-container {
        padding: 1.5rem;
    }

    .otp-input-container {
        gap: 8px;
    }

    .otp-input {
        width: 45px;
        height: 45px;
        font-size: 1.25rem;
    }
}

@media (max-width: 400px) {
    .otp-input {
        width: 40px;
        height: 40px;
        font-size: 1.125rem;
    }
}
</style>
