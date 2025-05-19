<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { ref } from 'vue'

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

// Form setup
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

// Image preview state
const previewUrl = ref<string | null>(null)
const fileName = ref<string | null>(null)
const isImage = ref<boolean>(false)

// Handle file change and generate preview
const onFileChange = (e: Event, field: string) => {
    const target = e.target as HTMLInputElement
    if (target.files && target.files[0]) {
        const file = target.files[0]
        form[field] = file
        fileName.value = file.name

        if (file.type.startsWith('image/')) {
            isImage.value = true
            const reader = new FileReader()
            reader.onload = (event) => {
                previewUrl.value = event.target?.result as string
            }
            reader.readAsDataURL(file)
        } else {
            isImage.value = false
            previewUrl.value = null // No preview for non-image files (e.g., PDF)
        }
    } else {
        form[field] = null
        previewUrl.value = null
        fileName.value = null
        isImage.value = false
    }
}

// Remove selected file
const removeFile = () => {
    form.commercial_registration_image = null
    previewUrl.value = null
    fileName.value = null
    isImage.value = false
    // Reset file input
    const input = document.getElementById('commercial_registration_image') as HTMLInputElement
    if (input) input.value = ''
}

// Submit form
const submit = () => {
    form.post(route('vendor.register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: () => {
            // OTP verification will be handled next
        },
    })
}
</script>

<template>
    <Head title="Vendor Registration" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-3xl bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="px-8 py-10 sm:px-10">
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900">{{ $t('Vendor Registration') }}</h2>
                    <p class="mt-2 text-sm text-gray-600">{{ $t('Fill in the details to register as a vendor') }}</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800">{{ $t('Basic Information') }}</h3>
                        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="name" :value="$t('Full Name')" class="text-gray-700" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="email" :value="$t('Email')" class="text-gray-700" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.email"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel for="mobile" :value="$t('Mobile Number')" class="text-gray-700" />
                                <TextInput
                                    id="mobile"
                                    type="tel"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.mobile"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.mobile" />
                            </div>

                            <div>
                                <InputLabel for="password" :value="$t('Password')" class="text-gray-700" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.password"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div>
                                <InputLabel for="password_confirmation" :value="$t('Confirm Password')" class="text-gray-700" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.password_confirmation"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>
                        </div>
                    </div>

                    <!-- Store Information -->
                    <div class="pt-6">
                        <h3 class="text-xl font-semibold text-gray-800">{{ $t('Store Information') }}</h3>
                        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <InputLabel for="store_name_en" :value="$t('Store Name (English)')" class="text-gray-700" />
                                <TextInput
                                    id="store_name_en"
                                    type="text"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.store_name_en"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.store_name_en" />
                            </div>

                            <div>
                                <InputLabel for="store_name_ar" :value="$t('Store Name (Arabic)')" class="text-gray-700" />
                                <TextInput
                                    id="store_name_ar"
                                    type="text"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.store_name_ar"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.store_name_ar" />
                            </div>

                            <div class="sm:col-span-2">
                                <InputLabel for="commercial_registration_number" :value="$t('Commercial Registration Number')" class="text-gray-700" />
                                <TextInput
                                    id="commercial_registration_number"
                                    type="text"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    v-model="form.commercial_registration_number"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.commercial_registration_number" />
                            </div>

                            <div class="sm:col-span-2">
                                <InputLabel for="commercial_registration_image" :value="$t('Commercial Registration Image')" class="text-gray-700" />
                                <input
                                    id="commercial_registration_image"
                                    type="file"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    @change="(e) => onFileChange(e, 'commercial_registration_image')"
                                    accept="image/*,.pdf"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.commercial_registration_image" />

                                <!-- Image Preview -->
                                <div v-if="fileName" class="mt-4">
                                    <div class="flex items-center space-x-4">
                                        <div v-if="isImage && previewUrl" class="flex-shrink-0">
                                            <img :src="previewUrl" alt="Image Preview" class="w-32 h-32 object-cover rounded-lg border border-gray-200" />
                                        </div>
                                        <div v-else class="flex-shrink-0">
                                            <div class="w-32 h-32 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 text-gray-500">
                                                {{ $t('PDF Selected') }}
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600">{{ $t('Selected File') }}: {{ fileName }}</p>
                                            <button
                                                type="button"
                                                class="mt-2 rounded-md bg-red-100 hover:bg-red-200 text-red-700 py-1.5 px-4 text-sm font-medium transition-colors"
                                                @click="removeFile"
                                            >
                                                {{ $t('Remove File') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <PrimaryButton
                            class="w-full justify-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white py-3 text-lg font-semibold"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? $t('Registering...') : $t('Register') }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>