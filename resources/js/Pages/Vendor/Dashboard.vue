<template>
    <AuthenticatedLayout>
        <!-- breadcrumb-->
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
        <!-- End breadcrumb-->
        <section class="section dashboard">
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
                                        <button type="submit" class="btn btn-primary me-2">{{ $t('apply') }}</button>
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
                        <!-- Average Rating Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card rating-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Average Rating') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ averageRating.toFixed(1) }}</h6>
                                            <span class="text-muted small pt-2 ps-1">/ 5</span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="rating-stars d-flex">
                                            <template v-for="n in 5" :key="n">
                                                <i class="bi"
                                                   :class="n <= Math.round(averageRating) ? 'bi-star-fill text-warning' : 'bi-star text-warning'"></i>
                                            </template>
                                        </div>
                                        <small class="text-muted">({{ totalRatings }} {{ $t('reviews') }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rating Breakdown Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Rating Breakdown') }}</h5>
                                    <div v-for="(count, index) in ratingDistribution" :key="index" class="mb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 text-nowrap" style="width: 50px;">
                                                {{ 5-index }} <i class="bi bi-star-fill text-warning"></i>
                                            </span>
                                            <div class="progress flex-grow-1" style="height: 10px;">
                                                <div class="progress-bar bg-warning"
                                                     role="progressbar"
                                                     :style="{ width: getPercentage(count) + '%' }"
                                                     :aria-valuenow="getPercentage(count)">
                                                </div>
                                            </div>
                                            <span class="ms-2 small">{{ count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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

                        <!-- Rental Orders Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Rental Orders') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-calendar2-check"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ rentalOrders }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Rentals Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card completed-rentals-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Completed Rentals') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-check-circle"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ rentalStats.completed }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Rentals Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card current-rentals-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Current Rentals') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ rentalStats.current }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Rentals Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card upcoming-rentals-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Upcoming Rentals') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-calendar-plus"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ rentalStats.upcoming }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Available Pieces Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card available-pieces-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Available Pieces') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ piecesStats.available }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Purchased Pieces Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card purchased-pieces-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('Purchased Pieces') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cart-check-fill"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ piecesStats.purchased }}</h6>
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

const { t } = useI18n();

const props = defineProps({
    auth: {
        user: {
            name: String,
            email: String
        }
    },
    roles: Number,
    admins: Number,
    branches: Number,
    salesOrders: Number,
    rentalOrders: Number,
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
            breakdown: [0, 0, 0, 0, 0] // [5-star, 4-star, 3-star, 2-star, 1-star]
        })
    },
    filters: {
        period: String,
        from_date: String,
        to_date: String
    }
});

const filters = ref({
    period: props.filters?.period || 'all',
    from_date: props.filters?.from_date || null,
    to_date: props.filters?.to_date || null,
});

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
