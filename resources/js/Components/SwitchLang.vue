<template>
  <nav class="header-nav" :class="isRTL ? 'ms-auto' : 'me-auto'">
    <ul class="d-flex align-items-center">
      <li class="nav-item dropdown m-2">
        <button
          class="btn btn-outline-secondary dropdown-toggle language-dropdown"
          type="button"
          :id="dropdownId"
          data-bs-toggle="dropdown"
          aria-expanded="false"
          :class="{
            'dropdown-rtl': isRTL,
            'dropdown-ltr': !isRTL
          }"
        >
          <i class="fas fa-globe" :class="isRTL ? 'ms-2' : 'me-2'"></i>
          {{ currentLanguageLabel }}
        </button>
        
        <ul 
          class="dropdown-menu" 
          :class="{
            'dropdown-menu-start': isRTL,
            'dropdown-menu-end': !isRTL,
            'dropdown-menu-rtl': isRTL,
            'dropdown-menu-ltr': !isRTL
          }"
          :aria-labelledby="dropdownId"
        >
          <li>
            <h6 class="dropdown-header">{{ $t('language') }} 🌍</h6>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <button
              class="dropdown-item"
              :class="{ 'active': currentLanguage === 'en' }"
              @click="changeLanguage('en')"
              type="button"
            >
              <i class="fas fa-flag-usa" :class="isRTL ? 'ms-2' : 'me-2'"></i>
              {{ $t('english') }}
            </button>
          </li>
          <li>
            <button
              class="dropdown-item"
              :class="{ 'active': currentLanguage === 'ar' }"
              @click="changeLanguage('ar')"
              type="button"
            >
              <i class="fas fa-flag" :class="isRTL ? 'ms-2' : 'me-2'"></i>
              {{ $t('arabic') }}
            </button>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const currentLanguage = computed(() => page.props.locale || 'en');
const isRTL = computed(() => currentLanguage.value === 'ar');
const dropdownId = ref('languageDropdown-' + Math.random().toString(36).substr(2, 9));

// Get current language label
const currentLanguageLabel = computed(() => {
  return currentLanguage.value === 'ar' ? 'العربية' : 'English';
});

const changeLanguage = (selectedLanguage) => {
  if (selectedLanguage === currentLanguage.value) return;
  
  // Use direct navigation to ensure full page refresh for RTL/LTR styles
  window.location.href = `/lang/change?lang=${selectedLanguage}`;
};
</script>

<style scoped>
/* Base dropdown styles */
.language-dropdown {
  border-radius: 8px;
  border: 1px solid #e9ecef;
  transition: all 0.2s ease;
  min-width: 130px;
  background-color: transparent;
  font-size: 14px;
}

.language-dropdown:hover {
  border-color: #007bff;
  background-color: rgba(0, 123, 255, 0.1);
}

.language-dropdown:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Dropdown menu positioning and styles */
.dropdown-menu {
  border-radius: 8px;
  border: 1px solid #e9ecef;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  min-width: 160px;
  padding: 0.5rem 0;
  z-index: 1050;
}

/* RTL specific dropdown menu positioning */
.dropdown-menu-rtl {
  right: 0;
  left: auto;
  text-align: right;
  direction: rtl;
}

.dropdown-menu-ltr {
  left: 0;
  right: auto;
  text-align: left;
  direction: ltr;
}

/* Dropdown items */
.dropdown-item {
  padding: 8px 16px;
  transition: all 0.2s ease;
  border: none;
  background: none;
  width: 100%;
  text-align: inherit;
  color: #212529;
  font-size: 14px;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
  color: #495057;
}

.dropdown-item.active {
  background-color: #007bff;
  color: white;
}

.dropdown-item:focus {
  background-color: #e9ecef;
  outline: none;
}

/* Dropdown header */
.dropdown-header {
  padding: 8px 16px;
  margin-bottom: 0;
  font-size: 12px;
  font-weight: 600;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Dropdown divider */
.dropdown-divider {
  margin: 0.5rem 0;
  border-top: 1px solid #e9ecef;
}

/* RTL specific button styles */
.dropdown-rtl::after {
  margin-left: 0.5rem;
  margin-right: 0;
  border-left: 0.3em solid transparent;
  border-right: 0;
  border-top: 0.3em solid;
  border-bottom: 0;
}

.dropdown-ltr::after {
  margin-left: 0.5rem;
  margin-right: 0;
}

/* Icon positioning for RTL */
[dir="rtl"] .fas {
  margin-left: 0.5rem;
  margin-right: 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .language-dropdown {
    min-width: 100px;
    font-size: 13px;
  }
  
  .dropdown-menu {
    min-width: 140px;
  }
  
  .dropdown-item {
    padding: 6px 12px;
    font-size: 13px;
  }
}

/* Animation for dropdown */
.dropdown-menu {
  opacity: 0;
  transform: translateY(-10px);
  transition: opacity 0.15s ease, transform 0.15s ease;
}

.dropdown-menu.show {
  opacity: 1;
  transform: translateY(0);
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .language-dropdown {
    background-color: #2d3748;
    border-color: #4a5568;
    color: #e2e8f0;
  }
  
  .language-dropdown:hover {
    background-color: #4a5568;
    border-color: #63b3ed;
  }
  
  .dropdown-menu {
    background-color: #2d3748;
    border-color: #4a5568;
  }
  
  .dropdown-item {
    color: #e2e8f0;
  }
  
  .dropdown-item:hover {
    background-color: #4a5568;
    color: #ffffff;
  }
  
  .dropdown-header {
    color: #a0aec0;
  }
  
  .dropdown-divider {
    border-color: #4a5568;
  }
}
</style>

