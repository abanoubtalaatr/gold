<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Edit Vendor') }}
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
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ $t('Commercial Registration Image') }}
                                        </label>
                                        <div class="flex items-center mt-1">
                                            <input type="file"
                                                @change="handleFileUpload('commercial_registration_image', $event)"
                                                accept="image/*"
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                        </div>
                                        <p v-if="form.errors.commercial_registration_image"
                                            class="mt-1 text-sm text-red-600">
                                            {{ form.errors.commercial_registration_image }}
                                        </p>
                                        <a v-if="vendor.media?.find(m => m.collection_name === 'commercial_registration')"
                                            :href="vendor.media.find(m => m.collection_name === 'commercial_registration').original_url"
                                            target="_blank"
                                            class="inline-block mt-2 text-sm text-indigo-600 hover:text-indigo-900">
                                            {{ $t('View Current Document') }}
                                        </a>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ $t('Store Logo') }}
                                        </label>
                                        <div class="flex items-center mt-1">
                                            <input type="file" @change="handleFileUpload('logo', $event)"
                                                accept="image/*"
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                        </div>
                                        <p v-if="form.errors.logo" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.logo }}
                                        </p>
                                        <img v-if="vendor.media?.find(m => m.collection_name === 'logo')"
                                            :src="vendor.media.find(m => m.collection_name === 'logo').original_url"
                                            class="mt-2 h-16 w-16 rounded-full object-cover" />
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
                                </div>
                            </div>

                            <div class="flex justify-end mt-6">
                                <Link :href="route('vendors.show', vendor.id)"
                                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                {{ $t('Cancel') }}
                                </Link>
                                <button type="submit"
                                    class="px-4 py-2 ml-3 text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                                    :disabled="form.processing">
                                    {{ $t('Update Vendor') }}
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
    vendor: Object,
    cities: Array,
});

const form = useForm({
    name: props.vendor.name,
    email: props.vendor.email,
    dialling_code: props.vendor.dialling_code,
    mobile: props.vendor.mobile,
    store_name_en: props.vendor.store_name_en,
    store_name_ar: props.vendor.store_name_ar,
    commercial_registration_number: props.vendor.commercial_registration_number,
    commercial_registration_image: null,
    logo: null,
    iban: props.vendor.iban,
    city_id: props.vendor.city_id,
});

const handleFileUpload = (field, event) => {
    form[field] = event.target.files[0];
};

const submit = () => {
    form.put(route('vendors.update', props.vendor.id), {
        forceFormData: true,
        onSuccess: () => {
            Swal.fire(
                t('Success'),
                t('Vendor updated successfully'),
                'success'
            );
        },
        onError: () => {
            Swal.fire(
                t('Error'),
                t('Failed to update vendor'),
                'error'
            );
        }
    });
};
</script>