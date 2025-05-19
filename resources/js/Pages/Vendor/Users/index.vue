<template>
    <AuthenticatedLayout>
        <!-- breadcrumb-->
        <div class="pagetitle row">
            <BreadcrumbComponent :pageTitle="$t('vendor_admins')" createRoute="vendor.users.create"
                createPermission="vendor create users" :homeLabel="$t('home')" :createButtonLabel="$t('create')" />
        </div>

        <!-- End breadcrumb-->

        <section class="section dashboard">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div class="col-md-12 px-2">
                            <FilterComponent :filter-fields="filterFields" :initial-filters="filterForm"
                                @update:filters="handleFilterUpdate" />
                        </div>
                        <div class="col-md-1 px-2">
                            <!-- <Link
                                v-if="hasPermission('read users')"
                                class="btn btn-outline-secondary w-100"
                                :href="route('export.users')"
                                ><i class="bi bi-filetype-xls"></i
                            ></Link> -->
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        

                        <DataTable :headers="headers" :data="users.data" :pagination-links="users.links"
                            noDataMessage="No users found." @update:page="handlePageChange">
                            <!-- Slot للأعمدة الخاصة -->
                            <template #avatar="{ data }">
                                <img :src="data.avatar" alt="Avatar" class="avatar" width="45px" />
                            </template>

                            <template #role="{ data }">
                                <span v-for="role in data.roles" :key="role.id" class="badge bg-secondary">
                                    {{ role.name }}
                                </span>
                            </template>

                            <template #is_active="{ data }">
                                <template v-if="isSuperAdmin(data)">
                                    <el-tag type="success">{{
                                        t("active")
                                        }}</el-tag>
                                </template>
                                <template v-else>
                                    <ActivateToggle v-if="!isSuperAdmin(data)" :id="data.id"
                                        :is-active="data.is_active == 1" :activate-url="`users/${data.id}/activate`"
                                        @update:is-active="
                                            (newStatus) =>
                                                updateStatus(data.id, newStatus)
                                        " />
                                </template>
                            </template>

                            <template #edit="{ data }">

                                <EditButton @click="
                                    router.get(
                                        route('vendor.users.edit', { user: data.id })
                                    )
                                    " />

                            </template>

                            <template #delete="{ data }">
                                <DeleteAction v-if="!isSuperAdmin(data)" :id="data.id" :delete-url="route('vendor.users.destroy', {
                                    user: data.id,
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
import { Link, usePage, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { reactive } from "vue";
import { useI18n } from "vue-i18n";
import FilterComponent from "@/Components/FilterComponent.vue";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";
import ActivateToggle from "@/Components/ActivateToggle.vue";
import DeleteAction from "@/Components/DeleteAction.vue";
import EditButton from "@/Components/EditButton.vue";
import DataTable from "@/Components/DataTable.vue";

const { t } = useI18n();
const props = defineProps({ users: Object });
const page = usePage();

const filterForm = reactive({
    name: "",
    email: "",
    is_active: "",
});
const headers = [
    { key: "name", label: t("name") },
    { key: "avatar", label: t("avatar"), slot: true },
    { key: "role", label: t("role"), slot: true },
    { key: "email", label: t("email") },
    { key: "created_at", label: t("created_at") },
    { key: "is_active", label: t("status"), slot: true },
    { key: "edit", label: t("edit"), slot: true },
    { key: "delete", label: t("delete"), slot: true },
];
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
        key: "is_active",
        type: "select",
        placeholder: t("status"),
        options: [
            { label: t("active"), value: 1 },
            { label: t("not_active"), value: 0 },
        ],
    },
];

const handleFilterUpdate = (updatedFilters) => {
    Object.assign(filterForm, updatedFilters);
    router.get(route("vendor.users.index"), filterForm, {
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

const hasPermission = (permission) => {
    return page.props.auth_permissions.includes(permission);
};
const Activate = (id) => {
    Swal.fire({
        title: t("are_your_sure"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#7066e0",
        confirmButtonText: t("yes"),
        cancelButtonText: t("cancel"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(`/users/${id}/activate`, {
                onSuccess: () => {
                    Swal.fire(
                        "Updated !",
                        "user stuaus item has been updated.",
                        "success"
                    );
                },
                onError: () => {
                    Swal.fire(
                        "Error!",
                        "There was an issue updating user status.",
                        "error"
                    );
                },
            });
        }
    });
};

const Delete = (id) => {
    Swal.fire({
        title: t("are_your_sure"),
        text: t("You_will_not_be_able_to_revert_this"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: t("yes"),
        cancelButtonText: t("cancel"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete("users/" + id, {
                onSuccess: () => {
                    Swal.fire({
                        title: t("data_deleted_successfully"),
                        icon: "success",
                    });
                },
                onError: () => {
                    Swal.fire(
                        "Error!",
                        "There was an issue deleting the item.",
                        "error"
                    );
                },
            });
        }
    });
};
const isSuperAdmin = (user) => {
    return user.email === 'admin@admin.com' || user.role === 'superadmin';
};
const exportUsers = () => {
    const url = route("export.users");
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", "users.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>
<style></style>
