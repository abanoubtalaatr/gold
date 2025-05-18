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

                                <!-- Image Upload -->
                                <div class="mb-3 col-md-6">
                                    <el-form-item :label="$t('image')">
                                        <el-upload
                                            action=""
                                            :auto-upload="false"
                                            :on-change="handleFileChange"
                                            list-type="picture-card"
                                        >
                                            <img
                                                v-if="props.banner.image"
                                                :src="props.banner.image_url"
                                                class="img-thumbnail"
                                                width="80"
                                            />
                                        </el-upload>
                                        <div
                                            v-if="form.errors.image"
                                            class="error-message"
                                        >
                                            {{ form.errors.image }}
                                        </div>
                                    </el-form-item>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-end">
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

const isEdit = computed(() => !!props.banner);
const fileList = ref([]);
const formRef = ref(null);

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

onMounted(() => {
    if (form.image) {
        fileList.value = [
            {
                name: "Current Image",
                url: `/storage/${form.image}`,
            },
        ];
    }
});

const handleFileChange = (file) => {
    form.image = file.raw;
    fileList.value = [file];
};

const submitForm = () => {
    form.post(route("banners.update", props.banner.id), {
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
