<template>

    <Head :title="$t('Order Details')" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Order Details') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Order Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $t('Order Information') }}</h3>

                                <div class="space-y-2">
                                    <p><strong class="text-gray-700">{{ $t('Order ID') }}:</strong> {{ order.id }}</p>
                                    <p><strong class="text-gray-700">{{ $t('Order Date') }}:</strong> {{
                                        formatDate(order.created_at) }}</p>
                                    <p><strong class="text-gray-700">{{ $t('Order Type') }}:</strong> {{ type ===
                                        'rental' ?
                                        $t('Rental') : $t('Sale') }}</p>
                                    <p><strong class="text-gray-700">{{ $t('Status') }}:</strong>
                                        <span
                                            :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusGroupClass(order.status).class]">
                                            {{ getStatusGroupClass(order.status).text }}
                                        </span>
                                    </p>
                                    <p v-if="type === 'rental'"><strong class="text-gray-700">{{ $t('Rental Period')
                                    }}:</strong>
                                        {{ formatDate(order.start_date) }} - {{ formatDate(order.end_date) }}
                                    </p>
                                    <!-- <p v-if="type === 'rental'"><strong class="text-gray-700">{{ $t('Rental Days')
                                            }}:</strong>
                                        {{ order.rental_days }}</p> -->
                                    <p><strong class="text-gray-700">{{ $t('Total Price') }}:</strong> {{
                                        order.total_price }}
                                        {{ $t('SAR') }}</p>
                                </div>
                            </div>

                            <!-- User Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $t('User Information') }}</h3>

                                <div class="flex items-start space-x-4">

                                    <div>
                                        <p><strong class="text-gray-700">{{ $t('Name') }}:</strong> {{ order.user?.name
                                            || 'N/A'
                                        }}</p>
                                        <p><strong class="text-gray-700">{{ $t('Email') }}:</strong> {{
                                            order.user?.email ||
                                            'N/A' }}</p>
                                        <p><strong class="text-gray-700">{{ $t('Phone') }}:</strong> {{
                                            order.user?.mobile ||
                                            'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Gold Piece Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $t('Gold Piece Information') }}</h3>

                                <div class="space-y-2">
                                    <p><strong class="text-gray-700">{{ $t('Name') }}:</strong> {{
                                        order.gold_piece?.name ||
                                        'N/A' }}</p>
                                    <p><strong class="text-gray-700">{{ $t('Description') }}:</strong> {{
                                        order.gold_piece?.description || 'N/A' }}</p>
                                    <p><strong class="text-gray-700">{{ $t('Weight') }}:</strong> {{
                                        order.gold_piece?.weight ||
                                        'N/A' }} {{ $t('grams') }}</p>
                                    <!-- <p><strong class="text-gray-700">{{ $t('Purity') }}:</strong> {{
                                        order.gold_piece?.purity ||
                                        'N/A' }}</p> -->
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ $t('Additional Information') }}</h3>

                                <div class="space-y-2">
                                    <p v-if="order.delivery_date"><strong class="text-gray-700">{{ $t('Delivery Date')
                                    }}:</strong> {{ formatDate(order.delivery_date) }}</p>
                                    <p v-if="order.received_date"><strong class="text-gray-700">{{ $t('Received Date')
                                    }}:</strong> {{ formatDate(order.received_date) }}</p>
                                    <p v-if="order.branch"><strong class="text-gray-700">{{ $t('Branch') }}:</strong> {{
                                        order.branch.name }}</p>
                                </div>

                                <div v-if="order.gold_piece?.images?.length" class="mt-4">
                                    <h4 class="text-md font-medium text-gray-900 mb-2">{{ $t('Images') }}</h4>
                                    <div class="flex flex-wrap gap-2">
                                        <img v-for="image in order.gold_piece.images" :key="image.id"
                                            :src="'/storage/' + image.path" class="h-24 w-24 object-cover rounded"
                                            alt="Gold piece image" />
                                    </div>
                                </div>

                                <div v-if="order.gold_piece?.qr_code" class="mt-4">
                                    <h4 class="text-md font-medium text-gray-900 mb-2">{{ $t('QR Code') }}</h4>
                                    <img :src="'data:image/png;base64,' + order.gold_piece.qr_code" class="h-24 w-24"
                                        alt="QR Code" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button @click="goBack"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                {{ $t('Back to Orders') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
    type: {
        type: String,
        required: true,
        validator: (value) => ['rental', 'sale'].includes(value),
    },
});

const getStatusGroupClass = (status) => {
    // Group the statuses as requested
    if (status === 'rented') {
        return {
            class: 'bg-blue-100 text-blue-800',
            text: t('current')
        };
    } else if (['pending_approval', 'approved', 'piece_sent'].includes(status)) {
        return {
            class: 'bg-yellow-100 text-yellow-800',
            text: t('future')
        };
    } else if (['available', 'sold', 'rejected'].includes(status)) {
        return {
            class: 'bg-green-100 text-green-800',
            text: t('finished')
        };
    }

    // Default fallback
    return {
        class: 'bg-gray-100 text-gray-800',
        text: t(status)
    };
};



const formatStatus = (status) => {
    return status
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};

const goBack = () => {
    window.history.back();
};
</script>
