<template>
    <AuthenticatedLayout>
        <!-- breadcrumb-->
        <div class="pagetitle">
            <h1>{{ $t("roles") }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link class="nav-link" :href="route('dashboard')">
                            {{ $t("Home") }}
                        </Link>
                    </li>
                    <li class="breadcrumb-item active">{{ $t("roles") }}</li>
                    <li class="breadcrumb-item active">{{ $t("edit") }}</li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb-->

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $t("edit") }}</h5>

                            <!-- General Form Elements -->
                            <form @submit.prevent="update" class="row g-3">
                                <div class="row mb-3">


                                    <div class="col-sm-10">
                            <div v-for="(language, index) in supportedLanguages" :key="index">
                            <el-form-item :label="$t('name') + ' (' + $t(language) + ')'" :error="form.errors[`translations.${language}.name`]">
                                <el-input
                                v-model="form.translations[language].name "
                                :placeholder="$t('name') + ' (' + $t(language) + ')'"
                                />
                            </el-form-item>
                            </div>

                                    </div>
                                </div>

                                <div class="text-center">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                        v-bind:disabled="show_loader"
                                    >
                                        {{ $t("update") }} &nbsp;
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
                            <!--  From -->
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
import InputError from "@/Components/InputError.vue";
import { ref } from "vue";
import { Link } from '@inertiajs/vue3';

import settings from '@/src/config/settings';

const supportedLanguages = settings.supportedLanguages;

const props = defineProps({
    role: Object,
});

const show_loader = ref(false);



const form = useForm({
    name: props.role.name,
    translations: {},
});


const translationsMap = (props.role.translations || []).reduce((acc, translation) => {
  acc[translation.locale] = { name: translation.name };
  return acc;
}, {});



console.log( props.role);
    supportedLanguages.forEach((lang) => {
    form.translations[lang] = translationsMap[lang] || { name: props.role.name };
    });


const update = () => {
    show_loader.value = true;
    form.put(route("roles.update", { role: props.role.id }), {
        onSuccess: () => {
            show_loader.value = false;
        },
        onError: () => {
            show_loader.value = false;
        },
    });
};
</script>
