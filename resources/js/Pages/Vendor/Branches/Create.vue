<template>

    <Head title="Add New Branch" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Add New Branch') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Branch Name -->
                            <div>
                                <InputLabel for="name" :value="$t('Branch Name')" />
                                <TextInput id="name" v-model="form.name" type="text" class="block w-full mt-1"
                                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.name }" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- City Selection -->
                            <div>
                                <InputLabel for="city_id" :value="$t('City')" />
                                <select id="city_id" v-model="form.city_id"
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.city_id }">
                                    <option value="" disabled selected>{{ $t('Select a city') }}</option>
                                    <option v-for="city in cities" :key="city.value" :value="city.value">
                                        {{ city.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.city_id" class="mt-2" />
                            </div>

                            <!-- Working Days and Hours -->
                            <div class="space-y-4 working-days-section">
                                <InputLabel :value="$t('Working Days and Hours')" />
                                <InputError :message="form.errors.working_days" class="mt-2" />
                                <InputError :message="form.errors['working_days.0']" class="mt-2" />
                                <div class="grid gap-4">
                                    <div v-for="day in weekDays" :key="day.value" class="flex items-center space-x-4">
                                        <div class="w-1/4">
                                            <label class="flex items-center">
                                                <Checkbox :value="day.value" v-model:checked="form.working_days"
                                                    :class="{ 'border-red-500': form.errors.working_days || form.errors['working_days.0'] }" />
                                                <span class="ml-2 text-sm text-gray-600">{{ day.label }}</span>
                                            </label>
                                        </div>
                                        <div v-if="form.working_days.includes(day.value)"
                                            class="flex-1 flex items-center space-x-2">
                                            <TimePicker v-model="form.working_hours[day.value].open"
                                                :placeholder="$t('Opening Time')"
                                                :class="{ 'border-red-500': form.errors['working_hours.' + day.value + '.open'] }" />
                                            <span class="text-gray-500">to</span>
                                            <TimePicker v-model="form.working_hours[day.value].close"
                                                :placeholder="$t('Closing Time')"
                                                :class="{ 'border-red-500': form.errors['working_hours.' + day.value + '.close'] }" />
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="form.errors.working_hours" class="mt-2" />
                            </div>

                            <!-- Services -->
                            <div>
                                <InputLabel :value="$t('Available Services')" />
                                <MultiSelect v-model="form.services" :options="services"
                                    :placeholder="$t('Select services')" class="mt-1"
                                    :class="{ 'border-red-500': form.errors.services || form.errors['services.0'] }" />
                                <InputError :message="form.errors.services" class="mt-2" />
                                <InputError :message="form.errors['services.0']" class="mt-2" />
                            </div>

                            <!-- Branch Images -->
                            <div>
                                <InputLabel :value="$t('Branch Images')" />
                                <FileUpload v-model="form.images" :multiple="true" accept="image/*" :maxFiles="5"
                                    :maxSize="5120" class="mt-1"
                                    :class="{ 'border-red-500': form.errors.images || form.errors['images.0'] }" />
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $t('Maximum 5 images, each up to 5MB (JPG, PNG)') }}
                                </p>
                                <InputError :message="form.errors.images" class="mt-2" />
                                <InputError :message="form.errors['images.0']" class="mt-2" />
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end mt-6">
                                <Link :href="route('vendor.branches.index')" class="text-gray-600 hover:text-gray-900">
                                {{ $t('Cancel') }}
                                </Link>
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    {{ $t('Create Branch') }}
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
import { computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Checkbox from '@/Components/Checkbox.vue'
import TimePicker from '@/Components/TimePicker.vue'
import MultiSelect from '@/Components/MultiSelect.vue'
import FileUpload from '@/Components/FileUpload.vue'

const props = defineProps({
    services: {
        type: Array,
        default: () => []
    },
    cities: {
        type: Array,
        default: () => []
    }
})

const weekDays = [
    { value: 0, label: 'Sunday' },
    { value: 1, label: 'Monday' },
    { value: 2, label: 'Tuesday' },
    { value: 3, label: 'Wednesday' },
    { value: 4, label: 'Thursday' },
    { value: 5, label: 'Friday' },
    { value: 6, label: 'Saturday' },
]

const form = useForm({
    name: '',
    city_id: '',
    working_days: [],
    working_hours: {},
    services: [],
    images: [],
}, {
    resetOnSuccess: false,
})

const selectedDays = computed(() => {
    return form.working_days.sort((a, b) => a - b)
})

watch(() => form.working_days, (newDays) => {
    // Initialize working hours for newly selected days
    newDays.forEach(day => {
        if (!form.working_hours[day]) {
            form.working_hours[day] = {
                open: '09:00',
                close: '17:00'
            }
        }
    })

    // Remove working hours for unselected days
    Object.keys(form.working_hours).forEach(day => {
        if (!newDays.includes(Number(day))) {
            delete form.working_hours[day]
        }
    })
}, { deep: true })

const submit = () => {
    // Prepare the data
    const postData = {
        name: form.name,
        city_id: form.city_id,
        working_days: form.working_days.map(Number),
        services: form.services.map(Number),
        working_hours: Object.fromEntries(
            form.working_days.map(day => [
                day,
                form.working_hours[day] || { open: '09:00', close: '17:00' }
            ])
        ),
        images: form.images
    };
    console.log(postData);
    // Submit using Inertia
    form.post(route('vendor.branches.store'), postData, {
        preserveScroll: true,
        onError: (errors) => {
            const firstError = document.querySelector('.border-red-500');
            if (firstError) firstError.scrollIntoView();
        }
    });
}
</script>
