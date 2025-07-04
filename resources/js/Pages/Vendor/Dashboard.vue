<template>
    <AuthenticatedLayout>
        <!-- Breadcrumb -->
        <div class="pagetitle">
            <h1>{{ $t('Vendor Dashboard') }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link class="nav-link" :href="route('vendor.dashboard')">
                            {{ $t('Home') }}
                        </Link>
                    </li>
                </ol>
            </nav>
        </div>
        <!-- End Breadcrumb -->

        <section class="section dashboard">
            <!-- Debt Payment Button -->
            
            <!-- Filters Section -->
            <div class="row mb-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $t('filters') }}</h5>
                            <form @submit.prevent="applyFilters">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">{{ $t('period') }}</label>
                                        <select class="form-select" v-model="filters.period">
                                            <option value="all">{{ $t('All Time') }}</option>
                                            <option value="daily">{{ $t('daily') }}</option>
                                            <option value="weekly">{{ $t('weekly') }}</option>
                                            <option value="monthly">{{ $t('monthly') }}</option>
                                            <option value="quarterly">{{ $t('quarterly') }}</option>
                                            <option value="semi-annually">{{ $t('semi-annually') }}</option>
                                            <option value="annually">{{ $t('annually') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ $t('from_date') }}</label>
                                        <input type="date" class="form-control" v-model="filters.from_date"
                                            :max="filters.to_date || new Date().toISOString().split('T')[0]">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ $t('to_date') }}</label>
                                        <input type="date" class="form-control" v-model="filters.to_date"
                                            :min="filters.from_date" :max="new Date().toISOString().split('T')[0]">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">{{ $t('Apply') }}</button>
                                        <button type="button" class="btn btn-secondary" @click="resetFilters">{{
                                            $t('reset') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Roles Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Total Roles') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ roles }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admins Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Total Admins') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-lock"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ admins }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Branches Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Total Branches') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-shop"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ branches }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Orders Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Sales Orders') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cart-check"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ salesOrders }}</h6>
                                        </div>
                                    </div>
                                </div>
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
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';

const { t } = useI18n();

const props = defineProps({
    auth: {
        user: {
            name: String,
            email: String,
            phone_number: String,
            id: Number,
        }
    },
    roles: Number,
    admins: Number,
    branches: Number,
    salesOrders: Number,
    rentalOrders: Number,
    rentalRequests: Number,
    rentalStats: {
        completed: Number,
        current: Number,
        upcoming: Number
    },
    piecesStats: {
        available: Number,
        purchased: Number
    },
    ratings: {
        type: Object,
        default: () => ({
            average: 0,
            total: 0,
            breakdown: [0, 0, 0, 0, 0]
        })
    },
    filters: {
        period: String,
        from_date: String,
        to_date: String
    },
    debt: Number,
});

const filters = ref({
    period: props.filters?.period || 'all',
    from_date: props.filters?.from_date || null,
    to_date: props.filters?.to_date || null,
});

const isProcessing = ref(false);

// Rating calculations
const averageRating = computed(() => props.ratings.average || 0);
const totalRatings = computed(() => props.ratings.total || 0);
const ratingDistribution = computed(() => props.ratings.breakdown || []);

const getPercentage = (count) => {
    return totalRatings.value > 0 ? Math.round((count / totalRatings.value) * 100) : 0;
};

const applyFilters = () => {
    router.get(route('vendor.dashboard'), {
        period: filters.value.period,
        from_date: filters.value.from_date || null,
        to_date: filters.value.to_date || null,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetFilters = () => {
    filters.value = {
        period: 'all',
        from_date: null,
        to_date: null,
    };

    router.get(route('vendor.dashboard'), {
        period: 'all',
        from_date: null,
        to_date: null,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const initiatePayment = async () => {
    isProcessing.value = true;
    
        const response = await axios.post(route('vendor.payment.initiate'), {
            amount: props.debt,
        });
        console.log(response.data);
        const checkoutUrl = response.data.checkout_url;
        window.location.href = checkoutUrl; // Redirect to Paymob checkout
   
};
</script>

<style scoped>
.completed-rentals-card .card-icon {
    background-color: #28a74520;
    color: #28a745;
}

.current-rentals-card .card-icon {
    background-color: #17a2b820;
    color: #17a2b8;
}

.upcoming-rentals-card .card-icon {
    background-color: #ffc10720;
    color: #ffc107;
}

.available-pieces-card .card-icon {
    background-color: #6f42c120;
    color: #6f42c1;
}

.purchased-pieces-card .card-icon {
    background-color: #fd7e1420;
    color: #fd7e14;
}

.rating-card .card-icon {
    background-color: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.rating-stars {
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.rating-stars .bi {
    margin-right: 2px;
}
</style>