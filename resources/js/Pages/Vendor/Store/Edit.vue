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
                                        <input id="commercial_image" type="file"
                                            @change="handleFileChange($event, 'commercial_image')"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                            accept="image/*,.pdf" />
                                        <InputError :message="form.errors.commercial_image" class="mt-2" />
                                        <p v-if="store.commercial_image" class="mt-1 text-sm text-gray-500">
                                            {{ $t('Current file:') }}
                                            <a :href="store.commercial_image" target="_blank"
                                                class="text-blue-600 hover:text-blue-800">
                                                {{ $t('View') }}
                                            </a>
                                        </p>
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
                                        <input id="logo" type="file" @change="handleFileChange($event, 'logo')"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                            accept="image/*" />
                                        <InputError :message="form.errors.logo" class="mt-2" />
                                        <div v-if="store.logo" class="mt-2">
                                            <img :src="store.logo" class="h-24 w-24 rounded-lg object-cover" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Working Hours -->
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
import {
    Combobox,
    ComboboxInput,
    ComboboxOptions,
    ComboboxOption,
} from '@headlessui/vue';

const props = defineProps({
    store: Object
});

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
    working_hours: props.store.working_hours || [
        { day: 'Sunday', open: '09:00', close: '18:00', closed: false },
        { day: 'Monday', open: '09:00', close: '18:00', closed: false },
        { day: 'Tuesday', open: '09:00', close: '18:00', closed: false },
        { day: 'Wednesday', open: '09:00', close: '18:00', closed: false },
        { day: 'Thursday', open: '09:00', close: '18:00', closed: false },
        { day: 'Friday', open: '09:00', close: '18:00', closed: true },
        { day: 'Saturday', open: '09:00', close: '18:00', closed: false }
    ]
});

// City selection
const query = ref('');
const selectedCity = ref(null);
const cities = ref([]);
const loading = ref(false);

// Load initial city if exists
if (props.store.address?.city_id) {
    loading.value = true;
    axios.get(`/api/cities/${props.store.address.city_id}`)
        .then(response => {
            selectedCity.value = response.data;
            cities.value = [response.data];
        })
        .finally(() => {
            loading.value = false;
        });
}

// Watch for city selection changes
watch(selectedCity, (newCity) => {
    if (newCity) {
        form.address.city_id = newCity.id;
    } else {
        form.address.city_id = null;
    }
});

// Search cities when query changes
watch(query, async (newQuery) => {
    if (newQuery.length > 2) {
        loading.value = true;
        try {
            const response = await axios.get('/api/cities', {
                params: { search: newQuery }
            });
            cities.value = response.data;
        } catch (error) {
            console.error('Error fetching cities:', error);
        } finally {
            loading.value = false;
        }
    }
});

// Filter cities based on search query
const filteredCities = computed(() => {
    if (query.value === '') {
        return cities.value;
    }
    return cities.value.filter((city) => {
        return city.name.toLowerCase().includes(query.value.toLowerCase());
    });
});

const handleFileChange = (event, field) => {
    form[field] = event.target.files[0];
};

const submit = () => {
    form.post(route('vendor.store.update'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset('commercial_image', 'logo');
        }
    });
};
</script>
