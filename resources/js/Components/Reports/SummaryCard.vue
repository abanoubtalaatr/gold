<template>
    <el-card shadow="hover" :body-style="{ padding: '20px' }">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm mb-1">{{ title }}</p>
                <p class="text-2xl font-bold">{{ value }}</p>
            </div>
            <el-icon :class="[`text-${color}-500 text-3xl`]">
                <component :is="iconComponent" />
            </el-icon>
        </div>
    </el-card>
</template>

<script setup>
import { computed } from "vue";
import {
    UserFilled,
    Money,
    StarFilled,
    ShoppingCartFull,
} from "@element-plus/icons-vue";

const props = defineProps({
    title: String,
    value: [String, Number],
    color: {
        type: String,
        default: "blue",
    },
    icon: {
        type: String,
        default: "UserFilled",
    },
});

const iconComponent = computed(() => {
    const icons = {
        UserFilled,
        Money,
        StarFilled,
        ShoppingCartFull,
    };
    return icons[props.icon];
});
</script>
