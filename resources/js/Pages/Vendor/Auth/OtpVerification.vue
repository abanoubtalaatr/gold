<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps<{
  email: string
  status?: string
}>()

const form = useForm({
  email: props.email,
  otp: ['', '', '', '', '', ''] as string[],
})

const otpRefs = ref<HTMLInputElement[]>([])

const focusNext = (index: number) => {
  if (index < 5 && form.otp[index]) {
    otpRefs.value[index + 1]?.focus()
  }
}

const focusPrev = (index: number, event: KeyboardEvent) => {
  if (event.key === 'Backspace' && !form.otp[index] && index > 0) {
    otpRefs.value[index - 1]?.focus()
  }
}

const submit = () => {
  form.post(route('vendor.password.verify-otp'))
}

const resendOtp = () => {
  useForm({ email: props.email }).post(route('vendor.password.resend-otp'))
}
</script>

<template>
  <Head title="OTP Verification" />

  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
      <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800">OTP Verification</h2>
        <p class="text-sm text-gray-600 mt-2">
          Enter the verification code sent to your email
        </p>
      </div>

      <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
      </div>

      <form @submit.prevent="submit">
        <div class="flex justify-center gap-2">
          <template v-for="(digit, index) in form.otp" :key="index">
            <input
              type="text"
              maxlength="1"
              class="w-12 h-12 text-center text-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
              v-model="form.otp[index]"
              @input="focusNext(index)"
              @keydown="focusPrev(index, $event)"
              :ref="(el) => { if (el) otpRefs[index] = el as HTMLInputElement }"
            />
          </template>
        </div>

        <InputError class="mt-2" :message="form.errors.otp" />

        <div class="flex flex-col items-center gap-4 mt-6">
          <PrimaryButton
            class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Verify OTP
          </PrimaryButton>

          <button
            type="button"
            class="text-sm text-blue-600 hover:text-blue-800"
            @click="resendOtp"
          >
            Resend OTP
          </button>
        </div>
      </form>
    </div>
  </div>
</template> 