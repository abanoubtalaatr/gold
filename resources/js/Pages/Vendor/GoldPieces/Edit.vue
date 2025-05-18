<template>
  <Create :gold-piece="goldPiece" :branches="branches" />
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Create from './Create.vue';

defineProps({
  goldPiece: {
    type: Object,
    required: true,
  },
  branches: {
    type: Array,
    default: () => [],
  },
});
</script> 