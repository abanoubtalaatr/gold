<template>
    <AuthenticatedLayout>
        <!-- breadcrumb-->
        <div class="pagetitle">
            <h1>{{ $t('dashboard') }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link class="nav-link" :href="route('dashboard')">
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
                                            <option value="daily">{{ $t('daily') }}</option>
                                            <option value="weekly">{{ $t('weekly') }}</option>
                                            <option value="monthly">{{ $t('monthly') }}</option>
                                            <option value="quarterly">{{ $t('quarterly') }}</option>
                                            <option value="semiannually">{{ $t('semiannually') }}</option>
                                            <option value="yearly">{{ $t('yearly') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ $t('from_date') }}</label>
                                        <input type="date" class="form-control" v-model="filters.start_date">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ $t('to_date') }}</label>
                                        <input type="date" class="form-control" v-model="filters.end_date">
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
                        <!-- Users Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('users') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ userCount }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Users Card -->

                        <!-- Roles Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('roles') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-lock"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ rolesCount }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Roles Card -->


                        <!-- Sales Orders Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('sales_orders') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cart-check"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ salesOrdersCount }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Sales Orders Card -->

                        <!-- Rental Orders Card -->
                        <!-- <div class="col-xxl-3 col-md-3">
                            <div class="card info-card revenue-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('rental_orders') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-calendar2-check"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ rentalOrdersCount }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- End Rental Orders Card -->

                        <!-- Total Branches Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('total_branches') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-shop"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ branchesCount }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Total Branches Card -->

                        <!-- Total Vendors Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('total_vendors') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ vendorsCount }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Total Vendors Card -->

                        <!-- New Rental Statistics Cards -->
                        <!-- Completed Rentals Card -->
                        <!-- <div class="col-xxl-3 col-md-3">
                            <div class="card info-card completed-rentals-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('completed_rentals') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-check-circle"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ rentalStats.completed }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- End Completed Rentals Card -->

                        <!-- Current Rentals Card -->
                        <!-- <div class="col-xxl-3 col-md-3">
                            <div class="card info-card current-rentals-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('current_rentals') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ rentalStats.current }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- End Current Rentals Card -->

                        <!-- Upcoming Rentals Card -->
                        <!-- <div class="col-xxl-3 col-md-3">
                            <div class="card info-card upcoming-rentals-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('upcoming_rentals') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-calendar-plus"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ rentalStats.upcoming }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- End Upcoming Rentals Card -->

                        <!-- Available Pieces Card -->
                        <!-- <div class="col-xxl-3 col-md-3">
                            <div class="card info-card available-pieces-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('available_pieces') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ availablePiecesCount }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- End Available Pieces Card -->

                        <!-- Purchased Pieces Card -->
                        <!-- <div class="col-xxl-3 col-md-3">
                            <div class="card info-card purchased-pieces-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('purchased_pieces') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cart-check-fill"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ purchasedPiecesCount }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- End Purchased Pieces Card -->
                    </div>
                </div>
                <!-- End Left side columns -->
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    UserPerRolechartData: Object,
    statusChartData: Object,
    reviewsData: Object,
    userCount: Number,
    rolesCount: Number,
    salesOrdersCount: Number,
    rentalRequestsCount: Number,
    rentalOrdersCount: Number,
    branchesCount: Number,
    vendorsCount: Number,
    rentalStats: Object,
    availablePiecesCount: Number,
    purchasedPiecesCount: Number,
    filters: Object,
});

const filters = ref({
    period: props.filters?.period || 'monthly',
    start_date: props.filters?.start_date || null,
    end_date: props.filters?.end_date || null,
});

const applyFilters = () => {
    const params = {
        period: filters.value.period,
        start_date: filters.value.start_date || null,
        end_date: filters.value.end_date || null,
    };

    router.get(route('dashboard'), params, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetFilters = () => {
    filters.value = {
        period: 'monthly',
        start_date: null,
        end_date: null,
    };

    router.get(route('dashboard'), {
        period: 'monthly',
        start_date: null,
        end_date: null,
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
</style>
