<script setup>
import { ref, watch, computed } from "vue";
import { router, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import { ElDatePicker, ElSelect, ElInput, ElButton } from "element-plus";
import debounce from "lodash/debounce";

const props = defineProps({
    transactions: Object,
    filters: Object,
    summary: Object,
    statuses: Array,
    paymentTypes: Array,
    transactionTypes: Array,
});

const filters = ref({
    search: props.filters.search || "",
    date_from: props.filters.date_from || "",
    date_to: props.filters.date_to || "",
    status: props.filters.status || "",
    payment_type: props.filters.payment_type || "",
    type: props.filters.type || "",
});
console.log(props.transactions);

watch(
    filters,
    debounce((value) => {
        router.get(route("admin.financial-reports.index"), value, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    }, 300),
    { deep: true }
);

const exportData = () => {
    window.open(
        route("admin.financial-reports.export", filters.value),
        "_blank"
    );
};

const expandedRows = ref(new Set());

const toggleDetails = (id) => {
    if (expandedRows.value.has(id)) {
        expandedRows.value.delete(id);
    } else {
        expandedRows.value.clear();
        expandedRows.value.add(id);
    }
};

const isExpanded = (id) => expandedRows.value.has(id);

const paginationLinks = computed(() => {
    if (!props.transactions.links) return [];

    return [
        {
            url: props.transactions.links.prev,
            label: "Previous",
            active: false,
        },
        ...Array.from(
            { length: props.transactions.meta.last_page },
            (_, i) => ({
                url: `${props.transactions.links.first.split("?")[0]}?page=${
                    i + 1
                }`,
                label: String(i + 1),
                active: i + 1 === props.transactions.meta.current_page,
            })
        ),
        {
            url: props.transactions.links.next,
            label: "Next",
            active: false,
        },
    ];
});
</script>

<template>
    <authenticated-layout :title="$t('financial_reports')">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $t('financial_reports') }}
                </h2>
                <el-button type="primary" @click="exportData">
                    {{ $t('export_data') }}
                </el-button>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">{{ $t('total_amount') }} </h3>
                <p class="text-2xl">{{ summary.total_amount }} {{ $t('sar') }} </p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">{{ $t('successful_payments') }} </h3>
                <p class="text-2xl text-green-600">
                    {{ summary.successful_payments }}
                </p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2">{{ $t('pending_payments') }} </h3>
                <p class="text-2xl text-yellow-600">
                    {{ summary.pending_payments }}
                </p>
            </div>
        </div>

        <!-- الفلاتر -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <el-input
                    v-model="filters.search"
                    :placeholder="$t('search') + '...'"
                    clearable
                />

                <el-date-picker
                    v-model="filters.date_from"
                    type="date"
                    :placeholder="$t('from')"
                    format="YYYY-MM-DD"
                    value-format="YYYY-MM-DD"
                />

                <el-date-picker
                    v-model="filters.date_to"
                    type="date"
                    :placeholder="$t('to')"
                    format="YYYY-MM-DD"
                    value-format="YYYY-MM-DD"
                />

                <el-select
                    v-model="filters.status"
                    :placeholder="$t('status')"
                    clearable
                >
                    <el-option
                        v-for="status in statuses"
                        :key="status.value"
                        :label="status.label"
                        :value="status.value"
                    />
                </el-select>

                <el-select
                    v-model="filters.payment_type"
                    :placeholder="$t('payment_type')"
                    clearable
                >
                    <el-option
                        v-for="type in paymentTypes"
                        :key="type.value"
                        :label="type.label"
                        :value="type.value"
                    />
                </el-select>

                <el-select
                    v-model="filters.type"
                    :placeholder="$t('transaction_type')"
                    clearable
                >
                    <el-option
                        v-for="type in transactionTypes"
                        :key="type.value"
                        :label="type.label"
                        :value="type.value"
                    />
                </el-select>
            </div>
        </div>

        <div
            class="bg-white rounded-lg shadow-[0_1px_3px_0_rgba(0,0,0,0.07)] overflow-hidden"
        >
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr class="border-b">
                                <th class="px-6 py-3 text-right">{{ $t('reference') }}</th>
                            <th class="px-6 py-3 text-right">{{ $t('type') }}</th>
                            <th class="px-6 py-3 text-right">{{ $t('amount') }}</th>
                            <th class="px-6 py-3 text-right">{{ $t('payment_type') }}</th>
                            <th class="px-6 py-3 text-right">{{ $t('status') }}</th>
                            <th class="px-6 py-3 text-right">{{ $t('created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template
                            v-for="transaction in transactions.data"
                            :key="transaction.id"
                        >
                            <tr
                                class="border-b hover:bg-gray-50 cursor-pointer transition-colors duration-150"
                                @click="toggleDetails(transaction.id)"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i
                                            class="fas"
                                            :class="
                                                isExpanded(transaction.id)
                                                    ? 'fa-chevron-down'
                                                    : 'fa-chevron-left'
                                            "
                                        ></i>
                                        {{ transaction.reference }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{
                                        transaction.type === "contract"
                                            ? $t('contract')
                                            : $t('subscription')
                                    }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ transaction.amount }} {{ $t('sar') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{
                                        transaction.payment_type === "card"
                                            ? $t('credit_card')
                                            : $t('bank_transfer')
                                    }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="{
                                            'px-3 py-1 rounded-full text-sm': true,
                                            'bg-green-100 text-green-800':
                                                transaction.status === 'paid',
                                            'bg-yellow-100 text-yellow-800':
                                                transaction.status ===
                                                'pending',
                                            'bg-red-100 text-red-800':
                                                transaction.status === 'failed',
                                        }"
                                    >
                                        {{
                                            transaction.status === "paid"
                                                ? $t('paid')
                                                : transaction.status ===
                                                  "pending"
                                                ? $t('pending')
                                                : $t('failed')
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    {{ transaction.created_at }}
                                </td>
                            </tr>

                            <tr v-show="isExpanded(transaction.id)">
                                <td colspan="6" class="px-6 py-4 bg-gray-50">
                                    <div class="mb-6">
                                        <h4
                                            class="text-lg font-semibold mb-4 text-gray-700 border-r-4 border-indigo-500 pr-3"
                                        >
                                            {{ $t('commission_details') }}
                                        </h4>
                                        <div
                                            class="grid grid-cols-3 gap-6 bg-white p-4 rounded-lg shadow-sm"
                                        >
                                            <div
                                                class="flex justify-between items-center p-3 bg-gray-50 rounded-md"
                                            >
                                                <span class="text-gray-600"
                                                    >{{ $t('commission_type') }}:</span
                                                >
                                                <span class="font-medium">{{
                                                    transaction.commission
                                                        .type === "percentage"
                                                        ? $t('percentage')
                                                        : $t('fixed')
                                                }}</span>
                                            </div>
                                            <div
                                                class="flex justify-between items-center p-3 bg-gray-50 rounded-md"
                                            >
                                                <span class="text-gray-600"
                                                    >{{ $t('commission_value') }}:</span
                                                >
                                                <span class="font-medium"
                                                    >{{
                                                        transaction.commission
                                                            .value
                                                    }}
                                                </span>
                                            </div>
                                            <div
                                                v-if="
                                                    transaction.commission
                                                        .type === 'percentage'
                                                "
                                                class="flex justify-between items-center p-3 bg-gray-50 rounded-md"
                                            >
                                                <span class="text-gray-600"
                                                    >{{ $t('percentage') }}:</span
                                                >
                                                <span class="font-medium"
                                                    >{{
                                                        transaction.commission
                                                            .percentage
                                                    }}%</span
                                                >
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        v-if="
                                            transaction.providers_insurance
                                                ?.length
                                        "
                                    >
                                        <h4
                                            class="text-lg font-semibold mb-4 text-gray-700 border-r-4 border-indigo-500 pr-3"
                                        >
                                            {{ $t('insurance_details_for_providers') }}
                                        </h4>
                                        <div
                                            class="bg-white rounded-lg shadow-sm overflow-hidden"
                                        >
                                            <div class="space-y-4 p-4">
                                                <div
                                                    v-for="provider in transaction.providers_insurance"
                                                    :key="provider.id"
                                                    class="bg-gray-50 rounded-lg p-4"
                                                >
                                                    <div
                                                        class="flex justify-between items-center mb-4"
                                                    >
                                                        <div>
                                                            <h5
                                                                class="font-medium text-gray-900"
                                                            >
                                                                {{
                                                                    provider.name
                                                                }}
                                                            </h5>
                                                            <p
                                                                class="text-sm text-gray-500"
                                                            >
                                                                {{
                                                                    provider.services_count
                                                                }}
                                                                {{ $t('services') }}
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="flex items-center gap-4"
                                                        >
                                                            <span
                                                                class="text-gray-700"
                                                                >{{
                                                                    provider.insurance_amount
                                                                }}
                                                                {{ $t('sar') }}</span
                                                            >
                                                            <span
                                                                :class="{
                                                                    'px-3 py-1 rounded-full text-sm': true,
                                                                    'bg-green-100 text-green-800':
                                                                        provider.insurance_status ===
                                                                        'refunded',
                                                                    'bg-yellow-100 text-yellow-800':
                                                                        provider.insurance_status ===
                                                                        'pending',
                                                                    'bg-red-100 text-red-800':
                                                                        provider.insurance_status ===
                                                                        'due',
                                                                }"
                                                            >
                                                                {{
                                                                    provider.insurance_status ===
                                                                    "refunded"
                                                                        ? $t('refunded')
                                                                        : provider.insurance_status ===
                                                                          "pending"
                                                                        ? $t('pending')
                                                                        : $t('due')
                                                                }}
                                                            </span>
                                                            <span
                                                                class="text-sm text-gray-500"
                                                                >{{
                                                                    provider.refund_date ||
                                                                    $t('no_refund_yet')
                                                                }}</span
                                                            >
                                                        </div>
                                                    </div>

                                                    <!-- تفاصيل الخدمات -->
                                                    <div
                                                        class="mt-2 space-y-2 border-t pt-2"
                                                    >
                                                        <div
                                                            v-for="service in provider.services"
                                                            :key="service.id"
                                                            class="flex justify-between items-center py-2 px-4 bg-white rounded"
                                                        >
                                                            <span
                                                                class="text-gray-700"
                                                                >{{
                                                                    service.name
                                                                }}</span
                                                            >
                                                            <div
                                                                class="flex items-center gap-4"
                                                            >
                                                                <span
                                                                    class="text-gray-600"
                                                                    >{{
                                                                        service.insurance_amount
                                                                    }}
                                                                    {{ $t('sar') }}</span
                                                                >
                                                                <span
                                                                    :class="{
                                                                        'px-2 py-1 rounded-full text-xs': true,
                                                                        'bg-green-100 text-green-800':
                                                                            service.insurance_status ===
                                                                            'refunded',
                                                                        'bg-yellow-100 text-yellow-800':
                                                                            service.insurance_status ===
                                                                            'pending',
                                                                        'bg-red-100 text-red-800':
                                                                            service.insurance_status ===
                                                                            'due',
                                                                    }"
                                                                >
                                                                    {{
                                                                        service.insurance_status ===
                                                                        "refunded"
                                                                                ? $t('refunded')
                                                                            : service.insurance_status ===
                                                                              "pending"
                                                                            ? $t('pending')
                                                                            : $t('due')
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

            </div>

            <div
                v-if="transactions.meta"
                class="px-6 py-3 border-t border-gray-200"
            >
                <div
                    class="flex flex-col sm:flex-row items-center justify-between gap-4"
                >
                    <div
                        class="text-sm text-gray-700 text-center sm:text-right"
                    >
                        {{ $t('showing') }}
                        <span class="font-medium">{{
                            transactions.meta.from
                        }}</span>
                        {{ $t('to') }}
                        <span class="font-medium">{{
                            transactions.meta.to
                        }}</span>
                        {{ $t('from') }}
                        <span class="font-medium">{{
                            transactions.meta.total
                        }}</span>
                        {{ $t('results') }}
                    </div>

                    <Pagination :links="paginationLinks" />
                </div>
            </div>
        </div>
    </authenticated-layout>
</template>

<style scoped>
.fa-chevron-left,
.fa-chevron-down {
    transition: transform 0.3s ease;
}

.fa-chevron-down {
    transform: rotate(90deg);
}
</style>
