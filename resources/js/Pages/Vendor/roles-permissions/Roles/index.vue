<template>
    <AuthenticatedLayout>
        <!-- breadcrumb-->

        <div class="pagetitle row">
            <BreadcrumbComponent :pageTitle="$t('Roles')" createRoute="vendor.roles.create"
                createPermission="vendor create roles" :homeLabel="$t('home')" :createButtonLabel="$t('create')" />
        </div>
        <!-- End breadcrumb-->
        <section class="section dashboard">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <div class="col-md-11 px-0">
                                <FilterComponent :filter-fields="filterFields" :initial-filters="filterForm"
                                    @update:filters="handleFilterUpdate" />
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <!-- Table -->
                        <DataTable :headers="headers" :data="roles.data" :pagination-links="roles.links"
                            @update:page="handlePageChange">
                            <template #permissions="{ data }">
                                <Link v-if="hasPermission('vendor update roles')" class="btn btn-dark btn-sm"
                                    :href="`roles/${data.id}/give-permissions`">
                                <i class="bi bi-lock"></i>
                                </Link>
                            </template>
                            <template #is_active="{ data }">
                                <ActivateToggle :id="data.id" :is-active="data.is_active == 1"
                                    :activate-url="`roles/${data.id}/activate`" @update:is-active="
                                        (newStatus) =>
                                            updateStatus(data.id, newStatus)
                                    " />
                            </template>
                            <template #edit="{ data }">
                                <div class="d-flex gap-2">


                                    <EditButton @click="
                                        router.get(
                                            route('vendor.roles.edit', {
                                                role: data.id,
                                            })
                                        )
                                        " />

                                </div>
                            </template>
                            <template #delete="{ data }">
                                <DeleteAction :id="data.id" :delete-url="route('vendor.roles.destroy', {
                                    role: data.id,
                                })
                                    " />
                            </template>

                        </DataTable>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Link, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";
import FilterComponent from "@/Components/FilterComponent.vue";
import DeleteAction from "@/Components/DeleteAction.vue";
import ActivateToggle from "@/Components/ActivateToggle.vue";
import { reactive } from "vue";
import EditButton from "@/Components/EditButton.vue";

import DataTable from "@/Components/DataTable.vue";
const { t } = useI18n();
const page = usePage();

const props = defineProps({ roles: Object });

const hasPermission = (permission) => {
    return page.props.auth_permissions.includes(permission);
};

const filterForm = reactive({
    name: "",

    is_active: "",
});

const filterFields = [
    {
        key: "name",
        type: "text",
        placeholder: t("name"),
    },

    {
        key: "is_active",
        type: "select",
        placeholder: t("status"),
        options: [
            { label: t("active"), value: 1 },
            { label: t("not_active"), value: 0 },
        ],
    },
];

const headers = [
    { key: "name", label: t("name") },
    { key: "permissions", label: t("permissions") },
    { key: "is_active", label: t("status") },
    { key: "edit", label: t("edit"), slot: true },
    { key: "delete", label: t("delete"), slot: true },

];
const updateStatus = (id, newStatus) => {
    const updatedStatus = roles.data.find((item) => item.id === id);
    if (updatedStatus) {
        updatedStatus.is_active = newStatus ? 1 : 0;
    }
};

const handleFilterUpdate = (updatedFilters) => {
    Object.assign(filterForm, updatedFilters);
    router.get(route("vendor.roles.index"), filterForm, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>
