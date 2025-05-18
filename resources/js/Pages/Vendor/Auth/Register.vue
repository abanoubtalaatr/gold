<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import Checkbox from '@/Components/Checkbox.vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    remember: false,
    avatar: '',
    mobile: '',
})

const submit = () => {
    form.post(route('vendor.register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        },
        onSuccess: () => {
            // Handle successful submission
            console.log('Success!');
        },
        onError: (errors) => {
            // Errors will be automatically bound to form.errors
            console.log('Errors occurred');
        }
    })
}
</script>

<template>

    <Head title="Vendor create account" />

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-8 text-center">
                <h2 class="text-2xl font-bold text-gray-800">Vendor Portal</h2>
                <p class="text-sm text-gray-600 mt-2">Create Account.</p>
            </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="name" value="Name" />
                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                        autocomplete="email" />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="mobile" value="Mobile" />
                    <TextInput id="mobile" type="tel" class="mt-1 block w-full" v-model="form.mobile" required />
                    <InputError class="mt-2" :message="form.errors.mobile" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />
                    <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
                        autocomplete="new-password" />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                        v-model="form.password_confirmation" required autocomplete="new-password" />
                </div>
                <div class="mt-6">
                    <PrimaryButton class="w-full justify-center py-3 bg-blue-600 hover:bg-blue-700"
                        :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create Account
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
