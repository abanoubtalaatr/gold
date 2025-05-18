<template>
    <el-form
        :model="form"
        @submit.prevent="submitForm"
        label-position="top"
        class="row g-3"
    >
        <template v-for="field in fields" :key="field.key">
            <!-- Multilang Fields -->
            <template v-if="field.multilang">
                <div :class="field.colClass || 'col-md-6'" v-for="lang in supportedLanguages" :key="lang">
                    <el-form-item 
                        :label="field.label + ' (' + t(lang) + ')'"
                        :error="getError(`translations.${lang}.${field.key}`)"
                    >
                        <div v-if="field.type === 'textarea' && field.editor" class="editor-wrapper">
                            <Editor
                                v-model="form.translations[lang][field.key]"
                                :api-key="'your-tiny-mce-key'"
                                :init="editorConfig"
                                @input="clearErrors(`translations.${lang}.${field.key}`)"
                            />
                        </div>
                        <el-input
                            v-else
                            v-model="form.translations[lang][field.key]"
                            :type="field.type"
                            :placeholder="field.placeholder"
                            :rows="field.type === 'textarea' ? 4 : undefined"
                            @input="clearErrors(`translations.${lang}.${field.key}`)"
                        />
                    </el-form-item>
                </div>
            </template>

            <!-- Regular Fields -->
            <div v-else :class="field.colClass || 'col-md-6'">
                <el-form-item 
                    :label="field.label"
                    :error="getError(field.key)"
                >
                    <!-- Select Component -->
                <DynamicSelect
                    v-if="field.type === 'select'"
                    :label="field.label"
                    :modelValue="form[field.key]"
                    :placeholder="field.placeholder"
                    :options="field.options"
                    :multiple="field.multiple"
                    @update:modelValue="(value) => updateFieldValue(field.key, value)"
                    @change="(value) => handleChange(field, value)"
                />
                    <!-- Editor Field -->
                    <div v-else-if="field.type === 'textarea' && field.editor" class="editor-wrapper">
                        <Editor
                            v-model="form[field.key]"
                            :api-key="'your-tiny-mce-key'"
                            :init="editorConfig"
                            @input="clearErrors(field.key)"
                        />
                    </div>

                    <template v-else-if="field.type === 'phone'">
                <el-input
                    v-model="form[field.key]"
                    :rules="[{ pattern: /^\5\d{8}$/, message: 'Invalid phone number format' }]"
                    :placeholder="field.placeholder"
                    :maxlength="9"
                    class="phone-input"
                    @input="handleInput(field)"
                >
                    <template #prefix>
                        <span>+966</span>
                    </template>
                </el-input>
            </template>


                <!-- Upload Components -->
                <!-- <template v-else-if="field.type === 'upload'">
          <SingleImageUpload
            v-if="!field.multiple"
            v-model="form[field.key]"
            :label="field.label"
            :accept="field.accept"
            :tip="field.tip"
            @change="handleUploadChange(field)"
          />

          <MultipleImageUpload
            v-else
            v-model="form[field.key]"
            :label="field.label"
            :accept="field.accept"
            :limit="field.limit"
            @change="handleUploadChange(field)"
            @remove="handleUploadRemove(field)"
          />
        </template> -->

                <template v-else-if="field.type === 'upload'">
                    <el-upload
                        v-if="field.type === 'upload'"
                        :action="''"
                        :auto-upload="false"
                        :file-list="fileList[field.key]"
                        :on-change="(file) => handleUploadChange(file, field)"
                        list-type="picture-card"
                        :accept="field.accept"
                    >
                        <i class="el-icon-plus"></i>
                    </el-upload>
                </template>

                <component
                    v-else
                    :is="getInputComponent(field)"
                    v-model="form[field.key]"
                    :placeholder="field.placeholder"
                    v-bind="getComponentProps(field)"
                    @input="handleInput(field)"
                    @change="handleChange(field)"
                />

                <small v-if="errors[field.key]" class="text-danger">
                    {{ errors[field.key][0] }}
                </small>
                </el-form-item>
            </div>
        </template>

        <div class="col-12">
            <el-button type="primary" native-type="submit" :loading="loading">
                {{ submitLabel }}
            </el-button>
        </div>
    </el-form>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import settings from '@/src/config/settings';
import DynamicSelect from "@/Components/DynamicSelect.vue";
import SingleImageUpload from "@/Components/SingleImageUpload.vue";
import MultipleImageUpload from "@/Components/MultipleImageUpload.vue";
import Editor from '@tinymce/tinymce-vue';

