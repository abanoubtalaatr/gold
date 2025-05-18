<template>
    <AuthenticatedLayout>
        <div class="pagetitle mb-4">
            <h1>{{ $t("edit_page") }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link class="nav-link" :href="route('dashboard')">{{
                            $t("home")
                        }}</Link>
                    </li>
                    <li class="breadcrumb-item active">{{ $t("pages") }}</li>
                    <li class="breadcrumb-item active">{{ $t("edit") }}</li>
                </ol>
            </nav>
        </div>

        <section class="section dashboard">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm p-4 rounded">
                        <div class="card-body">
                            <!-- Form Start -->
                            <form @submit.prevent="update" class="row g-3">
                                <!-- General Information -->
                                <div class="mb-4">
                                    <h5 class="text-primary mb-3">
                                        {{ $t("general_information") }}
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label
                                                class="form-label text-secondary"
                                                >{{ $t("slug") }}</label
                                            >
                                            <el-input
                                                v-model="form.slug"
                                                :placeholder="$t('slug')"
                                                disabled
                                            ></el-input>
                                            <small class="text-danger">{{
                                                form.errors.slug
                                            }}</small>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <!-- Image Upload -->
                                            <div class="mb-3">
                                                <el-form-item
                                                    :label="$t('image')"
                                                >
                                                    <el-upload
                                                        action=""
                                                        :auto-upload="false"
                                                        :on-change="
                                                            handleFileChange
                                                        "
                                                        list-type="picture-card"
                                                    >
                                                        <img
                                                            v-if="
                                                                props.page
                                                                    .image_url
                                                            "
                                                            :src="
                                                                props.page
                                                                    .image_url
                                                            "
                                                            class="img-thumbnail"
                                                            width="80"
                                                        />
                                                    </el-upload>
                                                </el-form-item>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Translations -->
                                <div class="mb-4">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label
                                                class="form-label text-secondary"
                                                >{{ $t("title_en") }}</label
                                            >
                                            <el-input
                                                v-model="
                                                    form.translations['en']
                                                        .title
                                                "
                                                :placeholder="
                                                    $t('title') + ' (en)'
                                                "
                                            ></el-input>
                                        </div>
                                        <div class="col-md-6">
                                            <label
                                                class="form-label text-secondary"
                                                >{{ $t("title_ar") }}</label
                                            >
                                            <el-input
                                                v-model="
                                                    form.translations['ar']
                                                        .title
                                                "
                                                :placeholder="
                                                    $t('title') + ' (ar)'
                                                "
                                            ></el-input>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="form-label text-secondary"
                                                >{{
                                                    $t("description_en")
                                                }}</label
                                            >
                                            <div class="editor-wrapper">
                                                <quill-editor
                                                    v-model:content="
                                                        form.translations['en']
                                                            .description
                                                    "
                                                    contentType="html"
                                                    :options="editorOptions"
                                                />
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label
                                                class="form-label text-secondary"
                                                >{{
                                                    $t("description_ar")
                                                }}</label
                                            >
                                            <div class="editor-wrapper">
                                                <quill-editor
                                                    v-model:content="
                                                        form.translations['ar']
                                                            .description
                                                    "
                                                    contentType="html"
                                                    :options="editorOptions"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sections -->
                                <div
                                    v-if="form.sections.length > 0"
                                    class="mb-4"
                                >
                                    <h5 class="text-primary mb-3">
                                        {{ $t("sections") }}
                                    </h5>
                                    <div
                                        class="accordion"
                                        id="sectionsAccordion"
                                    >
                                        <div
                                            v-for="(
                                                section, index
                                            ) in form.sections"
                                            :key="index"
                                            class="accordion-item mb-3"
                                        >
                                            <h2
                                                class="accordion-header"
                                                :id="'heading' + index"
                                            >
                                                <button
                                                    class="accordion-button"
                                                    type="button"
                                                    data-bs-toggle="collapse"
                                                    :data-bs-target="
                                                        '#collapse' + index
                                                    "
                                                    aria-expanded="true"
                                                    :aria-controls="
                                                        'collapse' + index
                                                    "
                                                >
                                                    {{ $t(section.type) }}
                                                </button>
                                            </h2>
                                            <div
                                                :id="'collapse' + index"
                                                class="accordion-collapse collapse"
                                                :class="{ show: index === 0 }"
                                                :aria-labelledby="
                                                    'heading' + index
                                                "
                                                data-bs-parent="#sectionsAccordion"
                                            >
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div
                                                            class="col-md-12 mb-3"
                                                        >
                                                           <!-- Image Upload -->
                                            <div class="mb-3">
                                                <el-form-item
                                                    :label="$t('image')"
                                                >
                                                  <el-upload
                                                    action=""
                                                    :auto-upload="false"
                                                    :on-change="(file) => handleSectionImageChange(section, file)"
                                                    list-type="picture-card"
                                                >
                                                    <img
                                                        v-if="section.image_url || section.image"
                                                        :src="section.image_url || `/storage/${section.image}`"
                                                        class="img-thumbnail"
                                                        width="80"
                                                    />
                                                    <div v-else class="upload-placeholder">
                                                        <i class="el-icon-plus"></i>
                                                        <div>{{ $t("upload_image") }}</div>
                                                    </div>
                                                </el-upload>

                                                </el-form-item>
                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-md-6 mb-3"
                                                        >
                                                            <label
                                                                class="form-label text-secondary"
                                                                >{{
                                                                    $t(
                                                                        "title_en"
                                                                    )
                                                                }}</label
                                                            >
                                                            <el-input
                                                                v-model="
                                                                    section
                                                                        .translations[
                                                                        'en'
                                                                    ].title
                                                                "
                                                                :placeholder="
                                                                    $t(
                                                                        'title'
                                                                    ) + ' (en)'
                                                                "
                                                            ></el-input>
                                                        </div>
                                                        <div
                                                            class="col-md-6 mb-3"
                                                        >
                                                            <label
                                                                class="form-label text-secondary"
                                                                >{{
                                                                    $t(
                                                                        "title_ar"
                                                                    )
                                                                }}</label
                                                            >
                                                            <el-input
                                                                v-model="
                                                                    section
                                                                        .translations[
                                                                        'ar'
                                                                    ].title
                                                                "
                                                                :placeholder="
                                                                    $t(
                                                                        'title'
                                                                    ) + ' (ar)'
                                                                "
                                                            ></el-input>
                                                        </div>
                                                        <div
                                                            class="col-md-6 mb-3"
                                                        >
                                                            <label
                                                                class="form-label text-secondary"
                                                                >{{
                                                                    $t(
                                                                        "description_en"
                                                                    )
                                                                }}</label
                                                            >
                                                            <div
                                                                class="editor-wrapper"
                                                            >
                                                                <quill-editor
                                                                    v-model:content="
                                                                        section
                                                                            .translations[
                                                                            'en'
                                                                        ]
                                                                            .description
                                                                    "
                                                                    contentType="html"
                                                                    :options="
                                                                        editorOptions
                                                                    "
                                                                />
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-md-6 mb-3"
                                                        >
                                                            <label
                                                                class="form-label text-secondary"
                                                                >{{
                                                                    $t(
                                                                        "description_ar"
                                                                    )
                                                                }}</label
                                                            >
                                                            <div
                                                                class="editor-wrapper"
                                                            >
                                                                <quill-editor
                                                                    v-model:content="
                                                                        section
                                                                            .translations[
                                                                            'ar'
                                                                        ]
                                                                            .description
                                                                    "
                                                                    contentType="html"
                                                                    :options="
                                                                        editorOptions
                                                                    "
                                                                />
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-center mt-5">
                                    <button
                                        type="submit"
                                        class="btn btn-primary px-4 py-2"
                                        v-bind:disabled="show_loader"
                                    >
                                        {{ $t("update") }}
                                        <i
                                            class="bi bi-save"
                                            v-if="!show_loader"
                                        ></i>
                                        <span
                                            class="spinner-border spinner-border-sm"
                                            role="status"
                                            aria-hidden="true"
                                            v-if="show_loader"
                                        ></span>
                                    </button>
                                </div>
                            </form>
                            <!-- Form End -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, onMounted, nextTick, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import settings from "@/src/config/settings";

