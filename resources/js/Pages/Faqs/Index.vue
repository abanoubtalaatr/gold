<template>
    <AuthenticatedLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>الأسئلة الشائعة</h1>
            <Link :href="route('faqs.create')" class="btn btn-primary">
                <el-icon class="me-2"><Plus /></el-icon>
                إضافة سؤال جديد
            </Link>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Table -->
                <DataTable
                    :headers="headers"
                    :data="faqs.data"
                    :pagination-links="faqs.links"
                    @update:page="handlePageChange"
                >
                    <template #is_active="{ data }">
                        <ActivateToggle
                            :id="data.id"
                            :is-active="data.is_active == 1"
                            :activate-url="`/faqs/${data.id}/activate`"
                        />
                    </template>
                    <template #edit="{ data }">
                        <Link
                            class="btn btn-outline-secondary"
                            :href="
                                route('faqs.edit', {
                                    faq: data.id,
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
                                route('faqs.destroy', {
                                    faq: data.id,
                                })
                            "
                        />
                    </template>
                </DataTable>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { Plus, Edit, Delete } from "@element-plus/icons-vue";
import DeleteAction from "@/Components/DeleteAction.vue";
import ActivateToggle from "@/Components/ActivateToggle.vue";
import DataTable from "@/Components/DataTable.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useI18n } from "vue-i18n";

const props = defineProps({ faqs: Array });
const loading = ref(null);
const { t } = useI18n();
const toggleStatus = async (faq) => {
    loading.value = faq.id;
    try {
        await axios.post(route("faqs.toggle-status", faq.id));
        faq.is_active = !faq.is_active;
    } finally {
        loading.value = null;
    }
};

const headers = [
    { key: "question", label: t("question") },
    { key: "answer", label: t("answer") },
    { key: "is_active", label: t("feature.fields.is_active") },
    { key: "sort_order", label: t("sort_order") },
    { key: "edit", label: t("edit"), slot: true },
    { key: "delete", label: t("delete"), slot: true },
];
</script>
