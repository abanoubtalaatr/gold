<template>
    <AuthenticatedLayout>
        <!-- breadcrumb -->
        <div class="pagetitle row">
            <BreadcrumbComponent
                :pageTitle="$t('banners')"
                :mainRoute="'banners.index'"
                :createRoute="'banners.create'"
                :createPermission="'create banners'"
                :homeLabel="$t('home')"
                :createButtonLabel="$t('create')"
            />
        </div>
        <!-- End breadcrumb -->

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
                        <template #image="{ data }">
                            <img
                                v-if="data.image"
                                :src="data.image_url"
                                class="h-10 w-10 rounded-full"
                                :alt="$t('image')"
                            />
                        </template>
                        <template #is_active="{ data }">
                            <ActivateToggle
                                :id="data.id"
                                :is-active="data.is_active == 1"
                                :activate-url="`/banners/${data.id}/activate`"
                                @update:is-active="
                                    (newStatus) =>
                                        updateStatus(data.id, newStatus)
                                "
                            />
                        </template>
                        <template #edit="{ data }">
                            <Link
                                class="btn btn-outline-secondary"
                                :href="
                                    route('banners.edit', {
                                        banner: data.id,
                                    })
                                "
                            >
                                <i class="bi bi-pencil-square"></i>
                            </Link>
                        </template>
                        <template #delete="{ data }">
                            <DeleteAction
                                :id="data.id"
                                :delete-url="
                                    route('banners.destroy', {
                                        banner: data.id,
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
import { reactive, ref } from "vue";
import { useI18n } from "vue-i18n";
import FilterComponent from "@/Components/FilterComponent.vue";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";
import ActivateToggle from "@/Components/ActivateToggle.vue";
import DeleteAction from "@/Components/DeleteAction.vue";
import DataTable from "@/Components/DataTable.vue";

const { t } = useI18n();
const props = defineProps({ 
    items: Object,
    filters: Object 
});

const filterForm = reactive({
    title: props.filters?.title || "",
    is_active: props.filters?.is_active ?? "",
});
console.log(props.items);
const filterFields = [
    {
        key: "title",
        type: "text",
        placeholder: t("title"),
    },
    {
        key: "is_active",
        type: "select",
        placeholder: t("status"),
        options: [
            { label: t("all"), value: "" },
            { label: t("active"), value: "1" },
            { label: t("not_active"), value: "0" },
        ],
    },
];
const updateStatus = (id, newStatus) => {
    const updatedStatus = props.items.data.find((item) => item.id === id);
    if (updatedStatus) {
        updatedStatus.is_active = newStatus ? 1 : 0;
    }
};
const headers = [
    { key: "title", label: t("title") },
    { key: "image", label: t("image") },
    { key: "is_active", label: t("status") },
    { key: "sort_order", label: t("sort_order") },
    { key: "edit", label: t("edit"), slot: true },
    { key: "delete", label: t("delete"), slot: true },
];

const handleFilterUpdate = (updatedFilters) => {
    Object.assign(filterForm, updatedFilters);
    router.get(route("banners.index"), filterForm, {
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
