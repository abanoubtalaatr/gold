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
                        <form @submit.prevent="submit" enctype="multipart/form-data">
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
                                        
                                        <p v-if="form.errors.commercial_registration_image"
                                            class="mt-1 text-sm text-red-600">
                                            {{ form.errors.commercial_registration_image }}
                                        </p>
                                        <div class="flex items-center gap-4 mt-2">
                                            <!-- Show current image if exists -->
                                            <div v-if="vendor.commercial_registration_image && !deletedCommercialRegistration && !commercialRegistrationPreview" class="relative">
                                                <a :href="'/storage/' + vendor.commercial_registration_image"
                                                    target="_blank"
                                                    class="inline-block text-sm text-indigo-600 hover:text-indigo-900">
                                                    {{ $t('View Current Document') }}
                                                </a>
                                                <img :src="'/storage/' + vendor.commercial_registration_image"
                                                    class="h-16 object-cover rounded-md" />
                                                
                                            </div>
                                            <!-- Show preview of new image if selected -->
                                            <div v-if="commercialRegistrationPreview" class="relative">
                                                <img :src="commercialRegistrationPreview"
                                                    class="h-16 object-cover rounded-md" />
                                                <button type="button" @click="deleteFile('commercial_registration_image')"
                                                    class="absolute top-0 right-0 p-1 text-white bg-red-500 rounded-full hover:bg-red-600">
                                                    &times;
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ $t('Store Logo') }}
                                        </label>
                                        
                                        <p v-if="form.errors.logo" class="mt-1 text-sm text-red-600">
                                            {{ form.errors.logo }}
                                        </p>
                                        <div class="flex items-center gap-4 mt-2">
                                            <!-- Show current logo if exists -->
                                            <div v-if="vendor.logo && !deletedLogo && !logoPreview" class="relative">
                                                <img :src="'/storage/' + vendor.logo"
                                                    class="h-16 w-16 rounded-full object-cover" />
                                                
                                            </div>
                                            <!-- Show preview of new logo if selected -->
                                            <div v-if="logoPreview" class="relative">
                                                <img :src="logoPreview"
                                                    class="h-16 w-16 rounded-full object-cover" />
                                                
                                            </div>
                                        </div>
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
import { ref } from 'vue';

const { t } = useI18n();

const props = defineProps({
    vendor: Object,
    cities: Array,
});

const logoPreview = ref(null);
const commercialRegistrationPreview = ref(null);
const deletedCommercialRegistration = ref(false);
const deletedLogo = ref(false);

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
    delete_commercial_registration_image: false,
    delete_logo: false,
});

const handleFileUpload = (field, event) => {
    if (event.target.files.length > 0) {
        const file = event.target.files[0];
        form[field] = file;

        // Reset deletion flags if new file is uploaded
        if (field === 'logo') {
            form.delete_logo = false;
            deletedLogo.value = false;
        } else if (field === 'commercial_registration_image') {
            form.delete_commercial_registration_image = false;
            deletedCommercialRegistration.value = false;
        }

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            if (field === 'logo') {
                logoPreview.value = e.target.result;
            } else if (field === 'commercial_registration_image') {
                commercialRegistrationPreview.value = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
};

const deleteFile = (field) => {
    if (field === 'logo') {
        form.logo = null;
        form.delete_logo = true;
        deletedLogo.value = true;
        logoPreview.value = null;
    } else if (field === 'commercial_registration_image') {
        form.commercial_registration_image = null;
        form.delete_commercial_registration_image = true;
        deletedCommercialRegistration.value = true;
        commercialRegistrationPreview.value = null;
    }
};

const submit = () => {
    const formData = new FormData();

    // Append all form data except files
    Object.keys(form.data()).forEach(key => {
        // Skip null files but keep deletion flags
        if ((key === 'logo' || key === 'commercial_registration_image') && form[key] === null) {
            return;
        }

        // Convert boolean values to 1/0 for FormData
        if (key === 'delete_logo' || key === 'delete_commercial_registration_image') {
            formData.append(key, form[key] ? '1' : '0');
        } else {
            formData.append(key, form[key]);
        }
    });

    // Append files if they exist
    if (form.logo instanceof File) {
        formData.append('logo', form.logo);
    }
    if (form.commercial_registration_image instanceof File) {
        formData.append('commercial_registration_image', form.commercial_registration_image);
    }

    // Send the request
    form.post(route('vendors.update', props.vendor.id), {
        data: formData,
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
