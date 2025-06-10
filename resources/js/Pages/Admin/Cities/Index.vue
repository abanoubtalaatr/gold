<template>

    <Head title="Cities Management" />

    <AuthenticatedLayout>
        <template #header>

        </template>

        <div class="py-12">

            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Filters -->
                        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                            <div class="flex-1 min-w-0">
                                <input v-model="form.search" type="text"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :placeholder="$t('Search by city name...')" @input="debouncedSearch" />
                            </div>
                            <div class="flex items-center gap-4">
                                <select v-model="form.status"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    @change="applyFilters">
                                    <option value="">{{ $t('All Statuses') }}</option>
                                    <option value="active">{{ $t('Active') }}</option>
                                    <option value="inactive">{{ $t('Inactive') }}</option>
                                </select>
                                <button @click="resetFilters"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                    {{ $t('Reset') }}
                                </button>

                                <Link :href="route('admin.cities.create')"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                {{ $t('Add New City') }}
                                </Link>
                            </div>
                        </div>

                        <!-- Cities Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            #</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('City Name') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Status') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Created At') }}</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(city, index) in cities.data" :key="city.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ city.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span v-if="city.status"
                                                class="px-3 py-1 inline-flex items-center text-sm font-medium rounded-full bg-green-100 text-green-800">

                                                {{ $t('Active') }}
                                            </span>
                                            <span v-else
                                                class="px-3 py-1 inline-flex items-center text-sm font-medium rounded-full bg-red-100 text-red-800">
                                                {{ $t('Inactive') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                            formatDate(city.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="relative">
                                                <button @click="toggleDropdown(city.id)"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-500 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>

                                                <div v-show="activeDropdown === city.id"
                                                    class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                    <Link :href="route('admin.cities.show', city.id)"
                                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    {{ $t('View Details') }}
                                                    </Link>
                                                    <Link :href="route('admin.cities.edit', city.id)"
                                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    {{ $t('Edit') }}
                                                    </Link>
                                                    <button @click="toggleStatus(city)"
                                                        class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100 select-button">
                                                        {{ city.status ? $t('Deactivate') : $t('Activate') }}
                                                    </button>
                                                    <button @click="deleteCity(city)"
                                                        class="block w-full px-4 py-2 text-sm text-left text-red-700 hover:bg-red-50 select-button">
                                                        {{ $t('Delete') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <Pagination :links="cities.links" class="mt-6" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import debounce from 'lodash/debounce';
import Swal from 'sweetalert2';

const { t } = useI18n();

const props = defineProps({
    cities: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const form = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
});

const activeDropdown = ref(null);

const toggleDropdown = (cityId) => {
    activeDropdown.value = activeDropdown.value === cityId ? null : cityId;
};

document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
        activeDropdown.value = null;
    }
});

const debouncedSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    router.get(route('admin.cities.index'), form.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.value = {
        search: '',
        status: '',
    };
    applyFilters();
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const toggleStatus = (city) => {
    router.patch(route('admin.cities.toggle-status', city.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            activeDropdown.value = null;
        },
    });
};

const deleteCity = async (city) => {
    const result = await Swal.fire({
        title: t('Delete City?'),
        text: t('Are you sure you want to delete this city? This action cannot be undone.'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('Delete'),
        cancelButtonText: t('Cancel'),
        confirmButtonColor: '#d33',
    });

    if (result.isConfirmed) {
        router.delete(route('admin.cities.destroy', city.id), {
            preserveScroll: true,
            onSuccess: () => {
                activeDropdown.value = null;
                Swal.fire(
                    t('Deleted!'),
                    t('The city has been deleted.'),
                    'success'
                );
            },
            onError: (errors) => {
                activeDropdown.value = null;
                // Handle validation errors
                const errorMessage = errors.city || errors.message || t('Failed to delete city.');
                Swal.fire(
                    t('Error'),
                    errorMessage,
                    'error'
                );
            }
        });
    }
};
</script>


<style scoped>
.select-button {
    background-color: inherit !important;
    border: none !important;
}

.select-button:hover {
    background-color: rgb(226, 226, 226) !important;
    color: black !important;
}
</style>
