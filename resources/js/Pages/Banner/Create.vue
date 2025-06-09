<template>
    <AuthenticatedLayout>
        <!-- breadcrumb -->
        <div class="pagetitle row">
            <BreadcrumbComponent
                :pageTitle="$t('banners')"
                :mainRoute="'banners.index'"
                :subTitle="$t('add_new')"
                :isIndexPage="false"
                :showMainRoute="false"
                :homeLabel="$t('home')"
            />
        </div>
        <!-- End breadcrumb -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $t("create") }}
                            </h5>
                            <form @submit.prevent="submitForm" class="row g-3">
                                <!-- Title and Description Translations -->
                                <div
                                    class="col-md-6"
                                    v-for="lang in supportedLanguages"
                                    :key="lang"
                                >
                                    <el-form-item
                                        :label="
                                            $t('title') + ' (' + $t(lang) + ')'
                                        "
                                    >
                                        <el-input
                                            v-model="
                                                form.translations[lang].title
                                            "
                                            :placeholder="$t('title')"
                                        />
                                        <div
                                            v-if="
                                                form.errors[
                                                    `translations.${lang}.title`
                                                ]
                                            "
                                            class="error-message"
                                        >
                                            {{
                                                form.errors[
                                                    `translations.${lang}.title`
                                                ]
                                            }}
                                        </div>
                                    </el-form-item>

                                    <el-form-item
                                        :label="
                                            $t('description') +
                                            ' (' +
                                            $t(lang) +
                                            ')'
                                        "
                                    >
                                        <el-input
                                            type="textarea"
                                            v-model="
                                                form.translations[lang]
                                                    .description
                                            "
                                            :placeholder="$t('description')"
                                            :rows="3"
                                        />
                                        <div
                                            v-if="
                                                form.errors[
                                                    `translations.${lang}.description`
                                                ]
                                            "
                                            class="error-message"
                                        >
                                            {{
                                                form.errors[
                                                    `translations.${lang}.description`
                                                ]
                                            }}
                                        </div>
                                    </el-form-item>
                                </div>

                                <!-- Sort Order -->
                                <div class="mb-3 col-md-6">
                                    <el-form-item :label="$t('sort_order')">
                                        <el-input
                                            v-model="form.sort_order"
                                            type="number"
                                            :placeholder="$t('sort_order')"
                                        />
                                        <div
                                            v-if="form.errors.sort_order"
                                            class="error-message"
                                        >
                                            {{ form.errors.sort_order }}
                                        </div>
                                    </el-form-item>
                                </div>

                                <!-- Image Upload -->
                                <div class="mb-3 col-md-6">
                                    <el-form-item :label="$t('image')">
                                        <el-upload
                                            action=""
                                            :auto-upload="false"
                                            :on-change="handleFileChange"
                                            list-type="picture-card"
                                            :limit="1"
                                        >
                                            <i class="el-icon-plus"></i>
                                        </el-upload>
                                        <div
                                            v-if="form.errors.image"
                                            class="error-message"
                                        >
                                            {{ form.errors.image }}
                                        </div>
                                    </el-form-item>
                                </div>

                                <!-- Active Status -->
                                <div class="mb-3 col-md-6">
                                    <el-form-item :label="$t('is_active')">
                                        <el-switch
                                            v-model="form.is_active"
                                            :active-text="$t('active')"
                                            :inactive-text="$t('inactive')"
                                        />
                                    </el-form-item>
                                </div>

                                <div class="text-center col-12">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        :disabled="form.processing"
                                    >
                                        {{ $t("save") }}
                                    </button>
                                </div>
                            </form>
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
import { Link } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import { ElMessage } from "element-plus";
import { useI18n } from "vue-i18n";
import settings from "@/src/config/settings";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";

const { t } = useI18n();
const supportedLanguages = settings.supportedLanguages;

// Props
const props = defineProps({
    banner: Object,
});

const form = useForm({
    image: null,
    sort_order: 0,
    is_active: false,
    translations: supportedLanguages.reduce((acc, lang) => {
        acc[lang] = {
            title: "",
            description: "",
        };
        return acc;
    }, {}),
});

const fileList = ref([]);

// Handle File Upload
const handleFileChange = (file) => {
    form.image = file.raw;
    fileList.value = [file];
};

// Submit Form
const submitForm = () => {
    form.post(route("banners.store"), {
        onSuccess: () => {
            ElMessage({
                type: "success",
                message: t("created_successfully"),
            });
        },
        onError: () => {
            ElMessage({
                type: "error",
                message: t("error_creating"),
            });
        },
    });
};
</script>

<style scoped>
.error-message {
    color: #f56565;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
</style>
