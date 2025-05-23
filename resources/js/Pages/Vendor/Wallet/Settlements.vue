<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Settlement Requests') }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            #
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Vendor') }}
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Amount') }}
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Request Date') }}
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Status') }}
                                        </th>
                                        <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                            {{ $t('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(request, index) in requests.data" :key="request.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ request.user }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ request.amount }} {{ $t('SAR') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(request.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                request.status === 'approved' ? 'bg-green-100 text-green-800' :
                                                request.status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'
                                            ]">
                                                {{ $t(request.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div v-if="request.status === 'pending'" class="flex space-x-2">
                                                <button @click="processRequest(request.id, 'approved')"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                                    {{ $t('Approve') }}
                                                </button>
                                                <button @click="processRequest(request.id, 'rejected')"
                                                    class="px-3 py-1 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                                    {{ $t('Reject') }}
                                                </button>
                                            </div>
                                            <span v-else class="text-gray-500">{{ $t('Processed') }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="requests.links" class="mt-4" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Process Request Modal -->
        <Modal :show="showProcessModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $t('Process Settlement Request') }}
                </h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $t('Notes') }} ({{ $t('Optional') }})
                    </label>
                    <textarea v-model="processForm.notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button @click="closeModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        {{ $t('Cancel') }}
                    </button>
                    <button @click="submitProcessRequest"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        {{ $t('Confirm') }}
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({
    requests: Object,
});

const showProcessModal = ref(false);
const currentRequestId = ref(null);
const processStatus = ref('');

const processForm = useForm({
    notes: '',
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};

const processRequest = (id, status) => {
    currentRequestId.value = id;
    processStatus.value = status;
    showProcessModal.value = true;
};

const closeModal = () => {
    showProcessModal.value = false;
    processForm.reset();
};

const submitProcessRequest = () => {
    processForm.put(route('admin.settlements.process', currentRequestId.value), {
        preserveScroll: true,
        data: {
            status: processStatus.value,
            notes: processForm.notes,
        },
        onSuccess: () => {
            closeModal();
        },
    });
};
</script>
