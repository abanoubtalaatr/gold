<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

defineProps<{
  status?: string
}>()

const form = useForm({
  email: '',
})

const submit = () => {
  form.post(route('vendor.password.email'))
}
</script>

<template>
  <Head title="Forgot Password" />

  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
      <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Forgot Password</h2>
        <p class="text-sm text-gray-600 mt-2">Enter your email to reset your password</p>
      </div>

      <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
      </div>

      <form @submit.prevent="submit">
        <div>
          <InputLabel for="email" value="Email" />
          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
            required
            autofocus
            autocomplete="username"
          />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="flex items-center justify-end mt-4">
          <PrimaryButton
            class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Send Reset Link
          </PrimaryButton>
        </div>
      </form>
    </div>
  </div>
</template> 