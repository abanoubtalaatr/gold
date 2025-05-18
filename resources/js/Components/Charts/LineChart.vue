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

console.log(props.data);

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
    if (!chartRef.value) return;

    const ctx = chartRef.value.getContext("2d");

    chart = new Chart(ctx, {
        type: "line",
        data: {
            labels: props.data.labels.map(formatDate),
            datasets: [
                {
                    label: t("reports.charts.subscriptions"),
                    data: props.data.subscriptions_values,
                    borderColor: "#6366F1", // لون الاشتراكات (أزرق)
                    backgroundColor: "rgba(99, 102, 241, 0.1)",
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                },
                {
                    label: t("reports.charts.contracts"),
                    data: props.data.contracts_values,
                    borderColor: "#F59E0B", // لون العقود (برتقالي)
                    backgroundColor: "rgba(245, 158, 11, 0.1)",
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
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
                    callbacks: {
                        label: (context) => {
                            return new Intl.NumberFormat("ar-SA", {
                                style: "currency",
                                currency: "SAR",
                            }).format(context.raw);
                        },
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
                        font: {
                            family: "Tajawal",
                        },
                        callback: (value) => {
                            return new Intl.NumberFormat("ar-SA", {
                                style: "currency",
                                currency: "SAR",
                                notation: "compact",
                            }).format(value);
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
