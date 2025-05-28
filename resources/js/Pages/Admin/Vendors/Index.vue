<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Vendor Management') }}
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between mb-6">
                            <h3 class="text-lg font-medium">{{ $t('All Vendors') }}</h3>
                            <Link :href="route('vendors.create')"
                                class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                            {{ $t('Add New Vendor') }}
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Vendor') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Store') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Status') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="vendor in vendors.data" :key="vendor.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-10 h-10">
                                                    <img class="w-10 h-10 rounded-full"
                                                        :src="vendor.media?.find(m => m.collection_name === 'logo')?.original_url || '/images/default-avatar.png'"
                                                        alt="" />
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ vendor.name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ vendor.email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ vendor.store_name_en }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ vendor.store_name_ar }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'bg-green-100 text-green-800': vendor.vendor_status === 'approved',
                                                'bg-yellow-100 text-yellow-800': vendor.vendor_status === 'pending',
                                                'bg-red-100 text-red-800': vendor.vendor_status === 'rejected',
                                            }" class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full">
                                                {{ $t(vendor.vendor_status) }}
                                            </span>
                                            <span v-if="!vendor.is_active"
                                                class="inline-flex px-2 ml-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">
                                                {{ $t('Inactive') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="relative">
                                                <button @click="toggleDropdown(vendor.id)"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-gray-500 rounded-full hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                    aria-expanded="false" :aria-haspopup="`actions-menu-${vendor.id}`">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                    </svg>
                                                </button>

                                                <!-- Dropdown menu -->
                                                <div v-show="activeDropdown === vendor.id"
                                                    :id="`actions-menu-${vendor.id}`"
                                                    class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none select">
                                                    <Link :href="route('vendors.show', vendor.id)"
                                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    {{ $t('View Details') }}
                                                    </Link>
                                                    <Link :href="route('vendors.edit', vendor.id)"
                                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    {{ $t('Edit') }}
                                                    </Link>
                                                    <button @click="toggleStatus(vendor)"
                                                        class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                        {{ vendor.is_active ? $t('Deactivate') : $t('Activate') }}
                                                    </button>
                                                    <template v-if="vendor.vendor_status === 'pending'">
                                                        <button @click="approveVendor(vendor)"
                                                            class="block w-full px-4 py-2 text-sm text-left text-green-700 hover:bg-green-50">
                                                            {{ $t('Approve') }}
                                                        </button>
                                                        <button @click="showRejectForm(vendor)"
                                                            class="block w-full px-4 py-2 text-sm text-left text-red-700 hover:bg-red-50">
                                                            {{ $t('Reject') }}
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="vendors.links" class="mt-4" />
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
import { ref } from 'vue';
import Swal from 'sweetalert2';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    vendors: Object,
});

const activeDropdown = ref(null);

const toggleDropdown = (vendorId) => {
    activeDropdown.value = activeDropdown.value === vendorId ? null : vendorId;
};

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
        activeDropdown.value = null;
    }
});

const toggleStatus = async (vendor) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('Do you want to change this vendor\'s status?'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('Yes, proceed'),
        cancelButtonText: t('Cancel'),
    });

    if (result.isConfirmed) {
        router.patch(route('vendors.toggle-status', vendor.id), {
            onSuccess: () => {
                activeDropdown.value = null;
                Swal.fire(
                    t('Success'),
                    t('Vendor status has been updated'),
                    'success'
                );
            },
            onError: () => {
                Swal.fire(
                    t('Error'),
                    t('Failed to update vendor status'),
                    'error'
                );
            }
        });
    }
};

const approveVendor = async (vendor) => {
    const result = await Swal.fire({
        title: t('Are you sure?'),
        text: t('Do you want to approve this vendor?'),
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: t('Yes, approve'),
        cancelButtonText: t('Cancel'),
    });

    if (result.isConfirmed) {
        router.post(route('vendors.approve', vendor.id), {
            onSuccess: () => {
                activeDropdown.value = null;
                Swal.fire(
                    t('Approved'),
                    t('Vendor has been approved'),
                    'success'
                );
            },
            onError: () => {
                Swal.fire(
                    t('Error'),
                    t('Failed to approve vendor'),
                    'error'
                );
            }
        });
    }
};

const showRejectForm = async (vendor) => {
    const { value: reason } = await Swal.fire({
        title: t('Reject Vendor'),
        input: 'textarea',
        inputLabel: t('Reason for rejection'),
        inputPlaceholder: t('Please enter the reason...'),
        inputAttributes: {
            'aria-label': t('Type your reason here')
        },
        showCancelButton: true,
        confirmButtonText: t('Reject'),
        cancelButtonText: t('Cancel'),
        inputValidator: (value) => {
            if (!value) {
                return t('You need to provide a reason!');
            }
        }
    });

    if (reason) {
        const confirmResult = await Swal.fire({
            title: t('Confirm Rejection'),
            text: t('Are you sure you want to reject this vendor?'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: t('Yes, reject'),
            cancelButtonText: t('Cancel'),
        });

        if (confirmResult.isConfirmed) {
            router.post(route('vendors.reject', vendor.id), {
                rejection_reason: reason,
                onSuccess: () => {
                    activeDropdown.value = null;
                    Swal.fire(
                        t('Rejected'),
                        t('Vendor has been rejected'),
                        'success'
                    );
                },
                onError: () => {
                    Swal.fire(
                        t('Error'),
                        t('Failed to reject vendor'),
                        'error'
                    );
                }
            });
        }
    }
};
</script>

<style scoped>
.select button {
    background-color: inherit !important;
    border: none !important;
}

.select button:hover {
    background-color: rgb(226, 226, 226) !important;
}
</style>