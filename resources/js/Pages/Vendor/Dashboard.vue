<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps<{
  auth: {
    user: {
      name: string
      email: string
    }
  }
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
            Welcome to your vendor dashboard!
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template> 