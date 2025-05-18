<template>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium">{{ title }}</h3>
            <div class="flex items-center space-x-4">
                <el-select
                    v-model="selectedPeriod"
                    size="small"
                    @change="$emit('period-changed', selectedPeriod)"
                >
                    <el-option
                        v-for="period in periods"
                        :key="period.value"
                        :label="period.label"
                        :value="period.value"
                    />
                </el-select>
            </div>
        </div>

        <div class="h-80">
            <apexchart
                :type="chartType"
                :options="chartOptions"
                :series="series"
                height="100%"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import VueApexCharts from "vue3-apexcharts";

const props = defineProps({
    title: String,
    chartType: {
        type: String,
        default: "line",
    },
    labels: {
        type: Array,
        default: () => [],
    },
    data: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["period-changed"]);

const selectedPeriod = ref("month");
const periods = [
    { label: "آخر 7 أيام", value: "week" },
    { label: "آخر 30 يوم", value: "month" },
    { label: "آخر 3 شهور", value: "quarter" },
    { label: "آخر سنة", value: "year" },
];

const chartOptions = computed(() => ({
    chart: {
        toolbar: { show: false },
        zoom: { enabled: false },
    },
    dataLabels: { enabled: false },
    stroke: {
        curve: "smooth",
        width: 2,
    },
    xaxis: {
        categories: props.labels,
        axisBorder: { show: false },
    },
    colors: ["#6366f1"],
    tooltip: {
        theme: "light",
        x: { show: true },
    },
}));

const series = computed(() => [
    {
        name: props.title,
        data: props.data,
    },
]);
</script>
