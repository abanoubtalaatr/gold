<template>

    <Head title="Gold Piece Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Gold Piece Details
                </h2>
                <Link :href="route('vendor.gold-pieces.index')"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <ArrowLeftIcon class="w-5 h-5 mr-2" />
                Back to List
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl">
                <div class=" overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">

                        <!-- ✅ IMAGE SECTION IN FULL ROW -->
                        <div class="mb-8">
                            <div class="w-full">
                                <div v-if="hasImages">
                                    <!-- Main Image -->
                                    <div class="relative overflow-hidden rounded-lg aspect-square mb-4">
                                        <img :src="currentImageSrc" class="object-cover w-full h-full"
                                            alt="Gold piece image" />
                                    </div>

                                    <!-- Thumbnails -->
                                    <div v-if="goldPiece.images.length > 1" class="flex space-x-2 overflow-x-auto">
                                        <div v-for="(image, index) in goldPiece.images" :key="index"
                                            class="overflow-hidden rounded-lg">
                                            <img :src="fullImagePath(image.path)"
                                                class="object-cover w-16 h-16 cursor-pointer"
                                                @click="currentImageIndex = index" alt="Thumbnail" />
                                        </div>
                                    </div>
                                </div>
                                <div v-else
                                    class="flex items-center justify-center w-full bg-gray-100 rounded-lg aspect-square">
                                    <CubeIcon class="w-16 h-16 text-gray-400" />
                                </div>
                            </div>
                        </div>

                        <!-- ✅ DETAILS SECTION BELOW IMAGE -->
                        <div class="space-y-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ goldPiece.name }}</h1>
                                <p class="mt-2 text-gray-600">{{ goldPiece.description }}</p>
                            </div>

                            <!-- Info Sections -->
                            <div class="grid gap-6 sm:grid-cols-2">
                                <!-- Basic Info -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <h3 class="mb-3 text-lg font-medium text-gray-900">Basic Information</h3>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Weight</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ goldPiece.weight }}g</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Carat</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ goldPiece.carat }}K</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                <span :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    goldPiece.type === 'for_rent'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : 'bg-green-100 text-green-800'
                                                ]">
                                                    {{ goldPiece.type === 'for_rent' ? 'For Rent' : 'For Sale' }}
                                                </span>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                <span :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    statusClass(goldPiece.status)
                                                ]">
                                                    {{ statusText(goldPiece.status) }}
                                                </span>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Pricing Info -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <h3 class="mb-3 text-lg font-medium text-gray-900">Pricing</h3>
                                    <dl class="space-y-3">
                                        <div v-if="goldPiece.type === 'for_rent'">
                                            <dt class="text-sm font-medium text-gray-500">Rental Price Per Day</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{
                                                formatCurrency(goldPiece.rental_price_per_day) }}</dd>
                                        </div>
                                        <div v-if="goldPiece.type === 'for_sale'">
                                            <dt class="text-sm font-medium text-gray-500">Sale Price</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{
                                                formatCurrency(goldPiece.sale_price) }}
                                            </dd>
                                        </div>
                                        <div v-if="goldPiece.deposit_amount">
                                            <dt class="text-sm font-medium text-gray-500">Deposit Amount</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{
                                                formatCurrency(goldPiece.deposit_amount)
                                                }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Additional Info -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <h3 class="mb-3 text-lg font-medium text-gray-900">Additional Information</h3>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Branch</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ goldPiece.branch?.name || 'N/A' }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ goldPiece.created_at }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ goldPiece.updated_at }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex mt-6 space-x-3">
                                <Link :href="route('vendor.gold-pieces.edit', goldPiece.id)"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <PencilIcon class="w-5 h-5 mr-2" />
                                Edit
                                </Link>
                                <button @click="confirmDelete"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <TrashIcon class="w-5 h-5 mr-2" />
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ DELETE MODAL - STYLED ALERT -->
        <Modal  :show="confirmingDeletion" @close="closeModal">
            <div class="p-6 bg-red-50 rounded-lg">
                <div class="flex items-center mb-4">
                    <TrashIcon class="w-6 h-6 text-red-600 mr-2" />
                    <h2 class="text-lg font-bold text-red-800">Delete Confirmation</h2>
                </div>
                <p class="text-sm text-red-700">Are you sure you want to delete this gold piece? This action
                    <strong>cannot be
                        undone</strong>.
                </p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        @click="closeModal">
                        Cancel
                    </button>
                    <button type="button"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        @click="deletePiece">
                        Yes, Delete It
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import Swal from 'sweetalert2';

import {
    ArrowLeftIcon,
    CubeIcon,
    PencilIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    goldPiece: {
        type: Object,
        required: true,
    },
});

const confirmingDeletion = ref(false);
const form = useForm({});

const hasImages = computed(() => props.goldPiece.images && props.goldPiece.images.length > 0);
const currentImageIndex = ref(0);
const currentImageSrc = computed(() => {
    if (hasImages.value) {
        return fullImagePath(props.goldPiece.images[currentImageIndex.value].path);
    }
    return '';
});

const fullImagePath = (path) => `/storage/${path}`;

const statusClass = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'available': return 'bg-green-100 text-green-800';
        case 'rented': return 'bg-blue-100 text-blue-800';
        case 'sold': return 'bg-purple-100 text-purple-800';
        case 'accepted': return 'bg-gray-100 text-gray-800';
        default: return '';
    }
};

const statusText = (status) => {
    const mapping = {
        pending: 'Pending',
        available: 'Available',
        rented: 'Rented',
        sold: 'Sold',
        accepted: 'Accepted',
    };
    return mapping[status] || status;
};

const confirmDelete = () => {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280', // Tailwind gray-500
        confirmButtonText: 'Yes, delete it!',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            deletePiece();
        }
    });
};

const closeModal = () => {
    confirmingDeletion.value = false;
};
const deletePiece = () => {
    form.delete(route('vendor.gold-pieces.destroy', props.goldPiece.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value || 0);
};
</script>
