<template>
    <el-button
        color="#9f0e1c"
        plain
        circle
        :icon="Delete"
    
        @click="handleDelete"
    />

</template>

<script setup>
import Swal from "sweetalert2";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { Edit, Delete } from "@element-plus/icons-vue";

const props = defineProps({
    id: {
        type: [Number, String],
        required: true,
    },
    deleteUrl: {
        type: String,
        required: true,
    },
});

const { t } = useI18n();

const handleDelete = () => {
    Swal.fire({
        title: t("are_your_sure"),
        text: t("delete_confirmation"),
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: t("yes"),
        cancelButtonText: t("cancel"),
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(props.deleteUrl, {
                onSuccess: () =>
                    Swal.fire(t("deleted"), t("data_deleted"), "success"),
                onError: () => Swal.fire("Error!", t("delete_error"), "error"),
            });
        }
    });
};
</script>
