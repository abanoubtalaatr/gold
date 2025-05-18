<template>
    <AuthenticatedLayout>
        <!-- breadcrumb -->
        <div class="pagetitle row">
            <BreadcrumbComponent
                :pageTitle="$t('faqs')"
                :mainRoute="'faqs.index'"
                :subTitle="$t('edit')"
                :isIndexPage="false"
                :showMainRoute="false"
                :homeLabel="$t('home')"
            />
        </div>

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $t("update_faq") }}</h5>
                            <el-form
                                :model="form"
                                :rules="rules"
                                ref="formRef"
                                class="row g-3"
                            >
                                <div
                                    v-for="lang in supportedLanguages"
                                    :key="lang"
                                    class="col-md-6"
                                >
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>{{ $t(lang) }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <el-form-item
                                                :label="$t('question')"
                                                :prop="
                                                    'translations.' +
                                                    lang +
                                                    '.question'
                                                "
                                            >
                                                <el-input
                                                    v-model="
                                                        form.translations[lang]
                                                            .question
                                                    "
                                                    :placeholder="
                                                        $t('enter_question')
                                                    "
                                                    :dir="
                                                        lang === 'ar'
                                                            ? 'rtl'
                                                            : 'ltr'
                                                    "
                                                />
                                            </el-form-item>

                                            <el-form-item
                                                :label="$t('answer')"
                                                :prop="
                                                    'translations.' +
                                                    lang +
                                                    '.answer'
                                                "
                                            >
                                                <el-input
                                                    v-model="
                                                        form.translations[lang]
                                                            .answer
                                                    "
                                                    type="textarea"
                                                    :rows="4"
                                                    :placeholder="
                                                        $t('enter_answer')
                                                    "
                                                    :dir="
                                                        lang === 'ar'
                                                            ? 'rtl'
                                                            : 'ltr'
                                                    "
                                                />
                                            </el-form-item>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sort Order -->
                                <div class="mb-3 col-md-6">
                                    <el-form-item :label="$t('sort_order')">
                                        <el-input
                                            v-model="form.sort_order"
                                            type="number"
                                            min="0"
                                            :placeholder="$t('sort_order')"
                                            :error="form.errors.sort_order"
                                        />
                                        <div
                                            v-if="form.errors.sort_order"
                                            class="error-message"
                                        >
                                            {{ form.errors.sort_order }}
                                        </div>
                                    </el-form-item>
                                </div>
                                <div class="text-center">
                                    <el-button
                                        type="primary"
                                        :loading="form.processing"
                                        @click="submitForm"
                                    >
                                        {{ $t("update") }}
                                    </el-button>
                                    <Link
                                        :href="route('faqs.index')"
                                        class="btn btn-secondary ms-2"
                                    >
                                        {{ $t("cancel") }}
                                    </Link>
                                </div>
                            </el-form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import { ElMessage } from "element-plus";
import { useI18n } from "vue-i18n";
import settings from "@/src/config/settings";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";

const { t } = useI18n();
const supportedLanguages = settings.supportedLanguages;
const props = defineProps({
    faq: {
        type: Object,
        required: true,
    },
});

const formRef = ref(null);

// Initialize form with translations
const form = useForm({
    translations: supportedLanguages.reduce((acc, lang) => {
        acc[lang] = {
            question: "",
            answer: "",
        };
        return acc;
    }, {}),
    is_active: true,
    sort_order: props.faq?.sort_order || 0,
});

// Set initial form values from props
onMounted(() => {
    if (props.faq) {
        form.is_active = props.faq.is_active;

        // Map translations from the backend format to the form format
        props.faq.translations.forEach((translation) => {
            if (form.translations[translation.locale]) {
                form.translations[translation.locale] = {
                    question: translation.question,
                    answer: translation.answer,
                };
            }
        });
    }
});

const rules = {
    "translations.ar.question": [
        { required: true, message: t("question_required_ar"), trigger: "blur" },
    ],
    "translations.ar.answer": [
        { required: true, message: t("answer_required_ar"), trigger: "blur" },
    ],
    "translations.en.question": [
        { required: true, message: t("question_required_en"), trigger: "blur" },
    ],
    "translations.en.answer": [
        { required: true, message: t("answer_required_en"), trigger: "blur" },
    ],
};

const submitForm = () => {
    formRef.value?.validate((valid) => {
        if (!valid) return;

        form.put(route("faqs.update", props.faq.id), {
            onSuccess: () => {
                ElMessage({
                    type: "success",
                    message: t("updated_successfully"),
                });
            },
            onError: (errors) => {
                ElMessage({
                    type: "error",
                    message: Object.values(errors)[0],
                });
            },
        });
    });
};
</script>

<style scoped>
.card {
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
}

.card-header h6 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

:deep(.el-form-item__label) {
    font-weight: 500;
}

:deep(.el-input__wrapper),
:deep(.el-textarea__inner) {
    box-shadow: none !important;
    border: 1px solid #dcdfe6;
}

:deep(.el-input__wrapper:hover),
:deep(.el-textarea__inner:hover) {
    border-color: #409eff;
}

:deep(.el-input.is-invalid .el-input__wrapper),
:deep(.el-input.is-invalid .el-textarea__inner) {
    border-color: #f56c6c;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}
</style>
