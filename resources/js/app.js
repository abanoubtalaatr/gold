import "./bootstrap";
import "../css/app.css";
import "../../public/dashboard-assets/js/main.js";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { createI18n } from "vue-i18n";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import ElementPlus from "element-plus";
import "element-plus/dist/index.css";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import Editor from "@tinymce/tinymce-vue";
import VueApexCharts from "vue3-apexcharts";

// لغات
import ar from "../../lang/ar.json";
import en from "../../lang/en.json";

const i18n = createI18n({
    locale: document.querySelector("html").getAttribute("lang"),
    fallbackLocale: "en",
    globalInjection: true,
    messages: { ar, en },
    legacy: false,
});

document.documentElement.style.setProperty(
    "--direction",
    i18n.global.locale === "ar" ? "rtl" : "ltr"
);
const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .use(ElementPlus)
            .component("QuillEditor", QuillEditor) // تسجيل Quill كمكون
            .component("Editor", Editor)
            .component("apexchart", VueApexCharts)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
