<template>
    <Head :title="$t('Edit City')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Edit City') }}: {{ city.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('City Name')
                                        }}</label>
                                    <input v-model="form.name" type="text"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required />
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.name }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Arabic City Name')
                                        }}</label>
                                    <input v-model="form.name_ar" type="text"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                    <p v-if="form.errors.name_ar" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.name_ar }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Status')
                                        }}</label>
                                    <select v-model="form.status"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required>
                                        <option value="active">{{ $t('Active') }}</option>
                                        <option value="inactive">{{ $t('Inactive') }}</option>
                                    </select>
                                    <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.status }}
                                    </p>
                                </div>

                                <div class="flex justify-end space-x-4">
                                    <Link :href="route('admin.cities.index')"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                    {{ $t('Cancel') }}
                                    </Link>
                                    <button type="submit" :disabled="form.processing"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                                        {{ form.processing ? $t('Updating...') : $t('Update') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    city: {
        type: Object,
        required: true,
    },
    states: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: props.city.name,
    name_ar: props.city.name_ar,
    state_id: props.city.state_id,
    status: props.city.status,
});

const submit = () => {
    form.put(route('admin.cities.update', props.city.id));
};
</script>