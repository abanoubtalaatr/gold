<template>

    <Head :title="$t('City Details')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('City Details') }}: {{ city.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $t('Basic Information') }}</h3>

                                <div class="space-y-2">
                                    <p><strong class="text-gray-700">{{ $t('City Name') }}:</strong> {{ city.name }}</p>
                                    <p v-if="city.name_ar"><strong class="text-gray-700">{{ $t('Arabic City Name')
                                    }}:</strong>
                                        {{ city.name_ar }}</p>
                                    <p><strong class="text-gray-700">{{ $t('Status') }}:</strong>
                                        <span
                                            :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', city.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                            {{ city.status === 'active' ? $t('Active') : $t('Inactive') }}
                                        </span>
                                    </p>
                                    <p><strong class="text-gray-700">{{ $t('Created At') }}:</strong> {{
                                        formatDate(city.created_at) }}</p>
                                    <p><strong class="text-gray-700">{{ $t('Updated At') }}:</strong> {{
                                        formatDate(city.updated_at) }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $t('Location Information') }}</h3>

                                <div class="space-y-2">
                                    <p><strong class="text-gray-700">{{ $t('State') }}:</strong> {{ city?.state.name }}
                                    </p>

                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <Link :href="route('admin.cities.index')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                            {{ $t('Back to Cities') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    city: {
        type: Object,
        required: true,
    },
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};
</script>
