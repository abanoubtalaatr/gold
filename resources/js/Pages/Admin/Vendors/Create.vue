<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Add New Vendor') }}
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Personal Information -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-medium">{{ $t('Personal Information') }}</h3>

                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">
                                            {{ $t('Full Name') }}
                                        </label>
                                        <input id="name" v-model="form.name" type="text"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required />
                                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.name }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">
                                            {{ $t('Email') }}
                                        </label>
                                        <input id="email" v-model="form.email" type="email"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required />
                                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.email }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="mobile" class="block text-sm font-medium text-gray-700">
                                            {{ $t('Mobile Number') }}
                                        </label>
                                        <div class="flex mt-1 rounded-md shadow-sm">
                                            <select v-model="form.dialling_code"
                                                class="flex-shrink-0 w-24 px-3 py-2 text-gray-500 bg-gray-100 border-gray-300 rounded-l-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="+966">+966 (SA)</option>
                                                <option value="+971">+971 (UAE)</option>
                                                <option value="+20">+20 (EG)</option>
                                            </select>
                                            <input id="mobile" v-model="form.mobile" type="tel"
                                                class="block w-full min-w-0 border-gray-300 rounded-none rounded-r-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                required />
                                        </div>
                                        <p v-if="form.errors.mobile" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.mobile }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">
                                            {{ $t('Password') }}
                                        </label>
                                        <input id="password" v-model="form.password" type="password"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required />
                                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.password }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="password_confirmation"
                                            class="block text-sm font-medium text-gray-700">
                                            {{ $t('Confirm Password') }}
                                        </label>
                                        <input id="password_confirmation" v-model="form.password_confirmation"
                                            type="password"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required />
                                    </div>
                                </div>

                                <!-- Store Information -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-medium">{{ $t('Store Information') }}</h3>

                                    <div>
                                        <label for="store_name_en" class="block text-sm font-medium text-gray-700">
                                            {{ $t('Store Name (English)') }}
                                        </label>
                                        <input id="store_name_en" v-model="form.store_name_en" type="text"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required />
                                        <p v-if="form.errors.store_name_en" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.store_name_en }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="store_name_ar" class="block text-sm font-medium text-gray-700">
                                            {{ $t('Store Name (Arabic)') }}
                                        </label>
                                        <input id="store_name_ar" v-model="form.store_name_ar" type="text"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required />
                                        <p v-if="form.errors.store_name_ar" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.store_name_ar }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="commercial_registration_number"
                                            class="block text-sm font-medium text-gray-700">
                                            {{ $t('Commercial Registration Number') }}
                                        </label>
                                        <input id="commercial_registration_number"
                                            v-model="form.commercial_registration_number" type="text"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required />
                                        <p v-if="form.errors.commercial_registration_number"
                                            class="mt-1 text-sm text-red-600">
                                            {{ form.errors.commercial_registration_number }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="commercial_registration_image"
                                            class="block text-sm font-medium text-gray-700">
                                            {{ $t('Commercial Registration Image') }}
                                        </label>
                                        <input id="commercial_registration_image" type="file"
                                            @change="handleFileUpload('commercial_registration_image', $event)"
                                            accept="image/*"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required />
                                        <p v-if="form.errors.commercial_registration_image"
                                            class="mt-1 text-sm text-red-600">
                                            {{ form.errors.commercial_registration_image }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="logo" class="block text-sm font-medium text-gray-700">
                                            {{ $t('Store Logo') }}
                                        </label>
                                        <input id="logo" type="file" @change="handleFileUpload('logo', $event)"
                                            accept="image/*"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required />
                                        <p v-if="form.errors.logo" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.logo }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="iban" class="block text-sm font-medium text-gray-700">
                                            {{ $t('IBAN') }}
                                        </label>
                                        <input id="iban" v-model="form.iban" type="text"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required />
                                        <p v-if="form.errors.iban" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.iban }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="city_id" class="block text-sm font-medium text-gray-700">
                                            {{ $t('City') }}
                                        </label>
                                        <select id="city_id" v-model="form.city_id"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required>
                                            <option value="">{{ $t('Select City') }}</option>
                                            <option v-for="city in cities" :key="city.id" :value="city.id">
                                                {{ city.name }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.city_id" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.city_id }}
                                        </p>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="accept_terms" v-model="form.accept_terms" type="checkbox"
                                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                required />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="accept_terms" class="font-medium text-gray-700">
                                                {{ $t('I accept the terms and conditions') }}
                                            </label>
                                            <p v-if="form.errors.accept_terms" class="text-red-600">
                                                {{ form.errors.accept_terms }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <Link :href="route('vendors.index')"
                                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                {{ $t('Cancel') }}
                                </Link>
                                <button type="submit"
                                    class="px-4 py-2 ml-3 text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                                    :disabled="form.processing">
                                    {{ $t('Create Vendor') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({
    cities: Array,
});

const form = useForm({
    name: '',
    email: '',
    dialling_code: '+966',
    mobile: '',
    password: '',
    password_confirmation: '',
    store_name_en: '',
    store_name_ar: '',
    commercial_registration_number: '',
    commercial_registration_image: null,
    logo: null,
    iban: '',
    city_id: '',
    accept_terms: false,
});

const handleFileUpload = (field, event) => {
    form[field] = event.target.files[0];
};

const submit = () => {
    form.post(route('vendors.store'), {
        forceFormData: true,
        onSuccess: () => {
            Swal.fire(
                t('Success'),
                t('Vendor created successfully'),
                'success'
            );
        },
        onError: () => {
            Swal.fire(
                t('Error'),
                t('Failed to create vendor'),
                'error'
            );
        }
    });
};
</script>