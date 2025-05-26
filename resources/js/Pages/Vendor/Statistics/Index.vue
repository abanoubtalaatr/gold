<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        {{ $t('Statistics') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Filter Section -->
            <div class="p-4 mb-6 bg-gray-50 rounded-lg">
              <h3 class="mb-4 text-lg font-medium">{{ $t('Filter Statistics') }}</h3>
              <form @submit.prevent="fetchStatistics">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                  <!-- Period Type -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">
                      {{ $t('Period Type') }}
                    </label>
                    <select v-model="filters.periodType" class="w-full mt-1 rounded-md">
                      <option value="daily">{{ $t('Daily') }}</option>
                      <option value="weekly">{{ $t('Weekly') }}</option>
                      <option value="quarterly">{{ $t('Quarterly') }}</option>
                      <option value="semiannual">{{ $t('Semiannual') }}</option>
                      <option value="annual">{{ $t('Annual') }}</option>
                      <option value="custom">{{ $t('Custom') }}</option>
                    </select>
                  </div>

                  <!-- Date Range -->
                  <div v-if="filters.periodType === 'custom'">
                    <label class="block text-sm font-medium text-gray-700">
                      {{ $t('From Date') }}
                    </label>
                    <input v-model="filters.startDate" type="date" class="w-full mt-1 rounded-md">
                  </div>

                  <div v-if="filters.periodType === 'custom'">
                    <label class="block text-sm font-medium text-gray-700">
                      {{ $t('To Date') }}
                    </label>
                    <input v-model="filters.endDate" type="date" class="w-full mt-1 rounded-md">
                  </div>

                  <!-- Submit Button -->
                  <div class="flex items-end">
                    <button type="submit"
                      class="w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                      {{ $t('Apply Filters') }}
                    </button>
                  </div>
                </div>
              </form>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4">
              <!-- Total Ratings -->
              <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900">
                  {{ $t('Total Ratings') }}
                </h3>
                <p class="mt-2 text-3xl font-bold text-indigo-600">
                  {{ statistics.totalRatings || 0 }}
                </p>
                <p class="mt-1 text-sm text-gray-500">
                  {{ $t('Average') }}: {{ statistics.averageRating || 0 }}/5
                </p>
              </div>

              <!-- Reservations -->
              <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900">
                  {{ $t('Reservations') }}
                </h3>
                <div class="grid grid-cols-3 gap-4 mt-2">
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('Completed') }}</p>
                    <p class="text-xl font-bold text-green-600">{{ statistics.completedReservations || 0 }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('Current') }}</p>
                    <p class="text-xl font-bold text-blue-600">{{ statistics.currentReservations || 0 }}</p>
                  </div>
                  <div>
                    <p class="text-sm text-gray-500">{{ $t('Upcoming') }}</p>
                    <p class="text-xl font-bold text-yellow-600">{{ statistics.upcomingReservations || 0 }}</p>
                  </div>
                </div>
              </div>

              <!-- Available Pieces -->
              <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900">
                  {{ $t('Available Rental Pieces') }}
                </h3>
                <p class="mt-2 text-3xl font-bold text-green-600">
                  {{ statistics.availablePieces || 0 }}
                </p>
              </div>

              <!-- Sold Pieces -->
              <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900">
                  {{ $t('Sold Pieces') }}
                </h3>
                <p class="mt-2 text-3xl font-bold text-red-600">
                  {{ statistics.soldPieces || 0 }}
                </p>
              </div>
            </div>

            <!-- Loading indicator -->
            <div v-if="loading" class="p-4 text-center">
              <p class="text-blue-600">{{ $t('Loading statistics...') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const filters = ref({
  periodType: 'weekly',
  startDate: new Date().toISOString().split('T')[0],
  endDate: new Date().toISOString().split('T')[0]
});

const statistics = ref({});
const loading = ref(false);

const fetchStatistics = async () => {
  loading.value = true;
  try {
    const response = await axios.get(route('vendor.statistics'), {
      params: filters.value
    });
    statistics.value = response.data;
  } catch (error) {
    console.error('Error fetching statistics:', error);
  } finally {
    loading.value = false;
  }
};

// Fetch initial statistics
fetchStatistics();
</script>