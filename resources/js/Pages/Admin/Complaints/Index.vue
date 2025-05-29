<template>

    <Head title="Complaints Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Complaints Management') }}
            </h2>
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
                                    :placeholder="$t('Search by name, subject...')" @input="debouncedSearch" />
                            </div>
                            <div class="flex items-center gap-4">
                                <select v-model="form.status"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    @change="applyFilters">
                                    <option value="">{{ $t('All Statuses') }}</option>
                                    <option value="new">{{ $t('New') }}</option>
                                    <option value="in_progress">{{ $t('In Progress') }}</option>
                                    <option value="resolved">{{ $t('Resolved') }}</option>
                                </select>
                                <button @click="resetFilters"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    {{ $t('Reset') }}
                                </button>
                            </div>
                        </div>

                        <!-- Complaints Table -->
                        <div v-if="complaints?.data?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Date') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Complainant') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Subject') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Message Preview') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Status') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Related Order') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="complaint in complaints.data" :key="complaint.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(complaint.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div v-if="complaint.user">
                                                {{ complaint.user.name }} ({{ $t('User') }})
                                            </div>
                                            <div v-else-if="complaint.vendor">
                                                {{ complaint.vendor.name }} ({{ $t('Vendor') }})
                                            </div>
                                            <div v-else>
                                                {{ complaint.name || 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ complaint.subject }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ complaint.message.length > 50 ? complaint.message.substring(0, 50) +
                                                '...' :
                                                complaint.message }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(complaint)]">
                                                {{ formatStatus(complaint) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div v-if="complaint.rental_order">
                                                <p><strong>{{ $t('Rental Order') }} #{{ complaint.rental_order.id
                                                }}</strong>
                                                </p>
                                                <p v-if="complaint.rental_order.branch">{{ $t('Branch') }}: {{
                                                    complaint.rental_order.branch.name }}</p>
                                                <p v-if="complaint.rental_order.branch?.vendor">{{ $t('Vendor') }}: {{
                                                    complaint.rental_order.branch.vendor.name }}</p>
                                            </div>
                                            <div v-else-if="complaint.sale_order">
                                                <p><strong>{{ $t('Sale Order') }} #{{ complaint.sale_order.id
                                                }}</strong></p>
                                                <p v-if="complaint.sale_order.branch">{{ $t('Branch') }}: {{
                                                    complaint.sale_order.branch.name }}</p>
                                                <p v-if="complaint.sale_order.branch?.vendor">{{ $t('Vendor') }}: {{
                                                    complaint.sale_order.branch.vendor.name }}</p>
                                            </div>
                                            <div v-else>
                                                N/A
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="relative inline-block text-left dropdown selesct">
                                                <button type="button"
                                                    class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                    @click="toggleDropdown(complaint.id)">
                                                    {{ $t('Actions') }}
                                                    <svg class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>

                                                <div v-show="activeDropdown === complaint.id"
                                                    class="absolute right-0 z-10 w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                                    <div class="py-1">
                                                        <button @click="showComplaintDetails(complaint)"
                                                            class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                            {{ $t('View Details') }}
                                                        </button>
                                                        <button @click="openStatusModal(complaint)"
                                                            class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                            {{ $t('Update Status') }}
                                                        </button>
                                                        <button v-if="!complaint.reply"
                                                            @click="openReplyModal(complaint)"
                                                            class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                            {{ $t('Reply') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <Pagination :links="complaints.links" class="mt-6" />
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <p class="text-xl font-semibold">{{ $t('No complaints found.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaint Details Modal -->
        <Modal :show="showDetailsModal" @close="closeDetailsModal">
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Complaint Details') }}</h2>
                        <div v-if="selectedComplaint" class="mt-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p><strong>{{ $t('Complainant') }}:</strong>
                                        <span v-if="selectedComplaint.user">
                                            {{ selectedComplaint.user.name }} ({{ $t('User') }})
                                        </span>
                                        <span v-else-if="selectedComplaint.vendor">
                                            {{ selectedComplaint.vendor.name }} ({{ $t('Vendor') }})
                                        </span>
                                        <span v-else>
                                            {{ selectedComplaint.name || 'N/A' }}
                                        </span>
                                    </p>
                                    <p><strong>{{ $t('Email') }}:</strong> {{ selectedComplaint.email || 'N/A' }}</p>
                                    <p><strong>{{ $t('Phone') }}:</strong> {{ selectedComplaint.phone || 'N/A' }}</p>
                                </div>
                                <div>
                                    <p><strong>{{ $t('Date') }}:</strong> {{ formatDate(selectedComplaint.created_at) }}
                                    </p>
                                    <p><strong>{{ $t('Status') }}:</strong>
                                        <span :class="getStatusClass(selectedComplaint)">
                                            {{ formatStatus(selectedComplaint) }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div v-if="selectedComplaint.rental_order || selectedComplaint.sale_order"
                                class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-medium mb-2">{{ $t('Related Order Information') }}</h3>
                                <div v-if="selectedComplaint.rental_order">
                                    <p><strong>{{ $t('Order Type') }}:</strong> {{ $t('Rental') }}</p>
                                    <p><strong>{{ $t('Order ID') }}:</strong> #{{ selectedComplaint.rental_order.id }}
                                    </p>
                                    <p v-if="selectedComplaint.rental_order.branch"><strong>{{ $t('Branch') }}:</strong>
                                        {{
                                            selectedComplaint.rental_order.branch.name }}</p>
                                    <p v-if="selectedComplaint.rental_order.branch?.vendor"><strong>{{ $t('Vendor')
                                    }}:</strong>
                                        {{ selectedComplaint.rental_order.branch.vendor.name }}</p>
                                </div>
                                <div v-else-if="selectedComplaint.sale_order">
                                    <p><strong>{{ $t('Order Type') }}:</strong> {{ $t('Sale') }}</p>
                                    <p><strong>{{ $t('Order ID') }}:</strong> #{{ selectedComplaint.sale_order.id }}</p>
                                    <p v-if="selectedComplaint.sale_order.branch"><strong>{{ $t('Branch') }}:</strong>
                                        {{
                                            selectedComplaint.sale_order.branch.name }}</p>
                                    <p v-if="selectedComplaint.sale_order.branch?.vendor"><strong>{{ $t('Vendor')
                                    }}:</strong>
                                        {{ selectedComplaint.sale_order.branch.vendor.name }}</p>
                                </div>
                            </div>

                            <div>
                                <p><strong>{{ $t('Subject') }}:</strong></p>
                                <p class="mt-1 font-medium">{{ selectedComplaint.subject }}</p>
                            </div>

                            <div>
                                <p><strong>{{ $t('Message') }}:</strong></p>
                                <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                    {{ selectedComplaint.message }}
                                </div>
                            </div>

                            <div v-if="selectedComplaint.reply">
                                <p><strong>{{ $t('Admin Reply') }}:</strong></p>
                                <div class="mt-2 p-3 bg-blue-50 rounded-lg">
                                    {{ selectedComplaint.reply }}
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $t('Replied at') }}: {{ formatDate(selectedComplaint.updated_at) }}
                                </p>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <button @click="closeDetailsModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200">
                                    {{ $t('Close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Reply Modal -->
        <Modal :show="showReplyModal" @close="closeReplyModal">
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Reply to Complaint') }}</h2>
                        <p class="mb-2"><strong>{{ $t('From') }}:</strong> {{ replyForm.name }} &lt;{{ replyForm.email
                        }}&gt;
                        </p>
                        <p class="mb-4"><strong>{{ $t('Subject') }}:</strong> {{ replyForm.subject }}</p>

                        <form @submit.prevent="sendReply">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Your Reply')
                                }}</label>
                                <textarea v-model="replyForm.message" rows="5"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    required></textarea>
                                <p v-if="replyForm.errors.message" class="mt-1 text-sm text-red-600">
                                    {{ replyForm.errors.message }}
                                </p>
                            </div>

                            <div class="flex justify-end space-x-3 pt-4">
                                <button type="button" @click="closeReplyModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200">
                                    {{ $t('Cancel') }}
                                </button>
                                <button type="submit" :disabled="replyForm.processing"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                    {{ replyForm.processing ? $t('Sending...') : $t('Send Reply') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Status Update Modal -->
        <Modal :show="showStatusModal" @close="closeStatusModal">
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Update Complaint Status') }}</h2>

                        <form @submit.prevent="updateStatus">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('Current Status')
                                }}</label>
                                <p>{{ formatStatus(selectedComplaint) }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('New Status')
                                }}</label>
                                <select v-model="statusForm.status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                    <option value="new">{{ $t('New') }}</option>
                                    <option value="in_progress">{{ $t('In Progress') }}</option>
                                    <option value="resolved">{{ $t('Resolved') }}</option>
                                </select>
                            </div>

                            <div class="flex justify-end space-x-3 pt-4">
                                <button type="button" @click="closeStatusModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200">
                                    {{ $t('Cancel') }}
                                </button>
                                <button type="submit" :disabled="statusForm.processing"
                                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                    {{ statusForm.processing ? $t('Updating...') : $t('Update Status') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({
    complaints: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
});

const showDetailsModal = ref(false);
const selectedComplaint = ref(null);
const showReplyModal = ref(false);
const showStatusModal = ref(false);
const activeDropdown = ref(null);

const replyForm = useForm({
    id: null,
    name: '',
    email: '',
    subject: '',
    message: '',
});

const statusForm = useForm({
    id: null,
    status: '',
});

const toggleDropdown = (id) => {
    activeDropdown.value = activeDropdown.value === id ? null : id;
};

const closeAllDropdowns = () => {
    activeDropdown.value = null;
};

const debouncedSearch = debounce(() => {
    form.get(route('admin.complaints.index'), {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);

const applyFilters = () => {
    form.get(route('admin.complaints.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.status = '';

    router.get(route('admin.complaints.index'), {}, {
        preserveState: false,
        preserveScroll: true,
        replace: true
    });
};

const getStatusClass = (complaint) => {
    switch (complaint.status) {
        case 'new': return 'bg-yellow-100 text-yellow-800';
        case 'in_progress': return 'bg-blue-100 text-blue-800';
        case 'resolved': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const formatStatus = (complaint) => {
    switch (complaint.status) {
        case 'new': return t('New');
        case 'in_progress': return t('In Progress');
        case 'resolved': return t('Resolved');
        default: return t('Unknown');
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
};

const showComplaintDetails = (complaint) => {
    selectedComplaint.value = complaint;
    showDetailsModal.value = true;
    closeAllDropdowns();
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedComplaint.value = null;
};

const openReplyModal = (complaint) => {
    selectedComplaint.value = complaint;
    replyForm.id = complaint.id;
    replyForm.name = complaint.user?.name || complaint.name || 'N/A';
    replyForm.email = complaint.user?.email || complaint.email || 'N/A';
    replyForm.subject = `Re: ${complaint.subject}`;
    replyForm.message = '';
    showReplyModal.value = true;
    closeAllDropdowns();
};

const closeReplyModal = () => {
    showReplyModal.value = false;
    replyForm.reset();
};

const sendReply = () => {
    replyForm.post(route('admin.complaints.reply', replyForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeReplyModal();
            if (selectedComplaint.value) {
                selectedComplaint.value.reply = replyForm.message;
            }
        },
    });
};

const openStatusModal = (complaint) => {
    selectedComplaint.value = complaint;
    statusForm.id = complaint.id;
    statusForm.status = complaint.status;
    showStatusModal.value = true;
    closeAllDropdowns();
};

const closeStatusModal = () => {
    showStatusModal.value = false;
    statusForm.reset();
};

const updateStatus = () => {
    statusForm.patch(route('admin.complaints.update-status', statusForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeStatusModal();
            if (selectedComplaint.value) {
                selectedComplaint.value.status = statusForm.status;
            }
        },
    });
};

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown')) {
        closeAllDropdowns();
    }
});
</script>

<style scoped>
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    z-index: 1;
}

.dropdown:hover .dropdown-content {
    display: block;
}

.dropdown button {
    display: flex;
    align-items: center;
    background-color: transparent !important;
    border: none !important;
}

.dropdown button:hover {
    background-color: rgb(242, 241, 241) !important;
    color: black !important;
}
</style>
