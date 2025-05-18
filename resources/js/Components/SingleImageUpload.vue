<template>
 <el-form-item>
  <div class="upload-container">
    <el-upload
      action=""
      :limit="1"
      accept="image/*"
      :on-change="handleFileChange"
      :auto-upload="false"
      list-type="picture-card"
    >
      <img
                                                v-if="props.modelValue"
                                                :src="props.modelValue"
                                                class="img-thumbnail"
                                                width="100"
                                            />
      <div class="upload-placeholder">
        <i class="el-icon-plus"></i>
        <div>{{ $t("upload_image") }}</div>
      </div>
    </el-upload>
    <small class="upload-tip">{{ $t("upload_image_tip") }}</small>
  </div>
</el-form-item>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
  modelValue: {
    type: [String, Object, File], // يمكن أن تكون سلسلة، كائن، أو ملف
    default: "",
  },
});

const emit = defineEmits(["update:modelValue"]);

const fileList = ref([]);

const handleFileChange = (file) => {
  if (file.raw) {
    emit("update:modelValue", file.raw); // إذا تم رفع صورة جديدة
    fileList.value = [file]; // تحديث قائمة الملفات
  } else {
    emit("update:modelValue", props.modelValue); // الإبقاء على الصورة الحالية
  }
};

// عند التعديل، اجعل الصورة الحالية مرئية
onMounted(() => {
  if (props.modelValue) {
    fileList.value = [
      {
        name: "Current Image",
        url:
          typeof props.modelValue === "string"
            ? `${props.modelValue}`
            : props.modelValue.url,
      },
    ];
  }
});



watch(
  () => props.modelValue,
  (newValue) => {
    if (!newValue) {
      fileList.value = [];
    } else if (typeof newValue === "string") {
      fileList.value = [{ name: "Current Image", url: newValue }];
    }
  }
);
</script>
