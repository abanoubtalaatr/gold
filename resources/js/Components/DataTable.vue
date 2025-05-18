<template>
  <div>
    <div class="">
      <table class="table table-hover text-center">
        <thead>
          <tr>
            <th>#</th>
            <th v-for="header in headers" :key="header.key">
              <span @click="sortBy(header.key)" style="cursor: pointer">
                {{ header.label }}
                <i
                  v-if="sortedColumn === header.key"
                  :class="sortOrder === 'asc' ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill'"
                ></i>
              </span>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, index) in sortedData" :key="row.id">
            <td>{{ index + 1 }}</td>
            <td v-for="header in headers" :key="header.key">
              <slot :name="header.key" :data="row">
                {{ row[header.key] || '-' }}
              </slot>
            </td>
          </tr>
          <tr v-if="data.length === 0">
            <td :colspan="headers.length + 1" class="text-center">
              {{ $t("no_data_found") }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <Pagination :links="paginationLinks" @update:page="handlePageChange" />
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import Pagination from "./Pagination.vue";

const props = defineProps({
  headers: {
    type: Array,
    required: true,
  },
  data: {
    type: Array,
    required: true,
  },
  paginationLinks: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(["update:page"]);

const sortedColumn = ref(null);
const sortOrder = ref(null);

const sortBy = (key) => {
  if (sortedColumn.value === key) {
    sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
  } else {
    sortedColumn.value = key;
    sortOrder.value = "asc";
  }
};

const sortedData = computed(() => {
  if (!sortedColumn.value) return props.data;

  return [...props.data].sort((a, b) => {
    const valueA = a[sortedColumn.value];
    const valueB = b[sortedColumn.value];

    if (valueA < valueB) return sortOrder.value === "asc" ? -1 : 1;
    if (valueA > valueB) return sortOrder.value === "asc" ? 1 : -1;
    return 0;
  });
});

const handlePageChange = (page) => {
  emit("update:page", page);
};
</script>

<style scoped>
th {
  cursor: pointer;
}

.table-responsive {
  overflow-x: auto;
}

@media (max-width: 768px) {
  td:nth-child(3),
  th:nth-child(3) {
    display: none;
  }
}
</style>
