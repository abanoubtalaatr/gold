<template>
    <AuthenticatedLayout>
        <div class="pagetitle">
            <h1>{{ $t('Payment Success') }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link class="nav-link" :href="route('vendor.dashboard')">
                            {{ $t('Home') }}
                        </Link>
                    </li>
                    <li class="breadcrumb-item active">{{ $t('Payment Success') }}</li>
                </ol>
            </nav>
        </div>
        

        <section class="section">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                        <h3 class="card-title mt-3">{{ $t('Payment Completed Successfully') }}</h3>
                        <p class="text-muted mb-4">
                            {{ $t('Thank you for your payment. Your transaction has been processed.') }}
                        </p>


                        <!-- Actions -->
                        <div class="d-flex justify-content-center gap-2">
                            <Link :href="route('vendor.dashboard')" class="btn btn-primary">
                                {{ $t('Back to Dashboard') }}
                            </Link>
                            <Link v-if="orderId" :href="route('vendor.orders.show', orderId)" class="btn btn-outline-secondary">
                                {{ $t('View Order Details') }}
                            </Link>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const { t } = useI18n();

const props = defineProps({
    payment: {
        type: Object,
        required: true,
        default: () => ({
            transaction_id: null,
            amount: 0,
            currency: 'SAR',
            created_at: null,
            order_id: null,
        }),
    },
});

const transactionId = computed(() => props.payment.transaction_id || 'N/A');
const amount = computed(() => Number(props.payment.amount).toFixed(2));
const currency = computed(() => props.payment.currency || 'SAR');
const orderId = computed(() => props.payment.order_id || null);
const formattedDate = computed(() => {
    if (!props.payment.created_at) return 'N/A';
    return new Date(props.payment.created_at).toLocaleString();
});
</script>

<style scoped>
.card {
    max-width: 600px;
    margin: 0 auto;
}

.bi-check-circle {
    color: #28a745;
}

.payment-details .row {
    margin-bottom: 0.5rem;
}

.payment-details .col-6 {
    padding: 0 10px;
}
</style>