const props = defineProps({
    fields: {
        type: Array,
        required: true
    },
    initialForm: {
        type: Object,
        required: true
    },
    submitLabel: {
        type: String,
        default: 'Submit'
    },
    loading: {
        type: Boolean,
        default: false
    }
});

const { t } = useI18n();
const supportedLanguages = settings.supportedLanguages;
const form = ref(props.initialForm);

const emit = defineEmits(['submit', 'change']);

const submitForm = () => {
    emit('submit', form.value);
};

const editorConfig = {
    height: 300,
    menubar: false,
    directionality: 'auto',
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | formatselect | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | help',
    content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px }'
};

const getComponentProps = (field) => ({
    type: field.type,
    placeholder: field.placeholder,
    rows: field.type === "textarea" ? 4 : undefined,
    min: field.min,
    max: field.max,
    step: field.step,
    clearable: field.clearable !== false,
    disabled: field.disabled,
    editor: field.editor,
    "show-password": field.type === "password",
});

const getInputComponent = (field) => {
    if (field.type === 'textarea' && field.editor) {
        return Editor;
    }
    
    switch (field.type) {
        case 'textarea':
            return 'el-input';
        case 'select':
            return 'el-select';
        case 'number':
            return 'el-input-number';
        default:
            return 'el-input';
    }
};

const errors = ref({});

const handleSubmit = async () => {
    try {
        loading.value = true;
        errors.value = {};
        console.log("Form Data:", form.value);
        await emit("submit", form.value);
    } catch (error) {
        console.error("Error during form submission:", error);
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
            console.log("Validation Errors:", errors.value);
        }
    } finally {
        loading.value = false;
    }
};

const getError = (path) => {
    return form.value.errors[path];
};

const clearErrors = (path) => {
    delete form.value.errors[path];
};

const handleInput = (field) => {
    clearErrors(field.key);
    emit("input", form.value);
};

const handleChange = (field) => {
    clearErrors(field.key);
    emit('change', { field: field.key, value: form.value[field.key] });
};

const handleUploadChange = (file, field) => {
    if (file && file.raw) {
        form[field.key] = file.raw;
        fileList.value[field.key] = [
            {
                raw: file.raw,
                name: file.name,
                url: URL.createObjectURL(file.raw),
            },
        ];
    } else {
        form[field.key] = null;
        fileList.value[field.key] = [];
    }
};

const handleUploadRemove = (field, file) => {
    if (field.multiple) {
        const index = fileList.value[field.key].indexOf(file);
        if (index > -1) {
            fileList.value[field.key].splice(index, 1);
            form[field.key] = fileList.value[field.key].map((f) => f.raw || f);
        }
    } else {
        form[field.key] = null;
    }
    emit("upload-remove", { field: field.key, file });
};

const getResponsiveClass = (field) => field.colClass || "col-md-6";

const updateFieldValue = (key, value) => {
    form.value[key] = value;
    console.log(`Updated ${key} to:`, value);
};

const fileList = ref({});

props.fields.forEach((field) => {
    if (field.type === "upload") {
        fileList.value[field.key] = [];

        if (props.initialForm[field.key]) {
            const initialFile = {
                name: props.initialForm[field.key].split("/").pop(),
                url: `/storage/${props.initialForm[field.key]}`,
            };
            fileList.value[field.key].push(initialFile);
        }
    }
});
</script>

<style scoped>
.row {
    margin-bottom: 1rem;
}

.el-form-item {
    margin-bottom: 1.5rem;
}

.d-flex {
    display: flex;
    flex-wrap: wrap;
}

.gap-3 {
    gap: 1rem;
}

.flex-fill {
    flex: 1 1 calc(50% - 1rem);
    min-width: 300px; 
}

.upload-demo {
    width: 100%;
}

.el-upload {
    width: 100%;
}

.el-upload-list {
    width: 100%;
    margin-top: 10px;
}

.el-upload__tip {
    font-size: 12px;
    color: #606266;
    margin-top: 7px;
}

.editor-container {
    margin-bottom: 1rem;
}

.editor-wrapper {
    margin-bottom: 20px;
}

.tox-tinymce {
    border: 1px solid var(--el-border-color) !important;
    border-radius: 4px !important;
}

[dir="rtl"] .tox-tinymce {
    direction: rtl;
}

.el-form-item.is-error .tox-tinymce {
    border-color: var(--el-color-danger) !important;
}
</style>
