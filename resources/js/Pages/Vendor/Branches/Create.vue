<template>

    <Head :title="editMode ? $t('Edit Branch') : $t('Add New Branch')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                {{ editMode ? $t('Edit Branch') : $t('Add New Branch') }}
            </h2>
        </template>

        <div class="py-6 bg-gradient-to-b from-gray-50 to-white min-h-screen">
            <div class="mx-auto sm:px-3 lg:px-4">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="p-4">
                        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-12 gap-2">
                            <!-- Branch Name -->
                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="name" :value="$t('Branch Name')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="name" v-model="form.name" type="text"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.name }"
                                    :placeholder="$t('Branch Name')" />
                                <InputError :message="form.errors.name" class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- City Selection -->
                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="city_id" :value="$t('City')"
                                    class="text-sm font-semibold text-gray-800" />
                                <select id="city_id" v-model="form.city_id"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.city_id }">
                                    <option value="" disabled selected>{{ $t('Select a city') }}</option>
                                    <option v-for="city in cities" :key="city.value" :value="city.value">
                                        {{ city.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.city_id"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- User Selection -->
                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="user_id" :value="$t('Assign User')"
                                    class="text-sm font-semibold text-gray-800" />
                                <select id="user_id" v-model="form.user_id"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.user_id }">
                                    <option value="" disabled selected>{{ $t('Select a user') }}</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.user_id"
                                    class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- Contact Number -->
                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="contact_number" :value="$t('Contact Number')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="contact_number" v-model="form.contact_number" type="tel"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.contact_number }"
                                    :placeholder="$t('Contact Number')" />
                                <InputError :message="form.errors.contact_number" class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- Contact Email -->
                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="contact_email" :value="$t('Contact Email')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="contact_email" v-model="form.contact_email" type="email"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.contact_email }"
                                    :placeholder="$t('Contact Email')" />
                                <InputError :message="form.errors.contact_email" class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- Number of Available Items -->
                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="number_of_available_items" :value="$t('Number of Available Items')"
                                    class="text-sm font-semibold text-gray-800" />
                                <TextInput id="number_of_available_items" v-model="form.number_of_available_items" type="number"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 ease-in-out"
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.number_of_available_items }"
                                    :placeholder="$t('Number of Available Items')" min="0" />
                                <InputError :message="form.errors.number_of_available_items" class="mt-1 text-xs text-red-500 font-medium" />
                            </div>

                            <!-- Branch Logo -->
                            <div class="col-span-1 md:col-span-6">
                                <InputLabel for="logo" :value="$t('Branch Logo')"
                                    class="text-sm font-semibold text-gray-800" />
                                <input id="logo" type="file" @input="form.logo = $event.target.files[0]"
                                    class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                    :class="{ 'border-red-500 focus:border-red-500': form.errors.logo }"
                                    accept="image/*" />
                                <p class="mt-1 text-xs text-gray-500 font-medium">
                                    {{ $t('Max 2MB (JPG, PNG)') }}
                                </p>
                                <InputError :message="form.errors.logo" class="mt-1 text-xs text-red-500 font-medium" />
                                
                                <!-- Logo Preview -->
                                <div v-if="logoPreview" class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $t('Logo Preview') }}</h4>
                                    <div class="relative">
                                        <img :src="logoPreview" class="h-20 w-20 object-cover rounded border">
                                        <button 
                                            type="button"
                                            @click="removeLogo"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Working Days and Hours -->
                            <div class="col-span-1 md:col-span-6">
                                <InputLabel :value="$t('Working Days and Hours')"
                                    class="text-sm font-semibold text-gray-800" />
                                <div class="mt-1 bg-gray-50 border-2 border-gray-200 rounded-md p-2">
                                    <InputError :message="form.errors.working_days"
                                        class="text-xs text-red-500 font-medium" />
                                    <div class="space-y-2">
                                        <div v-for="day in weekDays" :key="day.value"
                                            class="flex items-center space-x-2">
                                            <div class="w-1/3">
                                                <label class="flex items-center group">
                                                    <Checkbox :value="day.value" :modelValue="form.working_days"
                                                        @update:modelValue="(newValue) => {
                                                            form.working_days = Array.isArray(newValue) ? newValue :
                                                                (newValue ? [...(Array.isArray(form.working_days) ? form.working_days : []), day.value] :
                                                                    (Array.isArray(form.working_days) ? form.working_days.filter(d => d !== day.value) : []));
                                                            handleDaySelection(day.value);
                                                        }" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-0 group-hover:border-indigo-400 transition-all duration-200"
                                                        :class="{ 'border-red-500': form.errors.working_days }" />
                                                    <span
                                                        class="ml-2 text-xs font-medium text-gray-700 px-1 group-hover:text-indigo-600 transition-colors duration-200">{{
                                                        day.label }}</span>
                                                </label>
                                            </div>
                                            <div v-if="form.working_days.includes(day.value)"
                                                class="flex-1 grid grid-cols-2 gap-1 items-center">
                                                <TimePicker v-model="form.working_hours[day.value].open"
                                                    :placeholder="$t('Open')"
                                                    class="rounded-md border-2 border-gray-200 bg-white text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 text-xs"
                                                    :class="{ 'border-red-500 focus:border-red-500': form.errors[`working_hours.${day.value}.open`] }" />
                                                <TimePicker v-model="form.working_hours[day.value].close"
                                                    :placeholder="$t('Close')"
                                                    class="rounded-md border-2 border-gray-200 bg-white text-gray-900 focus:border-indigo-600 focus:ring-0 transition-all duration-200 text-xs"
                                                    :class="{ 'border-red-500 focus:border-red-500': form.errors[`working_hours.${day.value}.close`] }" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Branch Images -->
                            <!-- <div class="col-span-1 md:col-span-6">
                                <InputLabel
                                    :value="$t('Branch Images')"
                                    class="text-sm font-semibold text-gray-800"
                                /> -->
                            <!-- <FileUpload
                                    v-model="form.images"
                                    :multiple="true"
                                    accept="image/*"
                                    :maxFiles="5"
                                    :maxSize="5120"
                                    class="mt-1 block w-full rounded-md border-2 border-gray-200 bg-gray-50"
                                    :class="{ 'border-red-500': form.errors.images }"
                                /> -->
                            <!-- <p class="mt-1 text-xs text-gray-500 font-medium">
                                    {{ $t('Max 5 images, 5MB each (JPG, PNG)') }}
                                </p>
                                <InputError
                                    :message="form.errors.images"
                                    class="mt-1 text-xs text-red-500 font-medium"
                                /> -->

                            <!-- Display existing images in edit mode -->
                            <!-- <div v-if="editMode && existingImages.length" class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ $t('Existing Images') }}</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <div v-for="(image, index) in existingImages" :key="index" class="relative">
                                            <img :src="'/storage/' + image.path" class="h-20 w-20 object-cover rounded border">
                                            <button 
                                                type="button"
                                                @click="removeExistingImage(index)"
                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div> -->
                            <!-- </div> -->

                            <!-- Submit Button -->
                            <div class="col-span-1 md:col-span-12 flex items-center justify-end mt-3 space-x-2">
                                <Link :href="route('vendor.branches.index')"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 hover:text-gray-900 transition-all duration-200">
                                {{ $t('Cancel') }}
                                </Link>
                                <PrimaryButton
                                    class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-semibold text-xs rounded-md hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200"
                                    :disabled="form.processing || form.working_days.length === 0">
                                    {{ editMode ? $t('Update Branch') : $t('Create Branch') }}
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
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import TimePicker from '@/Components/TimePicker.vue';
import FileUpload from '@/Components/FileUpload.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({
    cities: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
    branch: {
        type: Object,
        default: null,
    },
});

