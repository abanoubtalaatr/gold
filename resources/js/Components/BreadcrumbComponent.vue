<template>
  <div class="pagetitle row">
    <div class="col-md-6">
      <div class="col-md-12 mb-2">
        <h1>{{ pageTitle }}</h1>
      </div>
      <nav class="col-md-12 d-flex justify-content-start">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <Link class="nav-link" :href="route('dashboard')">{{ homeLabel }}</Link>
          </li>
          <li
            v-for="(step, index) in breadcrumbSteps"
            :key="index"
            class="breadcrumb-item"
          >
            <span v-if="!step.route">{{ step.label }}</span>
            <Link v-else :href="route(step.route)">{{ step.label }}</Link>
          </li>
          <li class="breadcrumb-item active" v-if="showCreateButton">{{ pageTitle }}</li>
        </ol>
      </nav>
    </div>
    <div v-if="showCreateButton" class="col-md-6 justify-content-end d-flex">
      <div class="col-md-4 px-2 mt-3">
        <Link
          v-if="hasPermission(createPermission)"
          class="btn btn-primary w-100"
          :href="route(createRoute)"
        >
          {{ createButtonLabel }} &nbsp;
          <i class="bi bi-plus-circle"></i>
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { usePage, Link } from "@inertiajs/vue3";

const props = defineProps({
  pageTitle: {
    type: String,
    required: true,
  },
  createRoute: {
    type: String,
    default: "",
  },
  createPermission: {
    type: String,
    default: "create",
  },
  homeLabel: {
    type: String,
    default: "Home",
  },
  createButtonLabel: {
    type: String,
    default: "Create",
  },
  showCreateButton: {
    type: Boolean,
    default: true,
  },
  breadcrumbSteps: {
    type: Array,
    default: () => [],
  },
});

const page = usePage();
const hasPermission = (permission) => {
  return page.props.auth_permissions.includes(permission);
};
</script>

<style scoped>
.pagetitle {
  margin-bottom: 1rem;
}
</style>
