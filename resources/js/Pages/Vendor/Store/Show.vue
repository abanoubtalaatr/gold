<template>

    <Head title="Store Information" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Store Information') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Status alerts -->
                        <div v-if="store.status === 'pending' && user.role === 'vendor'"
                            class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400" />
                                </div>

                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        {{ $t('Your store is pending approval. You will be notified once approved.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-if="store.status === 'rejected'" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <XCircleIcon class="h-5 w-5 text-red-400" />
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        {{ $t('Your store has been rejected. Reason:') }} {{ store.rejection_reason }}
                                    </p>
                                    <div class="mt-2">
                                        <button @click="resubmit"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200">
                                            {{ $t('Resubmit for Approval') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Basic Information') }}</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Name') }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ store.name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Email') }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ store.email }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Mobile')
                                        }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ store.dialling_code }} {{ store.mobile
                                        }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Store Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Store Information') }}</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Store Name(English)')
                                            }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ store.store_name_en }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Store Name(Arabic)')
                                            }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ store.store_name_ar }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Commercial Registration Number') }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ store.commercial_number }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Commercial Registration Image') }}</label>
                                        <div class="mt-1">
                                            <a v-if="store.commercial_image" :href="store.commercial_image"
                                                target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                                {{ $t('View Document') }}
                                            </a>
                                            <span v-else class="text-sm text-gray-500">{{ $t('No file attached')
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Location') }}</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('Address')
                                        }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ store.address?.address || $t('Not specified')
                                            }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('City') }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ store.address?.city || $t('Not specified') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial & Other Info -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Financial Information') }}
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">{{ $t('IBAN') }}</label>
                                        <p class="mt-1 text-sm text-gray-900">{{ store.iban }}</p>
                                    </div>
                                </div>

                                <h3 class="text-lg font-medium text-gray-900 mb-3 mt-4">{{ $t('Store Logo') }}</h3>
                                <div class="mt-1">
                                    <img v-if="store.logo" :src="store.logo"
                                        class="h-32 w-32 rounded-lg object-cover" />
                                    <div v-else
                                        class="h-32 w-32 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-sm">{{ $t('No logo') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Working Hours -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Working Hours') }}</h3>
                                <div class="space-y-2">
                                    <div v-for="(day, index) in store.working_hours" :key="index"
                                        class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">{{ $t(day.day) }}</span>
                                        <span v-if="day.closed" class="text-sm text-gray-500">{{ $t('Closed') }}</span>
                                        <span v-else class="text-sm text-gray-900">{{ day.open }} - {{ day.close
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Working Hours -->
                            <!-- <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('Working Hours') }}</h3>
                                <div class="space-y-2">
                                    <div v-for="(day, index) in store.working_hours" :key="index"
                                        class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">{{ $t(day.day) }}</span>
                                        <span v-if="day.closed" class="text-sm text-gray-500">{{ $t('Closed') }}</span>
                                        <span v-else class="text-sm text-gray-900">{{ day.open }} - {{ day.close
                                        }}</span>
                                    </div>
                                </div>
                            </div> -->
                        </div>

                        <div class="mt-6">
                            <Link :href="route('vendor.store.edit')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            {{ $t('Edit Store Information') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ExclamationTriangleIcon, XCircleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    store: Object
});


const { props: page } = usePage();
const user = page.auth?.user ?? { role: '' };

const resubmit = () => {
    Inertia.post(route('vendor.store.resubmit'));
};

</script>
