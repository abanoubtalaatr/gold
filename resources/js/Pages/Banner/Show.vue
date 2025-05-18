<template>
    <AuthenticatedLayout>
        <!-- Breadcrumb -->
        <div class="pagetitle row">
            <BreadcrumbComponent
                :pageTitle="`${$t('banners')} - ${banner.title}`"
                :mainRoute="'banners.index'"
                :homeLabel="$t('home')"
            />
        </div>
        <!-- End Breadcrumb -->

        <!-- Banner Details -->
        <section class="section dashboard">
            <div class="card">
                <div class="card-header">
                    <h4>{{ banner.title }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Image -->
                        <div class="col-md-6">
                            <img
                                v-if="banner.image_url"
                                :src="banner.image_url"
                                class="img-fluid rounded"
                                :alt="$t('banner.fields.image')"
                            />
                        </div>

                        <!-- Details -->
                        <div class="col-md-6">
                            <div
                                v-for="lang in supportedLanguages"
                                :key="lang"
                                class="mb-4"
                            >
                                <h4>{{ $t(lang) }}</h4>
                                <div class="mb-3">
                                    <strong>{{ $t("title") }}:</strong>
                                    <p>{{ banner.translations[lang].title }}</p>
                                </div>
                                <div class="mb-3">
                                    <strong>{{ $t("description") }}:</strong>
                                    <p>
                                        {{
                                            banner.translations[lang]
                                                .description
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <strong>{{ $t("sort_order") }}:</strong>
                                <p>{{ banner.sort_order }}</p>
                            </div>
                            <div class="mb-3">
                                <strong>{{ $t("status") }}:</strong>
                                <p>
                                    {{
                                        banner.is_active
                                            ? $t("active")
                                            : $t("inactive")
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BreadcrumbComponent from "@/Components/BreadcrumbComponent.vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const props = defineProps({
    banner: Object,
});
</script>
