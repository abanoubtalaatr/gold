<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import Checkbox from '@/Components/Checkbox.vue'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('vendor.login'), {
    onFinish: () => {
      form.reset('password')
    },
  })
}
</script>

<template>
  <Head title="Vendor Login" />

  <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
      <div class="mb-8 text-center">
        <h2 class="text-2xl font-bold text-gray-800">Vendor Portal</h2>
        <p class="text-sm text-gray-600 mt-2">Welcome back! Please sign in to your account.</p>
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

        <div class="mt-4">
          <InputLabel for="password" value="Password" />
          <TextInput
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="form.password"
            required
            autocomplete="current-password"
          />
          <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="flex items-center justify-between mt-4">
          <label class="flex items-center">
            <Checkbox name="remember" v-model:checked="form.remember" />
            <span class="ml-2 text-sm text-gray-600">Remember me</span>
          </label>
          <Link
            :href="route('vendor.password.request')"
            class="text-sm text-blue-600 hover:text-blue-800"
          >
            Forgot your password?
          </Link>
        </div>

        <div class="mt-6">
          <PrimaryButton
            class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
          >
            Sign in
          </PrimaryButton>
        </div>
      </form>
    </div>
  </div>
</template> 