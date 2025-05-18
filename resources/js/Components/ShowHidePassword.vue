<template>
  <div class="password-input-container">
    <input
      :type="isPasswordVisible ? 'text' : 'password'"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      class="password-input border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-s"
    />
    <span class="toggle-password-visibility" @click="togglePasswordVisibility">
      <i :class="isPasswordVisible ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
    </span>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    required: true,
  },
  placeholder: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:modelValue']);
const isPasswordVisible = ref(false);

const togglePasswordVisibility = () => {
  isPasswordVisible.value = !isPasswordVisible.value;
};
</script>

<style scoped>
.password-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.password-input {
  flex: 1;
  padding-right: 2.5rem;
}

.toggle-password-visibility {
  position: absolute;
  right: 0.75rem;
  cursor: pointer;
}
</style>
