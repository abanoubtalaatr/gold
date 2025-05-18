<template>
  <el-form-item  class="col-md-12">
    <div class="upload-container">
      <el-upload
        action=""
        :file-list="fileList"
        multiple
        accept="image/*"
        :on-change="handleFileChange"
        :auto-upload="false"
        list-type="picture-card"
      >
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
import { ref, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

// Props
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
  label: {
    type: String,
    default: "Images",
  },
});

const emit = defineEmits(["update:modelValue"]);

const fileList = ref([]);

onMounted(() => {
  initializeFileList();
});

watch(
  () => props.modelValue,
  () => initializeFileList()
);

const initializeFileList = () => {
  if (Array.isArray(props.modelValue) && props.modelValue.length) {
    fileList.value = props.modelValue.map((image, index) => ({
      name: `Image ${index + 1}`,
      url: `/storage/${image}`,
    }));
  }
};

const handleFileChange = (files) => {
  const newFiles = files.map((file) => ({
    name: file.name || `Image ${fileList.value.length + 1}`,
    url: file.url || URL.createObjectURL(file.raw),
    raw: file.raw,
  }));

  fileList.value = [...fileList.value, ...newFiles];

  emit(
    "update:modelValue",
    fileList.value.map((file) => file.raw || file.url)
  );
};
</script>

<style scoped>
.upload-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  color: #409eff;
  cursor: pointer;
  transition: all 0.3s ease;
}

.upload-placeholder:hover {
  color: #66b1ff;
}

.upload-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 8px;
  text-align: center;
}
</style>
