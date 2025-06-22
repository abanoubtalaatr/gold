<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                إدارة العملاء
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ $t('all clients') }}</h3>
                            <Link :href="route('clients.create')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                {{ $t('add new client') }}
                            </Link>
                        </div>

                        <!-- Search and Filter Section -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Search Input -->
                                <div class="md:col-span-2">
                                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $t('search') }}
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input
                                            id="search"
                                            v-model="searchForm.search"
                                            type="text"
                                            :placeholder="$t('search_by_name_email_mobile_or_iban')"
                                            class="block w-full pr-10 pl-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                            @input="debouncedSearch"
                                        />
                                        <button
                                            v-if="searchForm.search"
                                            @click="clearSearch"
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center"
                                        >
                                            <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Status Filter -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $t('filter by status') }}    
                                    </label>
                                    <select
                                        id="status"
                                        v-model="searchForm.status"
                                        @change="applyFilters"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                    >
                                        <option value="">{{ $t('all clients') }}</option>
                                        <option value="active">{{ $t('active clients') }}</option>
                                        <option value="inactive">{{ $t('inactive clients') }}</option>
                                        <option value="max_canceled_orders">{{ $t('max canceled orders') }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Active Filters Display -->
                            <div v-if="hasActiveFilters" class="mt-4 flex flex-wrap gap-2">
                                <span class="text-sm text-gray-600">{{ $t('active filters') }}:</span>
                                <span v-if="searchForm.search" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $t('search') }}: "{{ searchForm.search }}"
                                    <button @click="clearSearch" class="mr-1.5 inline-flex items-center justify-center w-4 h-4 text-indigo-400 hover:text-indigo-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </span>
                                <span v-if="searchForm.status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                      {{ $t('status') }}  : {{ getStatusLabel(searchForm.status) }}
                                    <button @click="clearStatusFilter" class="mr-1.5 inline-flex items-center justify-center w-4 h-4 text-green-400 hover:text-green-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </span>
                                <button @click="clearAllFilters" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                                    {{ $t('clear_all_filters') }}
                                    <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('client') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('mobile_number') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('location') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('iban') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('status') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="client in clients.data" :key="client.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-12 w-12">
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-sm">
                                                        <span class="text-sm font-semibold text-white">
                                                            {{ client.name.charAt(0).toUpperCase() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="mr-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ client.name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ client.email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-medium">
                                                {{ client.dialling_code }}{{ client.mobile }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ client.city?.name || $t('not defined') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 font-mono bg-gray-50 px-2 py-1 rounded text-center">
                                                {{ client.iban || $t('not defined') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-green-100 text-green-800 border-green-200': client.is_active,
                                                'bg-red-100 text-red-800 border-red-200': !client.is_active,
                                            }" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border">
                                                <svg :class="{
                                                    'text-green-400': client.is_active,
                                                    'text-red-400': !client.is_active,
                                                }" class="w-2 h-2 ml-1.5" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                {{ client.is_active ? $t('active') : $t('inactive') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="relative inline-block text-right">
                                                <button @click="toggleDropdown(client.id)"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-400 bg-white rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                                                    :class="{ 'bg-gray-100 text-gray-600': activeDropdown === client.id }">
                                                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>

                                                <!-- Dropdown menu -->
                                                <div v-show="activeDropdown === client.id"
                                                    class="absolute left-0 z-20 w-56 mt-2 origin-top-left bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                                    @click.stop>
                                                    <div class="py-1">
                                                        <Link :href="route('clients.show', client.id)"
                                                            class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150">
                                                            <svg class="w-4 h-4 ml-3 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            {{ $t('view details') }}
                                                        </Link>
                                                        <Link :href="route('clients.edit', client.id)"
                                                            class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150">
                                                            <svg class="w-4 h-4 ml-3 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            {{ $t('edit') }}
                                                        </Link>
                                                        <!-- <Link :href="route('admin.wallet.show', client.id)"
                                                            class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150">
                                                            <svg class="w-4 h-4 ml-3 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                            </svg>
                                                            {{ $t('wallet management') }}
                                                        </Link> -->
                                                        <div class="border-t border-gray-100"></div>
                                                        <button @click="toggleStatus(client)"
                                                            class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150">
                                                            <svg v-if="client.is_active" class="w-4 h-4 ml-3 text-red-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                            </svg>
                                                            <svg v-else class="w-4 h-4 ml-3 text-green-400 group-hover:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            {{ client.is_active ? $t('deactivate') : $t('activate') }}
                                                        </button>
                                                        <button @click="activeClientAfterMaxCanceledOrders(client)"
                                                            class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150">
                                                            {{ $t('max canceled orders active') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty state -->
                        <div v-if="!clients.data || clients.data.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                {{ hasActiveFilters ? $t('no results') : $t('no clients') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ hasActiveFilters ? $t('try changing search criteria or filters.') : $t('start by adding a new client.') }}
                            </p>
                            <div class="mt-6">
                                <button v-if="hasActiveFilters" @click="clearAllFilters"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ml-3">
                                    {{ $t('clear filters') }}
                                </button>
                                <Link :href="route('clients.create')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ $t('add new client') }}
                                </Link>
                            </div>
                        </div>

                        <Pagination :links="clients.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref, onMounted, onUnmounted, reactive, computed, watch } from 'vue';
import Swal from 'sweetalert2';
import { useI18n } from 'vue-i18n';
const props = defineProps({
    clients: Object,
    filters: Object,
});

const { t } = useI18n();

const activeDropdown = ref(null);

// Search and filter form
const searchForm = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});

// Debounced search functionality
let searchTimeout = null;

const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500); // 500ms delay
};

// Apply filters function
const applyFilters = () => {
    const params = {};
    
    if (searchForm.search) {
        params.search = searchForm.search;
    }
    
    if (searchForm.status) {
        params.status = searchForm.status;
    }

    router.get(route('clients.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
};

// Computed properties
const hasActiveFilters = computed(() => {
    return searchForm.search || searchForm.status;
});

// Helper functions
const getStatusLabel = (status) => {
    const labels = {
        'active': 'مفعل',
        'inactive': 'غير مفعل',
    };
    return labels[status] || status;
};

const clearSearch = () => {
    searchForm.search = '';
    applyFilters();
};

const clearStatusFilter = () => {
    searchForm.status = '';
    applyFilters();
};

const clearAllFilters = () => {
    searchForm.search = '';
    searchForm.status = '';
    router.get(route('clients.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const toggleDropdown = (clientId) => {
    activeDropdown.value = activeDropdown.value === clientId ? null : clientId;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        activeDropdown.value = null;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
});

const toggleStatus = async (client) => {
    const result = await Swal.fire({
        title: t('are you sure'),
        text: t('do you want to change the status of this client'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('yes, confirm'),
        cancelButtonText: t('cancel'),
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    });

    if (result.isConfirmed) {
        router.patch(route('clients.toggle-status', client.id), {}, {
            onSuccess: () => {
                activeDropdown.value = null;
                Swal.fire({
                    title: 'تم!',
                    text: 'تم تحديث حالة العميل بنجاح',
                    icon: 'success',
                    confirmButtonText: 'موافق'
                });
            },
            onError: () => {
                Swal.fire({
                    title: 'خطأ!',
                    text: 'فشل في تحديث حالة العميل',
                    icon: 'error',
                    confirmButtonText: 'موافق'
                });
            }
        });
    }
};
const activeClientAfterMaxCanceledOrders = async (client) => {
    const result = await Swal.fire({
        title: t('are you sure'),
        text: t('do you want to activate this client after max canceled orders'),
    });
    if (result.isConfirmed) {
        router.post(route('clients.delete-canceled-orders', client.id), {}, {
            onSuccess: () => {
                Swal.fire(t('client activated successfully'));
            }
        });
    }
};
</script>

<style scoped>
/* Custom styles for better RTL support */
.table-container {
    direction: rtl;
}

/* Smooth transitions for dropdowns */
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Hover effects */
.hover-lift:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Status badge animations */
.status-badge {
    transition: all 0.2s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}
</style>
