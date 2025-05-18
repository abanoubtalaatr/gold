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

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("ar-SA", {
        day: "numeric",
        month: "short",
    });
};

const createChart = () => {
    const ctx = chartRef.value.getContext("2d");

    chart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: props.data.labels.map(formatDate),
            datasets: [
                {
                    label: t("reports.charts.providers"),
                    data: props.data.providers,
                    backgroundColor: "rgba(52, 211, 153, 0.8)",
                    borderColor: "#34D399",
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: "index",
            },
            plugins: {
                legend: {
                    position: "top",
                    rtl: true,
                    labels: {
                        font: {
                            family: "Tajawal",
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
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            family: "Tajawal",
                        },
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [2, 2],
                    },
                    ticks: {
                        stepSize: 1,
                        font: {
                            family: "Tajawal",
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
    direction: ltr;
}
</style>
