<template>
    <AuthenticatedLayout>
        <!-- breadcrumb -->
        <div class="pagetitle row">
            <BreadcrumbComponent
                :pageTitle="$t('faqs')"
                :mainRoute="'faqs.index'"
                :subTitle="$t('add_new')"
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
                            <h5 class="card-title">
                                {{
                                    isEdit ? $t("update_faq") : $t("create_faq")
                                }}
                            </h5>
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
                                        {{ isEdit ? $t("update") : $t("save") }}
                                    </el-button>
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
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { ElMessage } from "element-plus";
import { useI18n } from "vue-i18n";
import settings from "@/src/config/settings";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";

const { t } = useI18n();
const supportedLanguages = settings.supportedLanguages;
const props = defineProps({
    faq: Object,
    max_order: {
        type: [Number],
        required: false,
    },
});

const isEdit = computed(() => !!props.faq);
const formRef = ref(null);

const form = useForm({
    translations: supportedLanguages.reduce((acc, lang) => {
        acc[lang] = {
            question:
                props.faq?.translations?.find((t) => t.locale === lang)
                    ?.question || "",
            answer:
                props.faq?.translations?.find((t) => t.locale === lang)
                    ?.answer || "",
        };
        return acc;
    }, {}),
    is_active: props.faq?.is_active ?? true,
    sort_order: props.max_order ?? 0,
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

        const action = isEdit.value
            ? () => form.put(route("faqs.update", props.faq.id))
            : () => form.post(route("faqs.store"));

        action().then(() => {
            ElMessage({
                type: "success",
                message: t(
                    isEdit.value
                        ? "updated_successfully"
                        : "created_successfully"
                ),
            });
        });
    });
};
</script>

<style scoped>
.card {
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
}

.required:after {
    content: "*";
    color: #f56c6c;
    margin-right: 4px;
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

h5 {
    color: #606266;
    font-size: 1rem;
    margin-bottom: 1rem;
}
</style>
