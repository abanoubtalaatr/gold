<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps<{
    email: string
}>()

const form = useForm({
    email: props.email,
    otp: '',
})

const submit = () => {
    form.post(route('vendor.verify.submit'), {
        onSuccess: () => {
            // Will redirect to dashboard after successful verification
        },
    })
}

const resendOtp = () => {
    form.post(route('vendor.verify.resend'), {
        preserveScroll: true,
        onSuccess: () => {
            // Optionally show a success message (e.g., with a toast)
        },
    })
}
</script>

<template>
    <Head title="Verify Email" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="px-8 py-10 sm:px-10">
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900">{{ $t('Verify Your Email') }}</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ $t("We've sent a 6-digit OTP to") }} <span class="font-medium">{{ email }}</span>
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="otp" :value="$t('OTP Code')" class="text-gray-700" />
                        <TextInput
                            id="otp"
                            type="text"
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            v-model="form.otp"
                            required
                            autofocus
                            maxlength="6"
                            placeholder="Enter 6-digit code"
                        />
                        <InputError class="mt-2" :message="form.errors.otp" />
                    </div>

                    <div class="flex flex-col items-center space-y-3">
                        <PrimaryButton
                            class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 font-semibold"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? $t('Verifying...') : $t('Verify') }}
                        </PrimaryButton>

                        <button
                            type="button"
                            @click="resendOtp"
                            class="w-auto rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 py-1.5 px-4 text-sm font-medium transition-colors"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? $t('Sending...') : $t('Resend OTP') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>