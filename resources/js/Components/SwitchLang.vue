<template>
  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">
      <li class="nav-item dropdown m-2">
        <select class="form-control changeLang" v-model="currentLanguage" @change="changeLanguage">
          <option value="" disabled>{{ $t('language') }} 🌍</option>
          <option value="en">{{ $t('english') }}</option>
          <option value="ar">{{ $t('arabic') }}</option>
        </select>
      </li>
    </ul>
  </nav>
</template>



<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import axios from 'axios';

const page = usePage();
const currentLanguage = computed(() => page.props.locale || 'en'); // افتراضية

const changeLanguage = async (event) => {
    const selectedLanguage = event.target.value;
    try {
        await axios.get(`/lang/change?lang=${selectedLanguage}`);
        location.reload(); 
    } catch (error) {
        console.error('Error changing language:', error);
    }
};
</script>

