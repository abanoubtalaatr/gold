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
                                {{ isEdit ? $t("edit") : $t("create") }}
                            </h5>
                            <el-form
                                :model="form"
                                :rules="rules"
                                ref="formRef"
                                label-width="120px"
                                class="row g-3"
                            >
                                <!-- Title Translations -->
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

                                    <!-- Description field for each language -->
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

                                <!-- <el-form-item :label="$t('image')" class="col-md-6">
                  <el-upload
                    action=""
                    :file-list="fileList"
                    :limit="1"
                    accept="image/*"
                    :on-change="handleFileChange"
                    :auto-upload="false"
                    list-type="picture"
                  >
                    <el-button size="small" type="primary">{{ $t("upload_image") }}</el-button>
                    <div  class="el-upload__tip">
                      {{ $t("upload_image_tip") }}
                    </div>
                  </el-upload>
                </el-form-item> -->

                                <!-- Image Upload -->
                                <div class="mb-3 col-md-6">
                                    <el-form-item :label="$t('image')">
                                        <el-upload
                                            action=""
                                            :auto-upload="false"
                                            :on-change="handleFileChange"
                                            list-type="picture-card"
                                        >
                                        </el-upload>
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
        <pre>{{ props.banner }}</pre>
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
import SingleImageUpload from "@/Components/SingleImageUpload.vue";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";
const { t } = useI18n();
const supportedLanguages = settings.supportedLanguages;
// Props
const props = defineProps({
    banner: Object,
});

const isEdit = computed(() => !!props.banner);

const form = useForm({
    title: "",
    image: "",
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

if (isEdit.value) {
    console.log(props.banner);
    form.title = props.banner.title;
    form.image = props.banner.image;
    form.is_active = props.banner.is_active;
}

const fileList = ref([]);
const formRef = ref(null);
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
console.log(form.image);

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
            //resetForm();
        },
        onError: () => {
            ElMessage({
                type: "error",
                message: form.errors,
            });
        },
    });
};

// Reset Form
const resetForm = () => {
    form.reset();
    fileList.value = [];
    if (formRef.value) {
        formRef.value.clearValidate();
    }
};
</script>
