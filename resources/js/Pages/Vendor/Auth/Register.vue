<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

interface FormData {
    name: string
    email: string
    password: string
    password_confirmation: string
    mobile: string
    store_name_en: string
    store_name_ar: string
    commercial_registration_number: string
    commercial_registration_image: File | null
}

const form = useForm<FormData>({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    mobile: '',
    store_name_en: '',
    store_name_ar: '',
    commercial_registration_number: '',
    commercial_registration_image: null,
})

const onFileChange = (e: Event, field: string) => {
    const target = e.target as HTMLInputElement
    if (target.files && target.files[0]) {
        form[field] = target.files[0]
    }
}

const submit = () => {
    form.post(route('vendor.register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: () => {
            // OTP verification will be handled next
        }
    })
}
</script>

<template>

    <Head title="Vendor Registration" />

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-2xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-gray-800">{{ $t('Vendor Registration') }}</h2>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Basic Information') }}</h3>
                </div>

                <div class="w-full">
                    <InputLabel for="name" :value="$t('Full Name')" />
                    <TextInput id="name" type="text" class="w-full" v-model="form.name" required autofocus />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="w-full">
                    <InputLabel for="email" :value="$t('Email')" />
                    <TextInput id="email" type="email" class="w-full" v-model="form.email" required />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="w-full">
                    <InputLabel for="mobile" :value="$t('Mobile Number')" />
                    <TextInput id="mobile" type="tel" class="w-full" v-model="form.mobile" required />
                    <InputError class="mt-2" :message="form.errors.mobile" />
                </div>

                 <!-- Password -->
               

                <div class="w-full">
                    <InputLabel for="password" :value="$t('Password')" />
                    <TextInput id="password" type="password" class="w-full" v-model="form.password" required />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="w-full">
                    <InputLabel for="password_confirmation" :value="$t('Confirm Password')" />
                    <TextInput id="password_confirmation" 
                              type="password" 
                              class="w-full" 
                              v-model="form.password_confirmation" 
                              required />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
                <!-- Store Information -->
                <div class="pt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Store Information') }}</h3>
                </div>

                <div class="w-full">
                    <InputLabel for="store_name_en" :value="$t('Store Name (English)')" />
                    <TextInput id="store_name_en" type="text" class="w-full" v-model="form.store_name_en" required />
                    <InputError class="mt-2" :message="form.errors.store_name_en" />
                </div>

                <div class="w-full">
                    <InputLabel for="store_name_ar" :value="$t('Store Name (Arabic)')" />
                    <TextInput id="store_name_ar" type="text" class="w-full" v-model="form.store_name_ar" required />
                    <InputError class="mt-2" :message="form.errors.store_name_ar" />
                </div>

                <div class="w-full">
                    <InputLabel for="commercial_registration_number" :value="$t('Commercial Registration Number')" />
                    <TextInput id="commercial_registration_number" type="text" class="w-full"
                        v-model="form.commercial_registration_number" required />
                    <InputError class="mt-2" :message="form.errors.commercial_registration_number" />
                </div>

                <div class="w-full">
                    <InputLabel for="commercial_registration_image" :value="$t('Commercial Registration Image')" />
                    <input id="commercial_registration_image" type="file"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        @change="(e) => onFileChange(e, 'commercial_registration_image')" accept="image/*,.pdf"
                        required />
                    <InputError class="mt-2" :message="form.errors.commercial_registration_image" />
                </div>



                <div class="pt-4 w-full">
                    <PrimaryButton class="w-full justify-center" :disabled="form.processing">
                        {{ $t('Register') }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>