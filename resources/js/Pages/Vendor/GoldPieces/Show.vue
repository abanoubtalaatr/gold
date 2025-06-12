<template>

    <Head :title="goldPiece.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ goldPiece.name }}
                </h2>
                <div class="flex space-x-2">
                    <Link :href="route('admin.gold-pieces.edit', goldPiece.id)"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <PencilIcon class="w-5 h-5 mr-2" />
                    {{ $t('Edit') }}
                    </Link>
                    <button @click="confirmDelete"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <TrashIcon class="w-5 h-5 mr-2" />
                        {{ $t('Delete') }}
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Image Section -->
                        <div class="mb-8">
                            <div class="w-full">
                                <div v-if="goldPiece.images.length > 0">
                                    <!-- Main Image -->
                                    <div class="relative overflow-hidden rounded-lg aspect-square mb-4">
                                        <img :src="currentImageSrc" class="object-cover w-full h-full"
                                            :alt="goldPiece.name" />
                                    </div>

                                    <!-- Thumbnails -->
                                    <div v-if="goldPiece.images.length > 1" class="flex space-x-2 overflow-x-auto">
                                        <div v-for="(image, index) in goldPiece.images" :key="index"
                                            class="overflow-hidden rounded-lg cursor-pointer"
                                            @click="currentImageIndex = index">
                                            <img :src="image.url" class="object-cover w-16 h-16"
                                                :alt="'Thumbnail ' + (index + 1)" />
                                        </div>
                                    </div>
                                </div>
                                <div v-else
                                    class="flex items-center justify-center w-full bg-gray-100 rounded-lg aspect-square">
                                    <CubeIcon class="w-16 h-16 text-gray-400" />
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="space-y-6">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ goldPiece.name }}</h1>
                                <p class="mt-2 text-gray-600">{{ goldPiece.description }}</p>
                            </div>

                            <!-- Info Sections -->
                            <div class="grid gap-6 sm:grid-cols-2">
                                <!-- Basic Info -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <h3 class="mb-3 text-lg font-medium text-gray-900">{{ $t('Basic Information') }}
                                    </h3>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ $t('Weight') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ goldPiece.weight }}g</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ $t('Carat') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ goldPiece.carat }}K</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ $t('Type') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                <span :class="[
                                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                    goldPiece.type === 'for_rent'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : 'bg-green-100 text-green-800'
                                                ]">
                                                    {{ goldPiece.type === 'for_rent' ? $t('For Rent') : $t('For Sale')
                                                    }}
                                                </span>
                                            </dd>
                                        </div>
                                       
                                    </dl>
                                </div>

                                <!-- Pricing Info -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <h3 class="mb-3 text-lg font-medium text-gray-900">{{ $t('Pricing') }}</h3>
                                    <dl class="space-y-3">
                                        <div v-if="goldPiece.type === 'for_rent'">
                                            <dt class="text-sm font-medium text-gray-500">{{ $t('Rental Price Per Day')
                                            }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{
                                                formatCurrency(goldPiece.rental_price_per_day) }}</dd>
                                        </div>
                                        <div v-if="goldPiece.type === 'for_sale'">
                                            <dt class="text-sm font-medium text-gray-500">{{ $t('Sale Price') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{
                                                formatCurrency(goldPiece.sale_price) }}
                                            </dd>
                                        </div>
                                        <div v-if="goldPiece.deposit_amount">
                                            <dt class="text-sm font-medium text-gray-500">{{ $t('Deposit Amount') }}
                                            </dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{
                                                formatCurrency(goldPiece.deposit_amount)
                                            }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Additional Info -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <h3 class="mb-3 text-lg font-medium text-gray-900">{{ $t('Additional Information')
                                    }}</h3>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ $t('Branch') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ goldPiece.branch?.name ||
                                                $t('N/A') }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ $t('Owner') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ goldPiece.user?.name || $t('N/A')
                                            }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">{{ $t('Created At') }}</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ goldPiece.created_at }}</dd>
                                        </div>

                                    </dl>
                                </div>
                            </div>

                            <!-- QR Code Section -->
                            <div v-if="goldPiece.qr_code" class="p-4 bg-gray-50 rounded-lg">
                                <h3 class="mb-3 text-lg font-medium text-gray-900">{{ $t('QR Code') }}</h3>
                                <div class="flex flex-col items-center">
                                    <img :src="goldPiece.qr_code" alt="QR Code" class="w-32 h-32" />
                                    <p class="mt-2 text-sm text-gray-500">{{ $t('Scan this QR code to view this gold piece') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ $t('Are you sure you want to delete this gold piece?') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $t('This action cannot be undone.') }}
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        {{ $t('Cancel') }}
                    </SecondaryButton>
                    <DangerButton class="ml-3" @click="deletePiece" :disabled="form.processing">
                        {{ $t('Delete') }}
                    </DangerButton>
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
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { PencilIcon, TrashIcon, CubeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    goldPiece: {
        type: Object,
        required: true,
    },
});

const confirmingDeletion = ref(false);
const currentImageIndex = ref(0);
const form = useForm({});

const currentImageSrc = computed(() => {
    if (props.goldPiece.images.length > 0) {
        return props.goldPiece.images[currentImageIndex.value].url;
    }
    return '';
});

const statusClass = (status) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'available': return 'bg-green-100 text-green-800';
        case 'rented': return 'bg-blue-100 text-blue-800';
        case 'sold': return 'bg-purple-100 text-purple-800';
        case 'unavailable': return 'bg-gray-100 text-gray-800';
        default: return '';
    }
};

const statusText = (status) => {
    const mapping = {
        pending: 'Pending',
        available: 'Available',
        rented: 'Rented',
        sold: 'Sold',
        unavailable: 'Unavailable',
    };
    return mapping[status] || status;
};

const confirmDelete = () => {
    confirmingDeletion.value = true;
};

const deletePiece = () => {
    form.delete(route('admin.gold-pieces.destroy', props.goldPiece.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingDeletion.value = false;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value || 0);
};
</script>
