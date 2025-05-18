<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps<{
  email: string
  token: string
}>()

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('vendor.password.store'), {
    onSuccess: () => {
      window.location.href = route('vendor.login')
    },
    onFinish: () => {
      form.reset('password', 'password_confirmation')
    },
  })
}
</script>

<template>
  <Head title="Reset Password" />

  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
      <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Reset Password</h2>
        <p class="text-sm text-gray-600 mt-2">Create a new password for your account</p>
      </div>

      <form @submit.prevent="submit">
        <div class="mt-4">
          <InputLabel for="password" value="New Password" />
          <div class="relative">
            <TextInput
              id="password"
              :type="showPassword ? 'text' : 'password'"
              class="mt-1 block w-full pr-10"
              v-model="form.password"
              required
              autocomplete="new-password"
            />
            <button
              type="button"
              class="absolute inset-y-0 right-0 pr-3 flex items-center"
              @click="showPassword = !showPassword"
            >
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400" />
            </button>
          </div>
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="mt-4">
          <InputLabel for="password_confirmation" value="Confirm Password" />
          <div class="relative">
            <TextInput
              id="password_confirmation"
              :type="showConfirmPassword ? 'text' : 'password'"
              class="mt-1 block w-full pr-10"
              v-model="form.password_confirmation"
              required
              autocomplete="new-password"
            />
            <button
              type="button"
              class="absolute inset-y-0 right-0 pr-3 flex items-center"
              @click="showConfirmPassword = !showConfirmPassword"
            >
              <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400" />
            </button>
          </div>
          <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>

        <div class="mt-6">
          <PrimaryButton
            class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Reset Password
          </PrimaryButton>
        </div>
      </form>
    </div>
  </div>
</template> 