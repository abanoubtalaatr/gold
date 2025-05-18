<template>
    <div class="chart-container">
        <canvas ref="chartRef"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import Chart from "chart.js/auto";
import { useI18n } from "vue-i18n";

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
    height: {
        type: Number,
        default: 300,
    },
});

const { t } = useI18n();
const chartRef = ref(null);
let chart = null;

const colors = [
    "rgba(99, 102, 241, 0.8)", // Indigo
    "rgba(52, 211, 153, 0.8)", // Green
    "rgba(248, 113, 113, 0.8)", // Red
    "rgba(251, 191, 36, 0.8)", // Yellow
    "rgba(96, 165, 250, 0.8)", // Blue
    "rgba(167, 139, 250, 0.8)", // Purple
    "rgba(244, 114, 182, 0.8)", // Pink
    "rgba(251, 191, 36, 0.8)", // Amber
    "rgba(96, 165, 250, 0.8)", // Light Blue
    "rgba(129, 140, 248, 0.8)", // Light Indigo
];

const createChart = () => {
    const ctx = chartRef.value.getContext("2d");
    const total = props.data
        .map((item) => item.count)
        .reduce((a, b) => a + b, 0);

    chart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: props.data.map((item) => item.name),
            datasets: [
                {
                    data: props.data.map((item) => item.count),
                    backgroundColor: colors,
                    borderColor: colors.map((color) =>
                        color.replace("0.8", "1")
                    ),
                    borderWidth: 1,
                    hoverOffset: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: "60%",
            plugins: {
                legend: {
                    position: "right",
                    rtl: true,
                    labels: {
                        font: {
                            family: "Tajawal",
                        },
                        padding: 20,
                        generateLabels: (chart) => {
                            const data = chart.data;
                            return data.labels.map((label, i) => ({
                                text: `${label} (${Math.round(
                                    (data.datasets[0].data[i] / total) * 100
                                )}%)`,
                                fillStyle: data.datasets[0].backgroundColor[i],
                                strokeStyle: data.datasets[0].borderColor[i],
                                lineWidth: 1,
                                hidden: false,
                                index: i,
                            }));
                        },
                    },
                },
                tooltip: {
                    rtl: true,
                    titleFont: {
                        family: "Tajawal",
                    },
                    bodyFont: {
                        family: "Tajawal",
                    },
                    callbacks: {
                        label: (context) => {
                            const value = context.raw;
                            const percentage = ((value / total) * 100).toFixed(
                                1
                            );
                            return `${context.label}: ${value} (${percentage}%)`;
                        },
                    },
                },
            },
        },
    });
};

watch(
    () => props.data,
    () => {
        if (chart) {
            chart.destroy();
        }
        createChart();
    },
    { deep: true }
);

onMounted(() => {
    createChart();
});
</script>

<style scoped>
.chart-container {
    position: relative;
    height: v-bind(height + "px");
}
</style>