import { ElMessage } from "element-plus";
import SingleImageUpload from "@/Components/SingleImageUpload.vue";
import { Link } from "@inertiajs/vue3";

const supportedLanguages = settings.supportedLanguages;
const avatarPreview = ref(null);
const props = defineProps({
    page: Object,
});

const show_loader = ref(false);
const fileList = ref([]);
const editorOptions = {
    theme: "snow",
    placeholder: "Type here...",
    modules: {
        toolbar: [
            ["bold", "italic", "underline"],
            [{ list: "ordered" }, { list: "bullet" }],
            ["link"],
            ["clean"],

            ["code-block"],
            [{ header: [1, 2, 3, 4, 5, 6, false] }],
            [{ size: ["small", false, "large", "huge"] }],
            [{ color: [] }],
            [{ align: [] }],
            [{ direction: "rtl" }],
            [{ font: [] }],
            [{ script: "sub" }, { script: "super" }],
            [{ indent: "-1" }, { indent: "+1" }],
            [{ background: [] }],

            ["blockquote", "code-block"],
        ],
    },
};

const form = useForm({
    slug: props.page?.slug || "",
    image: null,
    translations: {},
    sections: (props.page?.sections || []).map(section => {
        const translations = {};
        if (section.translations && Array.isArray(section.translations)) {
            section.translations.forEach(trans => {
                translations[trans.locale] = {
                    title: trans.title || '',
                    description: trans.description || ''
                };
            });
        }

        if (!translations['en']) {
            translations['en'] = { title: '', description: '' };
        }
        if (!translations['ar']) {
            translations['ar'] = { title: '', description: '' };
        }

        return {
            id: section.id,
            type: section.type,
            image: null,
            image_url: section.image_url,
            translations: translations
        };
    })
});

