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

                        <!-- Reviews Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('total_reviews') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-star"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ reviewsData.total_reviews }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Reviews Card -->

                        <!-- Average Rating Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('average_rating') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ reviewsData.average_rating }}</h6>
                                            <span class="text-muted small pt-2 ps-1">/ 5</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Average Rating Card -->

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

                        <!-- Rental Requests Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card customers-card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $t('rental_requests') }}</h5>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6>{{ rentalRequestsCount }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Rental Requests Card -->

                        <!-- Rental Orders Card -->
                        <div class="col-xxl-3 col-md-3">
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
                        </div>
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
                    </div>
                </div>
                <!-- End Left side columns -->
            </div>

            <!-- Charts Row -->
            <!-- <div class="row"> -->
                <!-- Users by Role Chart -->
                <!-- <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $t('users_by_role') }}</h5>
                            <BarChart v-if="UserPerRolechartData" :chart-data="UserPerRolechartData" />
                            <div v-else class="text-muted">No data available</div>
                        </div>
                    </div>
                </div> -->

                <!-- Users by Status Chart -->
                <!-- <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $t('users_by_status') }}</h5>
                            <PieChart v-if="statusChartData" :chart-data="statusChartData" />
                            <div v-else class="text-muted">No data available</div>
                        </div>
                    </div>
                </div> -->
            <!-- </div> -->

            <!-- Rating Distribution Chart -->
            <!-- <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $t('rating_distribution') }}</h5>
                            <BarChart :chart-data="{
                                labels: ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'],
                                datasets: [{
                                    label: 'Number of Reviews',
                                    backgroundColor: '#36A2EB',
                                    data: Object.values(reviewsData.rating_distribution)
                                }]
                            }" />
                        </div>
                    </div>
                </div>
            </div> -->
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import PieChart from '@/Components/Charts/PieChart.vue';
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
    filters: Object,
});

const filters = ref({
    period: props.filters?.period || 'monthly',
    start_date: props.filters?.start_date || null,
    end_date: props.filters?.end_date || null,
});

const applyFilters = () => {
    router.get(route('dashboard'), {
        period: filters.value.period,
        start_date: filters.value.start_date,
        end_date: filters.value.end_date,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    filters.value = {
        period: 'monthly',
        start_date: null,
        end_date: null,
    };
    applyFilters();
};
</script>
