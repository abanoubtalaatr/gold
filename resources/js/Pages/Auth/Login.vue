<template>
  <div class="login-container">
    <!-- Left Side - Login Form -->
    <div class="login-form-container">
      <div class="form-wrapper">
        <SwitchLang class="lang-switch"/>
        
        <h1 class="login-title">{{ $t('login_to_your_account') }}</h1>
        <div class="register-link">
            <p class="register-text">
                {{ $t('dont_have_account') }}
                <Link 
                    :href="route('vendor.register')" 
                    class="register-link-text"
                    rel="noopener noreferrer"
                    target="_blank"
                >
                    {{ $t('create_account') }}
                </Link>
            </p>
        </div>

        <form @submit.prevent="submit" class="login-form" novalidate>
          <!-- Email Field -->
          <div class="form-group">
            <label for="email" class="form-label">{{ $t('email') }}</label>
            <div class="input-wrapper">
              <TextInput
                id="email"
                type="email"
                v-model="form.email"
                required
                autocomplete="username"
                :placeholder="$t('enter_email')"
                class="input-field"
              />
              <i class="bi bi-envelope input-icon"></i>
            </div>
            <InputError :message="form.errors.email" />
          </div>

                   <!-- Password Field -->
                   <!-- <div class="form-group">
            <label for="password" class="form-label">{{ $t('password') }}</label>
            <div class="input-wrapper">
              <ShowHidePassword
                id="password"
                v-model="form.password"
                :placeholder="$t('enter_password')"
                class="input-field"
                required
                autocomplete="current-password"
              />
              <i class="bi bi-lock input-icon"></i>
            </div>
            <InputError :message="form.errors.password" />
          </div> -->


                    <!-- Password Field -->
                    <div class="form-group">
            <label for="password" class="form-label">{{ $t('password') }}</label>
            <div class="input-wrapper">
              <input
                :type="showPassword ? 'text' : 'password'"
                id="password"
                v-model="form.password"
                :placeholder="$t('enter_password')"
                class="input-field"
                required
                autocomplete="current-password"
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

          
          <!-- Remember Me & Forgot Password -->
          <div class="form-actions">
            <div class="remember-me">
              <Checkbox name="remember" v-model:checked="form.remember" />
              <label for="remember">{{ $t('remember_me') }}</label>
            </div>
            <Link
              v-if="canResetPassword"
              :href="route('password.request')"
              class="forgot-link"
            >
              {{ $t('forgot_password') }}
            </Link>
          </div>

          <button type="submit" class="login-button text-center">
            {{ $t('login') }}
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

<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import SwitchLang from '@/Components/SwitchLang.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ShowHidePassword from "@/Components/ShowHidePassword.vue";
import { ref } from 'vue';

// import { computed } from 'vue'
// import { usePage } from '@inertiajs/vue3'

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    locale: {
        type: String
    }
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
// const page = usePage()
// const currentLanguage = computed(() => page.props.locale || 'en'); // 'en' كقيمة افتراضية

// const changeLanguage = (event) => {
//     const selectedLanguage = event.target.value;
//     const currentUrl = window.location.origin; // Get the current app URL
//     const newUrl = `${currentUrl}/lang/change?lang=${selectedLanguage}`; // Construct the new URL
//     window.location.href = newUrl; // Redirect to the new URL
// };
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
  margin-bottom: 2rem;
}

.social-login {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.social-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  height: 48px;
  border: 1px solid var(--border-color);
  border-radius: var(--button-radius);
  background: var(--bg-primary);
  color: var(--text-primary);
  font-weight: 500;
  transition: var(--transition);
}

.social-btn:hover {
  background: var(--bg-secondary);
}

.social-btn img {
  width: 24px;
  height: 24px;
}

.divider {
  text-align: center;
  margin: 1.5rem 0;
  position: relative;
}

.divider::before,
.divider::after {
  content: '';
  position: absolute;
  top: 50%;
  width: calc(50% - 30px);
  height: 1px;
  background: var(--border-color);
}

.divider::before {
  left: 0;
}

.divider::after {
  right: 0;
}

.divider span {
  background: var(--bg-primary);
  padding: 0 1rem;
  color: var(--text-secondary);
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

.input-wrapper i {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-secondary);
}

[dir="rtl"] .input-wrapper i {
  right: auto;
  left: 1rem;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-secondary);
}

.forgot-link {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 500;
  transition: var(--transition);
}

.forgot-link:hover {
  opacity: 0.8;
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

.login-button:hover {
  opacity: 0.9;
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
  color: var(--primary-color);
}

.image-content .logo {
  width: 200px; 
  /* margin-bottom: 2rem; */
}

.image-content h2 {
  font-size: 24px;
  font-weight: 600;
  margin-bottom: 1rem;
}

.image-content p {
  font-size: 18px;
  opacity: 0.8;
}

@media (max-width: 1024px) {
  .login-image {
    display: none;
  }
  
  .login-form-container {
    padding: 1.5rem;
  }
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
</style>
