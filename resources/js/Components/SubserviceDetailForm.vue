<template>
    <div class="border p-3 mb-3 rounded">
        <div class="d-flex justify-content-end mb-2">
            <button @click="$emit('remove')" class="btn btn-danger btn-sm" type="button">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ $t('level') }}</label>
                <select v-model="detail.level" class="form-control">
                    <option value="Low">{{ $t('low') }}</option>
                    <option value="Medium">{{ $t('medium') }}</option>
                    <option value="High">{{ $t('high') }}</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ $t('number_of_labors') }}</label>
                <input 
                    type="number" 
                    v-model="detail.number_of_labors" 
                    class="form-control"
                    min="1"
                >
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">{{ $t('price') }}</label>
                <input 
                    type="number" 
                    v-model="detail.price" 
                    class="form-control"
                    step="0.01"
                    min="0"
                >
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['remove', 'update:modelValue']);

const detail = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script> 