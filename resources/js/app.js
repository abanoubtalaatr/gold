import './bootstrap'
import '../css/app.css'
import '../../public/dashboard-assets/js/main.js'

import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { createI18n } from 'vue-i18n'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import Editor from '@tinymce/tinymce-vue'
import VueApexCharts from 'vue3-apexcharts'
import axios from 'axios'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// Languages
import ar from '../../lang/ar.json'
import en from '../../lang/en.json'

const i18n = createI18n({
  locale: document.querySelector('html').getAttribute('lang') || 'en',
  fallbackLocale: 'en',
  globalInjection: true,
  messages: { ar, en },
  legacy: false,
})

document.documentElement.style.setProperty(
  '--direction',
  i18n.global.locale === 'ar' ? 'rtl' : 'ltr'
)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

// CSRF Token Management
const updateCsrfToken = (token) => {
  if (token) {
    // Update meta tag
    const metaTag = document.head.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
      metaTag.setAttribute('content', token);
    }
    
    // Update axios headers
    if (window.axios) {
      window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
    
    console.log('CSRF token updated:', token.substring(0, 10) + '...');
  }
}

// Set initial CSRF token
const initialToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
if (initialToken) {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = initialToken;
}

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue')
    ),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(i18n)
      .use(ElementPlus)
      .component('QuillEditor', QuillEditor)
      .component('Editor', Editor)
      .component('apexchart', VueApexCharts)

    return app.mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})

// Update CSRF token on every Inertia page load/navigation
router.on('navigate', (event) => {
  const page = event.detail.page;
  if (page.props && page.props.csrf_token) {
    updateCsrfToken(page.props.csrf_token);
  }
});

// Handle CSRF token mismatch errors
router.on('error', (event) => {
  const response = event.detail.response;
  
  if (response.status === 419) {
    console.warn('CSRF token mismatch detected');
    
    // Try to refresh the token
    fetch('/refresh-csrf', {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Failed to fetch fresh token');
      }
      return response.json();
    })
    .then(data => {
      if (data.csrf_token) {
        updateCsrfToken(data.csrf_token);
        // Show user-friendly message
        alert('Your session has been refreshed. Please try your action again.');
      } else {
        throw new Error('No CSRF token in response');
      }
    })
    .catch(error => {
      console.error('Failed to refresh CSRF token:', error);
      alert('Your session has expired. The page will be refreshed.');
      window.location.reload();
    });
  }
});

// Axios setup for non-Inertia requests
window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Set up CSRF token for axios
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
  console.error('CSRF token not found')
}

// Ensure fresh CSRF token on each axios request
window.axios.interceptors.request.use(function (config) {
  const freshToken = document.head.querySelector('meta[name="csrf-token"]')?.content;
  if (freshToken) {
    config.headers['X-CSRF-TOKEN'] = freshToken;
  }
  return config;
}, function (error) {
  return Promise.reject(error);
});

// window.Pusher = Pusher

// window.Echo = new Echo({
//   broadcaster: 'pusher',
//   key: import.meta.env.VITE_PUSHER_APP_KEY,
//   cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//   forceTLS: true,
// })