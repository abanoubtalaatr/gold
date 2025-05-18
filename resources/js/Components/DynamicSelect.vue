<template>
  <el-select
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
    :placeholder="placeholder"
    :multiple="multiple"
    class="w-100"
    :class="{ 'is-rtl': isRTL }"
  >
    <el-option
      v-for="option in options"
      :key="option.value"
      :label="option.label"
      :value="option.value"
    />
  </el-select>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { usePage } from '@inertiajs/vue3';
import { ElSelect, ElOption } from 'element-plus';

const page = usePage();
const isRTL = computed(() => page.props.locale === 'ar');

const { t } = useI18n();

const props = defineProps({
  modelValue: {
    type: [String, Number, Array],
    required: true
  },
  options: {
    type: Array,
    required: true
  },
  placeholder: {
    type: String,
    default: ''
  },
  multiple: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const localValue = ref(props.modelValue ? Number(props.modelValue) : null);

// مراقبة التغييرات في modelValue
watch(() => props.modelValue, (newValue) => {
  if (newValue !== undefined) {
    localValue.value = Number(newValue);
  }
}, { immediate: true });

// مراقبة التغييرات في options
watch(() => props.options, () => {
  if (props.modelValue !== undefined) {
    localValue.value = Number(props.modelValue);
  }
}, { immediate: true });

const handleChange = (value) => {
  localValue.value = value;
  emit('update:modelValue', value);
  emit('change', value);
};

// تهيئة القيمة عند تحميل المكون
onMounted(() => {
  if (props.modelValue) {
    localValue.value = Number(props.modelValue);
  }
});
</script>

<style scoped>
.el-select {
  width: 100%;
}

.el-select .el-input__wrapper {
  background-color: #fff;
  border-radius: 4px;
  height: calc(1.5em + 0.75rem + 2px);
  padding: 0.375rem 0.75rem;
}

.el-select:not(.el-select--disabled):hover .el-input__wrapper {
  border-color: #409eff;
}

.el-select__selected-item {
  border-radius: 4px;
}

/* RTL Support */
.is-rtl :deep(.el-select__placeholder),
.is-rtl :deep(.el-input__wrapper),
.is-rtl :deep(.el-select__tags),
.is-rtl :deep(.el-select-dropdown__item) {
  text-align: right !important;
}

/* LTR Support */
:not(.is-rtl) :deep(.el-select__placeholder),
:not(.is-rtl) :deep(.el-input__wrapper),
:not(.is-rtl) :deep(.el-select__tags),
:not(.is-rtl) :deep(.el-select-dropdown__item) {
  text-align: left !important;
}

:deep(.el-select-dropdown.el-popper) {
  text-align: right;
}

:deep(.el-select-dropdown__item) {
  text-align: right;
  direction: rtl;
}

/* Override for LTR */
:not(.is-rtl) :deep(.el-select-dropdown.el-popper),
:not(.is-rtl) :deep(.el-select-dropdown__item) {
  text-align: left;
  direction: ltr;
}
</style>
