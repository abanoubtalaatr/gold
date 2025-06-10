<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Edit Client') }}
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
                                            <select v-model="form.dialling_code" disabled
                                                class="flex-shrink-0 w-24 px-3 py-2 text-gray-500 bg-gray-100 border-gray-300 rounded-l-md focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="+966">+966 (SA)</option>
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

                                <!-- Additional Information -->
                                <div class="space-y-6">
                                    <h3 class="text-lg font-medium">{{ $t('Additional Information') }}</h3>

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

                            <div class="flex justify-end mt-6 space-x-3">
                                <Link :href="route('clients.show', client.id)"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ $t('Cancel') }}
                                </Link>
                                <button type="submit" :disabled="form.processing"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                    <span v-if="form.processing">{{ $t('Updating...') }}</span>
                                    <span v-else>{{ $t('Update Client') }}</span>
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
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    client: Object,
    cities: Array,
});

const form = useForm({
    name: props.client.name,
    email: props.client.email,
    mobile: props.client.mobile,
    dialling_code: props.client.dialling_code,
    iban: props.client.iban,
    city_id: props.client.city_id,
});

const submit = () => {
    form.patch(route('clients.update', props.client.id), {
        onSuccess: () => {
            // Success handled by redirect
        },
    });
};
</script>
