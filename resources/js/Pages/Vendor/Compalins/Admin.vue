<template>

    <Head title="Contacts Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('Contacts Management') }}
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
                                    :placeholder="$t('Search by name, email...')" @input="debouncedSearch" />
                            </div>

                            <div class="flex items-center gap-4">
                                <select v-model="form.type"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    @change="applyFilters">
                                    <option value="">{{ $t('All Types') }}</option>
                                    <option value="general">{{ $t('General Inquiry') }}</option>
                                    <option value="rental">{{ $t('Rental Order') }}</option>
                                    <option value="sale">{{ $t('Sale Order') }}</option>
                                </select>
                                <select v-model="form.status"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    @change="applyFilters">
                                    <option value="">{{ $t('All Statuses') }}</option>
                                    <option value="read">{{ $t('Read') }}</option>
                                    <option value="unread">{{ $t('Unread') }}</option>
                                    <option value="replied">{{ $t('Replied') }}</option>
                                </select>
                                <button @click="resetFilters"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    {{ $t('Reset') }}
                                </button>
                                <Link :href="route('vendor.contacts.create')"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                {{ $t('Contact Admin') }}
                                </Link>
                            </div>
                        </div>
                        <!-- Vendor Messages Table -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Your Messages to Admin') }}</h3>
                            <div v-if="vendorMessages?.data?.length > 0" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ $t('Date') }}
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
                                                {{ $t('Actions') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="message in vendorMessages.data" :key="message.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ message.created_at }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ message.subject }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ message.message.length > 100 ? message.message.substring(0, 100) +
                                                    '...' :
                                                    message.message }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getVendorMessageStatusClass(message)]">
                                                    {{ formatVendorMessageStatus(message) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button @click="showVendorMessageDetails(message)"
                                                    class="px-3 py-1 text-sm text-white bg-yellow-500 rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    {{ $t('View') }}
                                                </button>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <Pagination :links="vendorMessages.links" class="mt-6" />
                            </div>
                            <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                                <p class="text-xl font-semibold">{{ $t('No messages sent to admin yet.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Details Modal -->
        <Modal :show="showDetailsModal" @close="closeDetailsModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div
                    class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Contact Details') }}</h2>
                        <div v-if="selectedContact" class="mt-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p><strong>{{ $t('Name') }}:</strong> {{ selectedContact.user?.name }}</p>
                                    <p><strong>{{ $t('Email') }}:</strong> {{ selectedContact.user?.email }}</p>
                                    <p v-if="selectedContact.vendor"><strong>{{ $t('vendor') }}:</strong> {{
                                        selectedContact.vendor }}</p>
                                    <p><strong>{{ $t('Date') }}:</strong> {{ selectedContact.created_at }}</p>
                                </div>
                                <div>
                                    <p><strong>{{ $t('Subject') }}:</strong> {{ selectedContact.subject }}</p>
                                    <p><strong>{{ $t('Type') }}:</strong> {{ formatType(selectedContact) }}</p>
                                    <p><strong>{{ $t('Status') }}:</strong> {{ formatStatus(selectedContact) }}</p>
                                    <p v-if="selectedContact.user"><strong>{{ $t('User') }}:</strong> {{
                                        selectedContact.user.name }}</p>
                                </div>
                            </div>

                            <div>
                                <p><strong>{{ $t('Message') }}:</strong></p>
                                <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                    {{ selectedContact.message }}
                                </div>
                            </div>

                            <div v-if="selectedContact.reply">
                                <p><strong>{{ $t('Your Reply') }}:</strong></p>
                                <div class="mt-2 p-3 bg-blue-50 rounded-lg">
                                    {{ selectedContact.reply }}
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $t('Replied at') }}: {{ selectedContact.updated_at }}
                                </p>
                            </div>

                            <div v-if="selectedContact.rentalOrder">
                                <p><strong>{{ $t('Related Rental Order') }} (ID: {{ selectedContact.rentalOrder.id
                                        }})</strong>
                                </p>
                                <p>{{ $t('Status') }}: {{ formatStatus(selectedContact.rentalOrder) }}</p>
                            </div>

                            <div v-if="selectedContact.saleOrder">
                                <p><strong>{{ $t('Related Sale Order') }} (ID: {{ selectedContact.saleOrder.id
                                        }})</strong></p>
                                <p>{{ $t('Status') }}: {{ formatStatus(selectedContact.saleOrder) }}</p>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3">
                                <button v-if="!selectedContact.read" @click="markAsRead(selectedContact.id)"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors duration-200">
                                    {{ $t('Mark as Read') }}
                                </button>
                                <button v-if="!selectedContact.reply" @click="openReplyModal(selectedContact)"
                                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors duration-200">
                                    {{ $t('Reply') }}
                                </button>
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
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-lg w-full">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Reply to Contact') }}</h2>
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

        <!-- Vendor Message Details Modal -->
        <Modal :show="showVendorMessageModal" @close="closeVendorMessageModal">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div
                    class="bg-white rounded-lg shadow-xl transform transition-all sm:max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ $t('Message Details') }}</h2>
                        <div v-if="selectedVendorMessage" class="mt-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p><strong>{{ $t('Date') }}:</strong> {{ selectedVendorMessage.created_at }}</p>
                                    <p><strong>{{ $t('Status') }}:</strong> {{
                                        formatVendorMessageStatus(selectedVendorMessage)
                                    }}</p>
                                </div>
                                <div>
                                    <p><strong>{{ $t('Subject') }}:</strong> {{ selectedVendorMessage.subject }}</p>
                                </div>
                            </div>

                            <div>
                                <p><strong>{{ $t('Your Message') }}:</strong></p>
                                <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                    {{ selectedVendorMessage.message }}
                                </div>
                            </div>

                            <div v-if="selectedVendorMessage.reply">
                                <p><strong>{{ $t('Admin Reply') }}:</strong></p>
                                <div class="mt-2 p-3 bg-blue-50 rounded-lg">
                                    {{ selectedVendorMessage.reply }}
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $t('Replied at') }}: {{ selectedVendorMessage.read_at }}
                                </p>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button @click="closeVendorMessageModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors duration-200">
                                    {{ $t('Close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import debounce from 'lodash/debounce';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({
    contacts: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    vendorMessages: {
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
    type: props.filters.type || '',
    status: props.filters.status || '',
});

const showDetailsModal = ref(false);
const selectedContact = ref(null);
const showReplyModal = ref(false);
const showVendorMessageModal = ref(false);
const selectedVendorMessage = ref(null);
const replyForm = useForm({
    id: null,
    name: '',
    email: '',
    subject: '',
    message: '',
});

const debouncedSearch = debounce(() => {
    form.get(route('vendor.contacts.index'), {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);

const applyFilters = () => {
    form.get(route('vendor.contacts.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.type = '';
    form.status = '';

    router.get(route('vendor.contacts.index'), {}, {
        preserveState: false,
        preserveScroll: true,
        replace: true
    });
};

const getStatusClass = (contact) => {
    if (contact.reply) return 'bg-green-100 text-green-800';
    if (!contact.read) return 'bg-yellow-100 text-yellow-800';
    return 'bg-gray-100 text-gray-800';
};

const formatStatus = (contact) => {
    if (contact.reply) return t('Replied');
    if (!contact.read) return t('Unread');
    return t('Read');
};

const getTypeClass = (contact) => {
    if (contact.rental_order_id) return 'bg-purple-100 text-purple-800';
    if (contact.sale_order_id) return 'bg-blue-100 text-blue-800';
    return 'bg-gray-100 text-gray-800';
};

const formatType = (contact) => {
    if (contact.rental_order_id) return t('Rental Order');
    if (contact.sale_order_id) return t('Sale Order');
    return t('General Inquiry');
};

const showContactDetails = (contact) => {
    selectedContact.value = contact;
    showDetailsModal.value = true;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
    selectedContact.value = null;
};

const openReplyModal = (contact) => {
    selectedContact.value = contact;
    replyForm.id = contact.id;
    replyForm.name = contact.name;
    replyForm.email = contact.email;
    replyForm.subject = `Re: ${contact.subject}`;
    replyForm.message = '';
    showReplyModal.value = true;
};

const closeReplyModal = () => {
    showReplyModal.value = false;
    replyForm.reset();
};

const sendReply = () => {
    replyForm.post(route('vendor.contacts.reply', replyForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeReplyModal();
            if (selectedContact.value) {
                selectedContact.value.reply = replyForm.message;
                selectedContact.value.read = true;
            }
        },
    });
};

const markAsRead = (contactId) => {
    router.patch(route('vendor.contacts.markAsRead', contactId), {
        preserveScroll: true,
        onSuccess: () => {
            if (selectedContact.value) {
                selectedContact.value.read = true;
            }
            // Update the contact in the list if needed
            const contact = props.contacts.data.find(c => c.id === contactId);
            if (contact) {
                contact.read = true;
            }
        },
    });
};

// Close modals when clicking outside
const onClickOutside = (event) => {
    if (!event.target.closest('.bg-white')) {
        closeDetailsModal();
        closeReplyModal();
    }
};

onMounted(() => {
    document.addEventListener('click', onClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', onClickOutside);
});

const activeDropdown = ref(null);

const toggleContactDropdown = (contactId) => {
    if (activeDropdown.value === contactId) {
        activeDropdown.value = null;
    } else {
        activeDropdown.value = contactId;
    }
};

onMounted(() => {
    document.addEventListener('click', onClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', onClickOutside);
});



const getVendorMessageStatusClass = (message) => {
    if (message.reply) return 'bg-green-100 text-green-800';
    if (message.read) return 'bg-blue-100 text-blue-800';
    return 'bg-gray-100 text-gray-800';
};

const formatVendorMessageStatus = (message) => {
    if (message.reply) return t('Replied');
    if (message.read) return t('Read by Admin');
    return t('Unread by Admin');
};

const showVendorMessageDetails = (message) => {
    selectedVendorMessage.value = message;
    showVendorMessageModal.value = true;
};

const showVendorMessageReply = (message) => {
    selectedVendorMessage.value = message;
    showVendorMessageModal.value = true;
};

const closeVendorMessageModal = () => {
    showVendorMessageModal.value = false;
    selectedVendorMessage.value = null;
};
</script>

<style>
.modal-overlay {
    display: none !important;
}

.dropdown-enter-active {
    transition: opacity 0.1s ease-out, transform 0.1s ease-out !important;
}

.dropdown-leave-active {
    transition: opacity 0.075s ease-in, transform 0.075s ease-in !important;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0 !important;
    transform: scale(0.95) !important;
}

.menuitem {
    background-color: transparent !important;
    border: none !important;
}
</style>