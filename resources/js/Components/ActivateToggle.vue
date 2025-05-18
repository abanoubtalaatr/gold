<template>
  <label class="inline-flex items-center cursor-pointer">
    <input
      type="checkbox"
      class="sr-only peer"
      :checked="isActive"
      @change="handleActivate"
    />
    <div
      class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"
    ></div>
  </label>
</template>

<script setup>
import Swal from "sweetalert2";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { reactive , ref } from "vue";

const props = defineProps({
  id: {
    type: [Number, String],
    required: true,
  },
  isActive: {
    type: Boolean,
    required: true,
  },
  activateUrl: {
    type: String,
    required: true,
  },
});

const { t } = useI18n();
console.log("props", props);
const localIsActive = ref(props.isActive);

const handleActivate = () => {
  Swal.fire({
    title: t("are_your_sure"),
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: t("yes"),
    cancelButtonText: t("cancel"),
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(props.activateUrl, { id: props.id }, {
        onSuccess: () => {
          Swal.fire(t("updated"), t("status_updated"), "success");
          localIsActive.value = !localIsActive.value;
        },
        onError: () => {
          Swal.fire(t("error"), t("status_update_error"), "error");
        },
      });
    }
  });
};



</script>
