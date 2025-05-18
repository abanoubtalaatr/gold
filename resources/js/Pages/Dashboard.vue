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




      <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">
            <!-- Users Card -->
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card customers-card">
                <div class="card-body">
                  <h5 class="card-title">
                    {{ $t('users') }}
                    <!-- <span>| This Year</span> -->
                  </h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <h6>{{ userCount }}</h6>
                      <!-- <span class="text-danger small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">decrease</span> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Customers Card -->

          
            <!-- Rules Card -->
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card revenue-card">
                <div class="card-body">
                  <h5 class="card-title">
                    {{ $t('roles') }}
                    <!-- <span>| This Month</span> -->
                  </h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-lock"></i>
                    </div>
                    <div class="ps-3">
                      <h6>{{ rolesCount }}</h6>
                      <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Rules Card -->
          </div>
        </div>
        <!-- End Left side columns -->
      </div>



    </section>
  </AuthenticatedLayout>
</template>


<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ar from "element-plus/es/locale/lang/ar";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
  userCount: Number,
  rolesCount: Number,

});

</script>
