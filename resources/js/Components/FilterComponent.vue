<template>
    <form @submit.prevent="applyFilter" class="col-md-12">
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div
                        v-for="(field, index) in filterFields"
                        :key="index"
                        class="col-md-3 mb-3"
                    >
                        <template v-if="field.type === 'text'">
                            <el-input
                                type="text"
                                v-model="filters[field.key]"
                                :placeholder="field.placeholder"
                            />
                        </template>
                        <template v-else-if="field.type === 'select'">
                            <el-select
                                v-model="filters[field.key]"
                                :placeholder="field.placeholder"
                                filterable

                                :size="'default'"
                                clearable
                            >
                                <el-option
                                    v-for="option in field.options"
                                    :key="option.value"
                                    :label="option.label"
                                    :value="option.value"
                                />
                            </el-select>
                        </template>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="col-md-12 d-flex gap-3">
                    <el-button
                        type="primary"
                        @click="applyFilter"
                        class="w-100"
                        :icon="Search"
                    />
                    <el-button
                        type="info"
                        class=" w-100"
                        plain
                        @click="clearFilters"
                    >
                        <i class="bi bi-x-circle"></i>
                    </el-button>
                </div>
            </div>



        </div>
    </form>
</template>

<script setup>
import { reactive } from "vue";
import { Search, Delete } from "@element-plus/icons-vue";

const props = defineProps({
    filterFields: {
        type: Array,
        required: true,
    },
    initialFilters: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["update:filters"]);

const filters = reactive({ ...props.initialFilters });

const applyFilter = () => {
    emit("update:filters", { ...filters });
};

const clearFilters = () => {
    Object.keys(filters).forEach((key) => {
        filters[key] = "";
    });
    emit("update:filters", { ...filters });
};
</script>

<style scoped>
input,
select {
    height: calc(2.25rem + 2px);
}

button {
    height: calc(2.25rem + 2px);
}

form .form-control,
form .form-select,
form button {
    margin-right: 0.5rem;
}

form .form-control:last-child,
form .form-select:last-child,
form button:last-child {
    margin-right: 0;
}

.el-select {
    --el-select-input-height: 32px !important;
}

.el-select-dropdown__item {
    text-align: start;
    padding: 0 12px;
}

.el-select-dropdown {
    min-width: 200px !important;
}

.el-button {
    --el-button-size: 32px !important;
    font-weight: 500;
}

.el-button--primary {
    --el-button-bg-color: var(--el-color-primary) !important;
    --el-button-border-color: var(--el-color-primary) !important;
    --el-button-hover-bg-color: var(--el-color-primary-light-3) !important;
    --el-button-hover-border-color: var(--el-color-primary-light-3) !important;
}
</style>
