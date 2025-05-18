<template>
    <AuthenticatedLayout>
        <!-- breadcrumb -->
        <div class="pagetitle mb-4">
            <h1>{{ $t("contacts") }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link class="nav-link" :href="route('dashboard')">{{
                            $t("home")
                        }}</Link>
                    </li>
                    <li class="breadcrumb-item active">{{ $t("contacts") }}</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div class="col-md-12 px-0">
                            <FilterComponent
                                :filter-fields="filterFields"
                                :initial-filters="filterForm"
                                @update:filters="handleFilterUpdate"
                            />
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Table -->
                    <DataTable
                        :headers="headers"
                        :data="items.data"
                        :pagination-links="items.links"
                        @update:page="handlePageChange"
                    >
                        <template #read="{ data }">
                            <ActivateToggle
                                :id="data.id"
                                :is-active="data.read == 1"
                                :activate-url="`/contacts/${data.id}/read`"
                            />
                        </template>
                        <template #show="{ data }">
                            <Link
                                class="btn btn-outline-secondary"
                                :href="
                                    route('contacts.show', { contact: data.id })
                                "
                                method="get"
                                as="button"
                                preserve-scroll
                            >
                                <i class="bi bi-eye"></i>
                            </Link>
                        </template>
                        <template #delete="{ data }">
                            <DeleteAction
                                :id="data.id"
                                :delete-url="
                                    route('contacts.destroy', {
                                        contact: data.id,
                                    })
                                "
                            />
                        </template>
                    </DataTable>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { Link, router } from "@inertiajs/vue3";
import { reactive } from "vue";
import { useI18n } from "vue-i18n";
import FilterComponent from "@/Components/FilterComponent.vue";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";
import ActivateToggle from "@/Components/ActivateToggle.vue";
import DeleteAction from "@/Components/DeleteAction.vue";
import DataTable from "@/Components/DataTable.vue";

const { t } = useI18n();
const props = defineProps({ items: Object });

const filterForm = reactive({
    name: "",
    read: "",
});

const filterFields = [
    {
        key: "name",
        type: "text",
        placeholder: t("name"),
    },
    {
        key: "email",
        type: "text",
        placeholder: t("email"),
    },
    {
        key: "phone",
        type: "text",
        placeholder: t("phone"),
    },

    {
        key: "read",
        type: "select",
        placeholder: t("status"),
        options: [
            { label: t("read"), value: 1 },
            { label: t("not_read"), value: 0 },
        ],
    },
];

const headers = [
    { key: "name", label: t("name") },
    { key: "email", label: t("email") },
    { key: "phone", label: t("phone") },
    { key: "read", label: t("status") },
    { key: "show", label: t("show"), slot: true },
    { key: "delete", label: t("delete"), slot: true },
];

const handleFilterUpdate = (updatedFilters) => {
    Object.assign(filterForm, updatedFilters);
    router.get(route("contacts.index"), filterForm, {
        preserveState: true,
        preserveScroll: true,
    });
};

const handlePageChange = (page) => {
    router.get(page, filterForm, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>
