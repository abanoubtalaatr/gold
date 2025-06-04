<template>
    <Head title="Edit Store Information" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Edit Store Information') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Information -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $t('Basic Information') }}</h3>

                                    <div>
                                        <InputLabel for="name" :value="$t('Name')" />
                                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full"
                                            required />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="email" :value="$t('Email')" />
                                        <TextInput id="email" v-model="form.email" type="email"
                                            class="mt-1 block w-full" required />
                                        <InputError :message="form.errors.email" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="mobile" :value="$t('Mobile')" />
                                        <div class="flex gap-2">
                                            <select v-model="form.dialling_code"
                                                class="mt-1 block w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="+966">+966</option>
                                            </select>
                                            <TextInput id="mobile" v-model="form.mobile" type="tel"
                                                class="mt-1 block w-full" required />
                                        </div>
                                        <InputError :message="form.errors.mobile" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Store Information -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $t('Store Information') }}</h3>

                                    <div>
                                        <InputLabel for="store_name_en" :value="$t('Store Name (English)')" />
                                        <TextInput id="store_name_en" v-model="form.store_name_en" type="text"
                                            class="mt-1 block w-full" required />
                                        <InputError :message="form.errors.store_name_en" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="store_name_ar" :value="$t('Store Name (Arabic)')" />
                                        <TextInput id="store_name_ar" v-model="form.store_name_ar" type="text"
                                            class="mt-1 block w-full" required />
                                        <InputError :message="form.errors.store_name_ar" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="commercial_number"
                                            :value="$t('Commercial Registration Number')" />
                                        <TextInput id="commercial_number" v-model="form.commercial_number" type="text"
                                            class="mt-1 block w-full" required />
                                        <InputError :message="form.errors.commercial_number" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="commercial_image"
                                            :value="$t('Commercial Registration Image')" />
                                        <div class="mt-1">
                                            <input id="commercial_image" type="file"
                                                @change="handleFileChange($event, 'commercial_image')"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                accept="image/*,.pdf" />

                                            <!-- Preview for new image -->
                                            <div v-if="commercialImagePreview" class="mt-2">
                                                <div class="relative inline-block">
                                                    <img :src="commercialImagePreview"
                                                        alt="Commercial registration preview"
                                                        class="h-32 object-contain border rounded-md" />
                                                    <button type="button" @click="removeImage('commercial_image')"
                                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Current image link -->
                                            <p v-if="store.commercial_image && !commercialImagePreview"
                                                class="mt-2 text-sm text-gray-500">
                                                {{ $t('Current file:') }}
                                                <a :href="store.commercial_image" target="_blank"
                                                    class="text-blue-600 hover:text-blue-800">
                                                    {{ $t('View') }}
                                                </a>
                                            </p>
                                        </div>
                                        <InputError :message="form.errors.commercial_image" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $t('Location') }}</h3>

                                    <div>
                                        <InputLabel for="address" :value="$t('Address')" />
                                        <TextInput id="address" v-model="form.address.address" type="text"
                                            class="mt-1 block w-full" required />
                                        <InputError :message="form.errors['address.address']" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="city_id" :value="$t('City')" />
                                        <select id="city_id" v-model="form.address.city_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            required>
                                            <option value="" disabled>{{ $t('Select a city') }}</option>
                                            <option v-for="city in store.cities" :key="city.id" :value="city.id">
                                                {{ city.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors['address.city_id']" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Financial & Other Info -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $t('Financial Information') }}</h3>

                                    <div>
                                        <InputLabel for="iban" :value="$t('IBAN')" />
                                        <TextInput id="iban" v-model="form.iban" type="text" class="mt-1 block w-full"
                                            required />
                                        <InputError :message="form.errors.iban" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="logo" :value="$t('Store Logo')" />
                                        <div class="mt-1">
                                            <input id="logo" type="file" @change="handleFileChange($event, 'logo')"
                                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                accept="image/*" />

                                            <!-- Preview for new logo -->
                                            <div v-if="logoPreview" class="mt-2">
                                                <div class="relative inline-block">
                                                    <img :src="logoPreview" alt="Logo preview"
                                                        class="h-24 w-24 rounded-lg object-cover border" />
                                                    <button type="button" @click="removeImage('logo')"
                                                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Current logo -->
                                            <div v-if="store.logo && !logoPreview" class="mt-2">
                                                <img :src="store.logo"
                                                    class="h-24 w-24 rounded-lg object-cover border" />
                                            </div>
                                        </div>
                                        <InputError :message="form.errors.logo" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Working Hours -->
                                <div class="space-y-4">
                                    <InputLabel :value="$t('Working Hours')" />
                                    <div class="mt-2 space-y-2">
                                        <div v-for="(day, index) in form.working_hours" :key="index"
                                            class="grid grid-cols-5 gap-2 items-center">
                                            <span class="text-sm font-medium text-gray-700">{{ $t(day.day) }}</span>
                                            <TextInput v-model="day.open" type="time" class="w-full"
                                                :disabled="day.closed" />
                                            <TextInput v-model="day.close" type="time" class="w-full"
                                                :disabled="day.closed" />
                                            <div class="flex items-center">
                                                <input v-model="day.closed" :id="`closed-${index}`" type="checkbox"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                                                <label :for="`closed-${index}`"
                                                    class="ml-2 block text-sm text-gray-700">
                                                    {{ $t('Closed') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <InputError :message="form.errors.working_hours" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end space-x-4">
                                <Link :href="route('vendor.store.show')"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                                {{ $t('Cancel') }}
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    <span v-if="form.processing">{{ $t('Saving...') }}</span>
                                    <span v-else>{{ $t('Save Changes') }}</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    store: Object
});

// Image preview refs
const commercialImagePreview = ref(null);
const logoPreview = ref(null);

// Initialize working hours properly
const initializeWorkingHours = () => {
    if (props.store.working_hours && props.store.working_hours.length) {
        return props.store.working_hours.map(day => ({
            day: day.day,
            open: day.open || '09:00',
            close: day.close || '18:00',
            closed: day.closed === true || day.closed === 1 || false
        }));
    }

    // Default working hours
    return [
        { day: 'Sunday', open: '09:00', close: '18:00', closed: false },
        { day: 'Monday', open: '09:00', close: '18:00', closed: false },
        { day: 'Tuesday', open: '09:00', close: '18:00', closed: false },
        { day: 'Wednesday', open: '09:00', close: '18:00', closed: false },
        { day: 'Thursday', open: '09:00', close: '18:00', closed: false },
        { day: 'Friday', open: '09:00', close: '18:00', closed: true },
        { day: 'Saturday', open: '09:00', close: '18:00', closed: false }
    ];
};

// Form setup
const form = useForm({
    name: props.store.name,
    email: props.store.email,
    mobile: props.store.mobile,
    dialling_code: props.store.dialling_code || '+966',
    store_name_en: props.store.store_name_en,
    store_name_ar: props.store.store_name_ar,
    commercial_number: props.store.commercial_number,
    commercial_image: null,
    logo: null,
    iban: props.store.iban,
    address: {
        address: props.store.address?.address || '',
        city_id: props.store.address?.city_id || null
    },
    working_hours: initializeWorkingHours()
});

const handleFileChange = (event, field) => {
    const file = event.target.files[0];
    form[field] = file;

    // Create preview for images
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            if (field === 'commercial_image') {
                commercialImagePreview.value = e.target.result;
            } else if (field === 'logo') {
                logoPreview.value = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    } else {
        if (field === 'commercial_image') {
            commercialImagePreview.value = null;
        } else if (field === 'logo') {
            logoPreview.value = null;
        }
    }
};

const removeImage = (field) => {
    if (field === 'commercial_image') {
        commercialImagePreview.value = null;
        form.commercial_image = null;
        document.getElementById('commercial_image').value = '';
    } else if (field === 'logo') {
        logoPreview.value = null;
        form.logo = null;
        document.getElementById('logo').value = '';
    }
};

const submit = () => {
    form.post(route('vendor.store.update'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset('commercial_image', 'logo');
            commercialImagePreview.value = null;
            logoPreview.value = null;
        }
    });
};
</script>
