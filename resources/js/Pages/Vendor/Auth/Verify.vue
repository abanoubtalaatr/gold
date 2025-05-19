<!-- resources/js/Pages/Vendor/Auth/Verify.vue -->
<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps < {
    email: string
} > ()

const form = useForm({
    email: props.email,
    otp: '',
})

const submit = () => {
    form.post(route('vendor.verify.submit'), {
        onSuccess: () => {
            // Will redirect to dashboard after successful verification
        }
    })
}

const resendOtp = () => {
    form.post(route('vendor.verify.resend'), {
        preserveScroll: true,
        onSuccess: () => {
            // Show success message
        }
    })
}
</script>

<template>

    <Head title="Verify Email" />

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-gray-800">Verify Your Email</h2>
                <p class="text-sm text-gray-600 mt-2">
                    We've sent a 6-digit OTP to {{ email }}
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel for="otp" value="OTP Code" />
                    <TextInput id="otp" type="text" v-model="form.otp" required autofocus maxlength="6"
                        placeholder="Enter 6-digit code" />
                    <InputError :message="form.errors.otp" />
                </div>

                <div class="flex items-center justify-between">
                    <PrimaryButton :disabled="form.processing">
                        Verify
                    </PrimaryButton>

                    <button type="button" @click="resendOtp" class="text-sm text-blue-600 hover:text-blue-800"
                        :disabled="form.processing">
                        Resend OTP
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>