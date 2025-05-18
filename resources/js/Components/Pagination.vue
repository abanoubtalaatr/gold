<template>
    <div style="display: flex; justify-content: center">
        <nav>
            <ul class="pagination">
                <li
                    v-for="(link, index) in links"
                    :key="index"
                    class="page-item"
                    :class="{ active: link?.active, disabled: !link?.url }"
                >
                    <Link v-if="link?.url" class="page-link" :href="link.url">
                        <span
                            v-if="link?.label && link.label.includes('&laquo;')"
                        >
                            {{ $t("previous") }}
                        </span>
                        <span
                            v-else-if="
                                link?.label && link.label.includes('&raquo;')
                            "
                        >
                            {{ $t("next") }}
                        </span>
                        <span v-else>
                            <span
                                v-if="
                                    link?.label &&
                                    (link.label.includes('Previous') ||
                                        link.label.includes('«'))
                                "
                            >
                                {{ $t("previous") }}
                            </span>
                            <span
                                v-else-if="
                                    link?.label &&
                                    (link.label.includes('Next') ||
                                        link.label.includes('»'))
                                "
                            >
                                {{ $t("next") }}
                            </span>
                            <span v-else v-html="link?.label"></span>
                        </span>
                    </Link>
                    <span v-else class="page-link">
                        <span
                            v-if="
                                link?.label &&
                                (link.label.includes('Previous') ||
                                    link.label.includes('«'))
                            "
                        >
                            {{ $t("previous") }}
                        </span>
                        <span
                            v-else-if="
                                link?.label &&
                                (link.label.includes('Next') ||
                                    link.label.includes('»'))
                            "
                        >
                            {{ $t("next") }}
                        </span>
                        <span v-else v-html="link?.label"></span>
                    </span>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

defineProps({
    links: {
        type: Array,
        required: true,
        default: () => [],
    },
});
</script>

<style scoped>
.pagination {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.page-item {
    list-style: none;
}



.page-item.disabled .page-link {
    color: #a0aec0;
    cursor: not-allowed;
}

.page-link:hover:not(.page-item.disabled .page-link) {
    background-color: #f7fafc;
}
</style>
