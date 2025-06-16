<template>
    <AuthenticatedLayout>
        <!-- breadcrumb -->
        <div class="pagetitle row">
            <BreadcrumbComponent
                :pageTitle="$t('banners')"
                :mainRoute="'banners.index'"
                :subTitle="$t('edit')"
                :isIndexPage="false"
                :showMainRoute="true"
                :homeLabel="$t('home')"
            />
        </div>

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $t("edit") }}</h5>

                            <form @submit.prevent="submitForm" class="row g-3">
                                <!-- Title and Description Translations -->
                                <div
                                    class="col-md-6"
                                    v-for="lang in supportedLanguages"
                                    :key="lang"
                                >

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

                                <!-- Image Upload -->
                                <div class="mb-3 col-md-6">
                                    <el-form-item :label="$t('image')">
                                        <div v-if="props.banner.image" class="mb-2">
                                            <img
                                                :src="props.banner.image_url"
                                                class="img-thumbnail"
                                                width="80"
                                                alt="Current image"
                                            />
                                            <p class="text-sm text-gray-600 mt-1">{{ $t('current_image') }}</p>
                                        </div>
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

                                <!-- Submit Button -->
                                <div class="text-end col-12">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        :disabled="form.processing"
                                    >
                                        {{ $t("update") }}
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

const props = defineProps({
    banner: Object,
});

const fileList = ref([]);

const form = useForm({
    image: null,
    sort_order: props.banner?.sort_order || 0,
    is_active: props.banner?.is_active || false,
    translations: supportedLanguages.reduce((acc, lang) => {
        acc[lang] = {
            title:
                props.banner?.translations?.find((t) => t.locale === lang)
                    ?.title || "",
            description:
                props.banner?.translations?.find((t) => t.locale === lang)
                    ?.description || "",
        };
        return acc;
    }, {}),
});

const handleFileChange = (file) => {
    form.image = file.raw;
    fileList.value = [file];
};

const submitForm = () => {
    form.put(route("banners.update", props.banner.id), {
        onSuccess: () => {
            ElMessage({
                type: "success",
                message: t("updated_successfully"),
            });
        },
        onError: () => {
            ElMessage({
                type: "error",
                message: t("error_updating"),
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