const editMode = computed(() => !!props.branch);

const weekDays = [
    { value: 0, label: t('Sunday') },
    { value: 1, label: t('Monday') },
    { value: 2, label: t('Tuesday') },
    { value: 3, label: t('Wednesday') },
    { value: 4, label: t('Thursday') },
    { value: 5, label: t('Friday') },
    { value: 6, label: t('Saturday') },
];

// Initialize form with default working hours structure
const initializeWorkingHours = (workingDays = [], existingHours = {}) => {
    const hours = {};
    weekDays.forEach(day => {
        hours[day.value] = {
            open: existingHours[day.value]?.open || '09:00',
            close: existingHours[day.value]?.close || '17:00'
        };
    });
    return hours;
};

const form = useForm({
    name: props.branch?.name || '',
    city_id: props.branch?.city_id || '',
    user_id: props.branch?.user_id || '',
    contact_number: props.branch?.contact_number || '',
    contact_email: props.branch?.contact_email || '',
    number_of_available_items: props.branch?.number_of_available_items || 0,
    working_days: props.branch?.working_days || [], // Ensure this is always an array
    working_hours: initializeWorkingHours(
        props.branch?.working_days,
        props.branch?.working_hours || {}
    ),
    logo: null,
    images: [],
    deleted_images: [],
});

const existingImages = ref(props.branch?.images || []);
const logoPreview = ref(null);

// Watch for logo file changes to generate preview
watch(() => form.logo, (newLogo) => {
    if (newLogo) {
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target.result;
        };
        reader.readAsDataURL(newLogo);
    } else {
        logoPreview.value = null;
    }
});

const handleDaySelection = (dayValue) => {
    // Ensure working_days is always treated as an array
    const workingDays = Array.isArray(form.working_days) ? form.working_days : [];

    if (workingDays.includes(dayValue)) {
        // Day is selected - ensure working hours exist
        if (!form.working_hours[dayValue]) {
            form.working_hours[dayValue] = { open: '09:00', close: '17:00' };
        }
    } else {
        // Day is deselected - remove from working hours if empty
        if (form.working_hours[dayValue] &&
            form.working_hours[dayValue].open === '09:00' &&
            form.working_hours[dayValue].close === '17:00') {
            delete form.working_hours[dayValue];
        }
    }
};

const removeExistingImage = (index) => {
    const image = existingImages.value[index];
    form.deleted_images.push(image.id);
    existingImages.value.splice(index, 1);
};

const removeLogo = () => {
    form.logo = null;
    logoPreview.value = null;
    // Clear the file input
    const fileInput = document.getElementById('logo');
    if (fileInput) {
        fileInput.value = '';
    }
};

const submit = () => {
    if (form.working_days.length === 0) {
        form.errors.working_days = t('Please select at least one working day');
        return;
    }

    // Filter working_hours to only include selected days
    const filteredWorkingHours = {};
    form.working_days.forEach(day => {
        if (form.working_hours[day]) {
            filteredWorkingHours[day] = form.working_hours[day];
        }
    });
    form.working_hours = filteredWorkingHours;

    if (editMode.value) {
        form.post(route('vendor.branches.update', props.branch.id), {
            preserveScroll: true,
            onError: (errors) => {
                console.log('Update errors:', errors);
            },
        });
    } else {
        form.post(route('vendor.branches.store'), {
            preserveScroll: true,
            onError: (errors) => {
                console.log('Creation errors:', errors);
            },
        });
    }
};
</script>