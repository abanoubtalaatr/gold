<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Define props to receive data from the controller
defineProps<{
  auth: {
    user: {
      name: string
      email: string
    }
  }
  roles: number
  admins: number
  branches: number
  rentalRequests: number
  salesOrders: number
  rentalOrders: number
}>()
</script>

<template>
  <Head title="Vendor Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Vendor Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h3 class="text-lg font-medium mb-4">Welcome to your vendor dashboard!</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <div class="bg-blue-100 p-4 rounded-lg">
                <h4 class="text-sm font-semibold text-blue-800">Total Roles</h4>
                <p class="text-2xl font-bold text-blue-900">{{ roles }}</p>
              </div>
              <div class="bg-green-100 p-4 rounded-lg">
                <h4 class="text-sm font-semibold text-green-800">Total Admins</h4>
                <p class="text-2xl font-bold text-green-900">{{ admins }}</p>
              </div>
              <div class="bg-purple-100 p-4 rounded-lg">
                <h4 class="text-sm font-semibold text-purple-800">Total Branches</h4>
                <p class="text-2xl font-bold text-purple-900">{{ branches }}</p>
              </div>
              <div class="bg-yellow-100 p-4 rounded-lg">
                <h4 class="text-sm font-semibold text-yellow-800">Rental Requests</h4>
                <p class="text-2xl font-bold text-yellow-900">{{ rentalRequests }}</p>
              </div>
              <div class="bg-red-100 p-4 rounded-lg">
                <h4 class="text-sm font-semibold text-red-800">Sales Orders</h4>
                <p class="text-2xl font-bold text-red-900">{{ salesOrders }}</p>
              </div>
              <div class="bg-indigo-100 p-4 rounded-lg">
                <h4 class="text-sm font-semibold text-indigo-800">Rental Orders</h4>
                <p class="text-2xl font-bold text-indigo-900">{{ rentalOrders }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Optional: Add custom styles for better appearance */
.grid {
  display: grid;
  gap: 1rem;
}
</style>