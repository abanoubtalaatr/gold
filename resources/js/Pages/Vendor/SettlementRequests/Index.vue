<template>
    <AuthenticatedLayout>
        <!-- Page Header -->
        <div class="pagetitle">
            <h1>{{ $t('Settlement Requests') }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link class="nav-link" :href="route('vendor.dashboard')">
                            {{ $t('Home') }}
                        </Link>
                    </li>
                    <li class="breadcrumb-item active">{{ $t('Settlement Requests') }}</li>
                </ol>
            </nav>
        </div>

        <!-- Main Content -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $t('My Settlement Requests') }}</h5>

                            <!-- Empty State -->
                            <div v-if="!requests.data || requests.data.length === 0" class="text-center py-12">
                                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="bi bi-file-earmark-text text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('No Settlement Requests') }}</h3>
                                <p class="text-gray-500">{{ $t('You haven\'t made any settlement requests yet.') }}</p>
                                <Link :href="route('vendor.wallet.index')" 
                                      class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ $t('Go to Wallet') }}
                                </Link>
                            </div>

                            <!-- Requests Table -->
                            <div v-else class="overflow-x-auto">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">{{ $t('Amount') }}</th>
                                            <th scope="col">{{ $t('Status') }}</th>
                                            <th scope="col">{{ $t('Request Date') }}</th>
                                            <th scope="col">{{ $t('Last Updated') }}</th>
                                            <th scope="col">{{ $t('Admin Notes') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(request, index) in requests.data" :key="request.id">
                                            <th scope="row">{{ index + 1 }}</th>
                                            <td>
                                                <span class="fw-bold text-primary">
                                                    {{ formatCurrency(request.amount) }} {{ $t('SAR') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span :class="getStatusBadgeClass(request.status)" 
                                                      class="badge">
                                                    {{ $t(request.status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    {{ formatDateTime(request.created_at) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    {{ formatDateTime(request.updated_at) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div v-if="request.admin_notes" class="d-flex align-items-center">
                                                    <button @click="showNotes(request.admin_notes)" 
                                                            class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye me-1"></i>
                                                        {{ $t('View Notes') }}
                                                    </button>
                                                </div>
                                                <span v-else class="text-muted">{{ $t('No notes') }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Pagination -->
                                <Pagination :links="requests.links" class="mt-4" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Admin Notes Modal -->
        <div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notesModalLabel">{{ $t('Admin Notes') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ currentNotes }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ $t('Close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// Props
const props = defineProps({
    requests: {
        type: Object,
        default: () => ({ data: [], links: [] })
    }
});

// Reactive data
const currentNotes = ref('');

// Methods
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
};

const formatDateTime = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-warning text-dark';
        case 'approved':
            return 'bg-success';
        case 'rejected':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
};

const showNotes = (notes) => {
    currentNotes.value = notes;
    // Using Bootstrap's modal
    const modal = new bootstrap.Modal(document.getElementById('notesModal'));
    modal.show();
};
</script>

<style scoped>
.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.card-title {
    color: #495057;
    font-weight: 600;
}
</style> 