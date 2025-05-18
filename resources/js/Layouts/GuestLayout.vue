<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link , usePage} from '@inertiajs/vue3';
import { computed } from 'vue';


const page = usePage()
const currentLanguage = computed(() => page.props.locale || 'en');
</script>

<template >

    <div   dir="rtl {{ currentLanguage == 'ar' ? 'rtl' : 'ltr' }}" class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
                {{ locale }}
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
