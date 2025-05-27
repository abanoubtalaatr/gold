<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Vendor Details
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-medium">{{ vendor.store_name_en }}</h3>
                                <p class="text-gray-600">{{ vendor.store_name_ar }}</p>
                            </div>
                            <div class="flex space-x-2 ">
                                <Link :href="route('vendors.edit', vendor.id)"
                                    class="px-4 py-2 text-black bg-slate-400 edit-button">
                                Edit
                                </Link>
                                <button @click="toggleStatus" :class="{
                                    'bg-red-600 hover:bg-red-700': vendor.is_active,
                                    'bg-green-600 hover:bg-green-700': !vendor.is_active,
                                }" class="px-4 py-2 text-white rounded-md">
                                    {{ vendor.is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h4 class="mb-4 text-lg font-medium">Vendor Information</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Name</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ vendor.name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ vendor.email }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            {{ vendor.dialling_code }} {{ vendor.mobile }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            <span :class="{
                                                'bg-green-100 text-green-800': vendor.vendor_status === 'approved',
                                                'bg-yellow-100 text-yellow-800': vendor.vendor_status === 'pending',
                                                'bg-red-100 text-red-800': vendor.vendor_status === 'rejected',
                                            }" class="inline-flex px-2 text-xs font-semibold leading-5 rounded-full">
                                                {{ vendor.vendor_status }}
                                            </span>
                                            <span v-if="!vendor.is_active"
                                                class="inline-flex px-2 ml-2 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">
                                                Inactive
                                            </span>
                                        </p>
                                    </div>
                                    <div v-if="vendor.vendor_status === 'rejected' && vendor.rejection_reason">
                                        <label class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ vendor.rejection_reason }}</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="mb-4 text-lg font-medium">Store Information</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Commercial
                                            Registration</label>
                                        <p class="mt-1 text-sm text-gray-900">
                                            {{ vendor.commercial_registration_number }}
                                        </p>
                                        <a v-if="vendor.media?.find(m => m.collection_name === 'commercial_registration')"
                                            :href="vendor.media.find(m => m.collection_name === 'commercial_registration').original_url"
                                            target="_blank"
                                            class="mt-2 inline-block text-sm text-indigo-600 hover:text-indigo-900">
                                            View Document
                                        </a>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">IBAN</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ vendor.iban }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Location</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ vendor.city?.name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Logo</label>
                                        <img v-if="vendor.media?.find(m => m.collection_name === 'logo')"
                                            :src="vendor.media.find(m => m.collection_name === 'logo').original_url"
                                            class="mt-2 h-20 w-20 rounded-full object-cover" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="vendor.vendor_status === 'pending'" class="mt-8">
                            <h4 class="mb-4 text-lg font-medium">Review Application</h4>
                            <div class="flex space-x-4">
                                <button @click="approveVendor"
                                    class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700">
                                    Approve
                                </button>
                                <button @click="showRejectForm = true"
                                    class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700">
                                    Reject
                                </button>
                            </div>

                            <div v-if="showRejectForm" class="mt-4 p-4 bg-gray-50 rounded-md">
                                <form @submit.prevent="rejectVendor">
                                    <div class="mb-4">
                                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">
                                            Rejection Reason
                                        </label>
                                        <textarea id="rejection_reason" v-model="rejectionReason" rows="3"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required></textarea>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="button" @click="showRejectForm = false"
                                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700">
                                            Submit Rejection
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    vendor: Object,
});

const showRejectForm = ref(false);
const rejectionReason = ref('');

const toggleStatus = () => {
    if (confirm('Are you sure you want to change this vendor\'s status?')) {
        router.patch(route('vendors.toggle-status', props.vendor.id));
    }
};

const approveVendor = () => {
    if (confirm('Are you sure you want to approve this vendor?')) {
        router.post(route('vendors.approve', props.vendor.id));
    }
};

const rejectVendor = () => {
    if (confirm('Are you sure you want to reject this vendor?')) {
        router.post(route('vendors.reject', props.vendor.id), {
            rejection_reason: rejectionReason.value,
        });
    }
};
</script>

<style scoped>
button {
    height: 40px;
    margin-left: 5px;
}

.edit-button {
    height: 40px;
    width: 100px;
    background-color: rgb(226, 226, 226) !important;
    border: 1px solid rgb(0, 0, 0) !important;
    border-radius: 5px;
    text-align: center;
}
</style>