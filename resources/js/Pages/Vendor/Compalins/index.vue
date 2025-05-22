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
                            </div>
                        </div>

                        <!-- Contacts Table -->
                        <div v-if="contacts?.data?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('Date') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ $t('User') }}
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
                                    <tr v-for="contact in contacts.data" :key="contact.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ contact.created_at }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ contact.user?.name || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ contact.subject }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ contact.message.length > 100 ? contact.message.substring(0, 100) + '...'
                                                :
                                                contact.message }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusClass(contact)]">
                                                {{ formatStatus(contact) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="relative inline-block text-left">
                                                <button type="button" @click="toggleContactDropdown(contact.id)"
                                                    class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    :aria-expanded="activeDropdown === contact.id" aria-haspopup="true">
                                                    {{ $t('Actions') }}
                                                    <svg class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>

                                                <transition enter-active-class="transition ease-out duration-100"
                                                    enter-from-class="transform opacity-0 scale-95"
                                                    enter-to-class="transform opacity-100 scale-100"
                                                    leave-active-class="transition ease-in duration-75"
                                                    leave-from-class="transform opacity-100 scale-100"
                                                    leave-to-class="transform opacity-0 scale-95">
                                                    <div v-if="activeDropdown === contact.id"
                                                        class="absolute right-0 z-10 mt-2 w-56 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                                                        role="menu" aria-orientation="vertical"
                                                        :aria-labelledby="'menu-button-' + contact.id">
                                                        <div class="py-1" role="none">
                                                            <button
                                                                @click="showContactDetails(contact); activeDropdown = null"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 menuitem"
                                                                role="menuitem">
                                                                <svg class="w-5 h-5 mr-3 text-blue-500"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                </svg>
                                                                {{ $t('View') }}
                                                            </button>

                                                            <button v-if="!contact.reply"
                                                                @click="openReplyModal(contact); activeDropdown = null"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 menuitem"
                                                                role="menuitem">
                                                                <svg class="w-5 h-5 mr-3 text-green-500"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                                </svg>
                                                                {{ $t('Reply') }}
                                                            </button>

                                                            <button v-if="!contact.read"
                                                                @click="markAsRead(contact.id); activeDropdown = null"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 menuitem"
                                                                role="menuitem">
                                                                <svg class="w-5 h-5 mr-3 text-indigo-500"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                {{ $t('Mark as Read') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </transition>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <Pagination :links="contacts.links" class="mt-6" />
                        </div>
                        <div v-else class="flex flex-col items-center justify-center py-12 text-gray-500">
                            <p class="text-xl font-semibold">{{ $t('No contacts found.') }}</p>
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
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
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
