<template>
    <AuthenticatedLayout>
        <div class="pagetitle">
            <h1>{{ $t("settings") }}</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <Link class="nav-link" :href="route('settings.index')">
                            {{ $t("Home") }}
                        </Link>
                    </li>
                    <li class="breadcrumb-item active">{{ $t("settings") }}</li>
                    <li class="breadcrumb-item active">{{ $t("edit") }}</li>
                </ol>
            </nav>
        </div>
        <!-- End breadcrumb -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $t("edit_settings_info") }}
                            </h5>
                            <form @submit.prevent="submit">
                                <div
                                    v-for="(groupSettings, group) in settings"
                                    :key="group"
                                    class="mb-4"
                                >
                                    <!-- Group Title -->
                                    <h2 class="mb-3">
                                        {{ $t("groups." + group) }}
                                    </h2>
                                    <div class="row">
                                        <!-- Group Settings -->
                                        <div
                                            v-for="setting in groupSettings"
                                            :key="setting.key"
                                            class="col-md-6 mb-3"
                                        >
                                            <label
                                                :for="setting.key"
                                                class="form-label"
                                            >
                                                {{ $t("keys." + setting.key) }}
                                            </label>

                                            <template
                                                v-if="
                                                    setting.key ===
                                                    'commission_type'
                                                "
                                            >
                                                <div class="d-flex gap-4">
                                                    <el-radio-group
                                                        v-model="
                                                            form[setting.key]
                                                        "
                                                    >
                                                        <el-radio
                                                            label="percentage"
                                                            >{{
                                                                $t("percentage")
                                                            }}</el-radio
                                                        >
                                                        <el-radio
                                                            label="fixed"
                                                            >{{
                                                                $t("fixed")
                                                            }}</el-radio
                                                        >
                                                    </el-radio-group>
                                                </div>
                                            </template>


                                            

                                            <!-- باقي أنواع الحقول -->
                                            <component
                                                v-else
                                                :is="
                                                    getComponentType(
                                                        setting.type
                                                    )
                                                "
                                                v-model="form[setting.key]"
                                                :id="setting.key"
                                                :placeholder="
                                                    $t(
                                                        'placeholders.' +
                                                            setting.key
                                                    ) || 'Enter ' + setting.key
                                                "
                                            />

                                            <div
                                                v-if="errors[setting.key]"
                                                class="text-danger mt-1"
                                            >
                                                {{ errors[setting.key] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="d-flex justify-content-end gap-2 mt-3"
                                >
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        {{ $t("save") }}
                                    </button>
                                </div>
                            </form>
                            <!-- End Edit User From -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm(
    Object.fromEntries(
        Object.entries(props.settings).flatMap(([group, groupSettings]) =>
            groupSettings.map((setting) => [setting.key, setting.value])
        )
    )
);

const errors = ref({});

const getComponentType = (type) => {
    switch (type) {
        case "text":
            return "el-input";
        case "textarea":
            return "el-input";
        case "number":
            return "el-input-number";
        case "url":
            return "el-input";
        case "file":
            return "el-upload";
        case "select":
            return "el-select";
        case "radio":
            return "el-radio-group";
        default:
            return "el-input";
    }
};

const submit = () => {
    form.post(route("settings.update"), {
        onError: (e) => {
            errors.value = e;
        },
    });
};
</script>

<style scoped>
.el-radio {
    margin-right: 20px;
}
.el-radio-group {
    display: flex;
    gap: 20px;
}
</style>
