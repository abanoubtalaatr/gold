<template>
    <AuthenticatedLayout>
        <!-- Page Header -->
        <div class="pagetitle">
            <h1>{{ $t('Liquidation Requests') }}</h1>
            <nav>
                <ol class="breadcrumb">
                
                    <li class="breadcrumb-item active">{{ $t('Liquidation Requests') }}</li>
                </ol>
            </nav>
        </div>

        <!-- Main Content -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $t('User Liquidation Requests') }}</h5>

                            <!-- Empty State -->
                            <div v-if="!requests.data || requests.data.length === 0" class="text-center py-12">
                                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="bi bi-file-earmark-text text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('No Liquidation Requests') }}</h3>
                                <p class="text-gray-500">{{ $t('No liquidation requests found.') }}</p>
                            </div>

                            <!-- Requests Table -->
                            <div v-else class="overflow-x-auto">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">{{ $t('User') }}</th>
                                            <th scope="col">{{ $t('Amount') }}</th>
                                            <th scope="col">{{ $t('Bank Account') }}</th>
                                            <th scope="col">{{ $t('Status') }}</th>
                                            <th scope="col">{{ $t('Request Date') }}</th>
                                            <th scope="col">{{ $t('Last Updated') }}</th>
                                            <th scope="col">{{ $t('Admin Notes') }}</th>
                                            <th scope="col">{{ $t('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(request, index) in requests.data" :key="request.id">
                                            <th scope="row">{{ index + 1 }}</th>
                                            <td>
                                                <span class="fw-bold text-dark">
                                                    {{ request.user_name }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary">
                                                    {{ formatCurrency(request.amount) }} {{ $t('SAR') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <button @click="showBankDetails(request)" 
                                                            class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-bank me-1"></i>
                                                        {{ $t('View Details') }}
                                                    </button>
                                                </div>
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
                                            <td>
                                                <div v-if="request.status === 'pending'" class="d-flex gap-2">
                                                    <button @click="approveRequest(request.id)" 
                                                            class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        {{ $t('Approve') }}
                                                    </button>
                                                    <button @click="openRejectModal(request)" 
                                                            class="btn btn-sm btn-danger">
                                                        <i class="bi bi-x-circle me-1"></i>
                                                        {{ $t('Reject') }}
                                                    </button>
                                                </div>
                                                <span v-else class="text-muted">
                                                    {{ $t('Processed') }}
                                                </span>
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

        <!-- Bank Details Modal -->
        <div class="modal fade" id="bankDetailsModal" tabindex="-1" aria-labelledby="bankDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bankDetailsModalLabel">{{ $t('Bank Account Details') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="currentBankDetails" class="row">
                            <div class="col-12 mb-3">
                                <strong>{{ $t('Account Holder Name') }}:</strong>
                                <p class="mb-0">{{ currentBankDetails.bank_account_holder_name || $t('Not provided') }}</p>
                            </div>
                            <div class="col-12 mb-3">
                                <strong>{{ $t('Bank Name') }}:</strong>
                                <p class="mb-0">{{ currentBankDetails.bank_account_name || $t('Not provided') }}</p>
                            </div>
                            <div class="col-12 mb-3">
                                <strong>{{ $t('Account Number') }}:</strong>
                                <p class="mb-0">{{ currentBankDetails.bank_account_number || $t('Not provided') }}</p>
                            </div>
                            <div class="col-12 mb-3">
                                <strong>{{ $t('IBAN') }}:</strong>
                                <p class="mb-0">{{ currentBankDetails.bank_account_iban || $t('Not provided') }}</p>
                            </div>
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

        <!-- Reject Request Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">{{ $t('Reject Liquidation Request') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ $t('You are about to reject this liquidation request. Please provide a reason.') }}
                        </div>
                        
                        <div v-if="currentRequest" class="mb-3">
                            <strong>{{ $t('User') }}:</strong> {{ currentRequest.user_name }}<br>
                            <strong>{{ $t('Amount') }}:</strong> {{ formatCurrency(currentRequest.amount) }} {{ $t('SAR') }}
                        </div>

                        <div class="mb-3">
                            <label for="rejectReason" class="form-label">
                                {{ $t('Rejection Reason') }} <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                id="rejectReason"
                                v-model="rejectReason" 
                                class="form-control" 
                                rows="4" 
                                :placeholder="$t('Please provide a detailed reason for rejecting this request...')"
                                required
                            ></textarea>
                            <div v-if="rejectReasonError" class="text-danger small mt-1">
                                {{ rejectReasonError }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ $t('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-danger" @click="confirmReject" :disabled="!rejectReason.trim()">
                            <i class="bi bi-x-circle me-1"></i>
                            {{ $t('Reject Request') }}
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
import { Link, router } from '@inertiajs/vue3';
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
const currentRequest = ref(null);
const currentBankDetails = ref(null);
const rejectReason = ref('');
const rejectReasonError = ref('');

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

const showBankDetails = (request) => {
    currentBankDetails.value = request;
    const modal = new bootstrap.Modal(document.getElementById('bankDetailsModal'));
    modal.show();
};

const showNotes = (notes) => {
    currentNotes.value = notes;
    const modal = new bootstrap.Modal(document.getElementById('notesModal'));
    modal.show();
};

const approveRequest = (requestId) => {
    if (confirm(t('Are you sure you want to approve this liquidation request?'))) {
        router.post(route('admin.settlement-requests-user.approve', requestId), {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Success message will be handled by the backend
            },
            onError: (errors) => {
                console.error('Error approving request:', errors);
            }
        });
    }
};

const openRejectModal = (request) => {
    currentRequest.value = request;
    rejectReason.value = '';
    rejectReasonError.value = '';
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
};

const confirmReject = () => {
    if (!rejectReason.value.trim()) {
        rejectReasonError.value = t('Rejection reason is required');
        return;
    }

    if (rejectReason.value.trim().length < 10) {
        rejectReasonError.value = t('Please provide a more detailed reason (at least 10 characters)');
        return;
    }

    router.put(route('admin.settlement-requests-user.reject', currentRequest.value.id), {
        reason: rejectReason.value.trim()
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
            modal.hide();
            
            // Reset form
            currentRequest.value = null;
            rejectReason.value = '';
            rejectReasonError.value = '';
        },
        onError: (errors) => {
            console.error('Error rejecting request:', errors);
            if (errors.reason) {
                rejectReasonError.value = errors.reason[0];
            }
        }
    });
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

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 0.375rem;
}

.d-flex.gap-2 {
    gap: 0.5rem;
}

.btn-success:hover {
    background-color: #198754;
    border-color: #198754;
}

.btn-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

.modal-body .alert {
    margin-bottom: 1rem;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.text-danger.small {
    font-size: 0.875rem;
}
</style> 