const pageTranslations = {};
if (props.page?.translations && Array.isArray(props.page.translations)) {
    props.page.translations.forEach(trans => {
        pageTranslations[trans.locale] = {
            title: trans.title || '',
            description: trans.description || ''
        };
    });
}

form.translations = {
    en: pageTranslations['en'] || { title: '', description: '' },
    ar: pageTranslations['ar'] || { title: '', description: '' }
};

const handleFileChange = (file) => {
    if (file && file.raw) {
        avatarPreview.value = URL.createObjectURL(file.raw);
        form.image = file.raw;
    }
};

const handleSectionImageChange = (section, file) => {
      if (file && file.raw) {
        section.image = file.raw;
    }

};


// Submit Form
const update = () => {
    show_loader.value = true;

    const formData = {
        ...form,
        sections: form.sections.map(section => ({
            id: section.id,
            type: section.type,
            image: section.image,
            translations: {
                en: section.translations.en,
                ar: section.translations.ar
            }
        }))
    };

    form.post(route("pages.update", { id: props.page.id }), {
        onSuccess: () => {
            ElMessage({
                type: "success",
                message: "Page updated successfully!"
            });
        },
        onError: (errors) => {
            console.error('Update errors:', errors);
            ElMessage({
                type: "error",
                message: "There was an error updating the page."
            });
        },
        onFinish: () => {
            show_loader.value = false;
        }
    });
};

// Reset Form
const resetForm = () => {
    form.reset();
    fileList.value = [];
};

onMounted(() => {
    form.translations["en"].description =
        props.page?.translations?.en?.description || "";
    form.translations["ar"].description =
        props.page?.translations?.ar?.description || "";

    if (props.page.image) {
        fileList.value = [
            {
                name: "Current Image",
                url: `/storage/${props.page.image}`,
            },
        ];
    }
});

// watch(
//     () => form.translations['en'].description,
//     (newValue) => {
//         form.translations['en'].description = newValue;

//         console.log("Updated description sent to the model:", newValue);
//     }
// );
</script>

<style scoped>
.editor-class {
    min-height: 150px;
    margin-bottom: 15px;
}

/* Add spacing below each section */
.mb-4 {
    margin-bottom: 1.5rem;
}
.form-input {
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 10px;
}

.img-thumbnail {
    max-width: 120px;
    max-height: 120px;
    border-radius: 6px;
    border: 1px solid #ddd;
}

.preview-container {
    text-align: center;
}

.form-layout {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.section-card {
    border: 1px solid #ddd;
    padding: 1rem;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-top: 1rem;
}

.img-thumbnail {
    max-width: 100px;
    max-height: 100px;
    border-radius: 6px;
    border: 1px solid #ddd;
}

.preview-container {
    text-align: center;
}

button[disabled] {
    opacity: 0.7;
    cursor: not-allowed;
}

/* تحسين أبعاد المحرر النصي */
.editor-class {
    min-height: 200px;
    max-height: 300px;
    overflow: auto;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 10px;
    background-color: #fff;
}

/* تحسين تباعد الحقول */
.mb-4 {
    margin-bottom: 1.5rem;
}

/* تحسين الكولابس */
.accordion-body {
    padding: 1.5rem;
    background-color: #f9f9f9;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* تحسين شكل الصور */
.img-thumbnail {
    max-width: 100px;
    max-height: 100px;
    border-radius: 6px;
    border: 1px solid #ddd;
}

.preview-container {
    text-align: center;
    margin-top: 10px;
}

/* زر التحديث */
button[disabled] {
    opacity: 0.7;
    cursor: not-allowed;
}

/* تحسين عرض الكولابس */
.accordion-item {
    border: 1px solid #ddd;
    border-radius: 6px;
    margin-bottom: 1rem;
    overflow: hidden;
}

/* التباعد بين الحقول */
.form-group {
    margin-bottom: 1rem;
}
</style>
