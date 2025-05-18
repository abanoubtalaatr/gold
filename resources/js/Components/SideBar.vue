<template>
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            
            <li class="nav-item">
                <Link
                    class="nav-link"
                    :href="route('dashboard')"
                    :class="{ collapsed: $page.url !== '/dashboard' }"
                >
                    <i class="bi bi-grid"></i>
                    <span> {{ $t("dashboard") }} </span>
                </Link>
            </li>

            <!-- banners -->
            <li class="nav-item" v-if="hasPermission('read banners')">
                <Link
                    class="nav-link"
                    :href="route('banners.index')"
                    :class="{
                        collapsed: !$page.url.startsWith('/banners'),
                    }"
                >
                    <i class="bi bi-image"></i>
                    <span>{{ $t("banners") }}</span>
                </Link>
            </li>

            <!-- contacts -->
            <li class="nav-item" v-if="hasPermission('read contacts')">
                <Link
                    class="nav-link"
                    :href="route('contacts.index')"
                    :class="{ collapsed: !$page.url.startsWith('/contacts') }"
                >
                    <i class="bi bi-inbox"></i>
                    <span>{{ $t("contacts") }}</span>
                </Link>
            </li>

            <li class="nav-item" v-if="hasPermission('read static_pages')">
                <a
                    class="nav-link collapsed"
                    data-bs-target="#pages-dropdown"
                    data-bs-toggle="collapse"
                    href="#"
                >
                    <i class="bi bi-file-earmark"></i>
                    <span>{{ $t("pages") }}</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="pages-dropdown"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar"
                >
                    <li v-if="hasPermission('read static_pages')">
                        <Link
                            class="nav-link"
                            :href="route('pages.edit', { slug: 'about-us' })"
                            :class="{
                                collapsed: !$page.url.startsWith(
                                    '/pages/about-us/edit'
                                ),
                            }"
                        >
                            <i class="bi bi-circle"></i>
                            <span>{{ $t("about_us") }}</span>
                        </Link>
                    </li>
                    <li v-if="hasPermission('read static_pages')">
                        <Link
                            class="nav-link"
                            :href="
                                route('pages.edit', { slug: 'privacy-policy' })
                            "
                            :class="{
                                collapsed: !$page.url.startsWith(
                                    '/pages/privacy-policy/edit'
                                ),
                            }"
                        >
                            <i class="bi bi-circle"></i>
                            <span>{{ $t("privacy_policy") }}</span>
                        </Link>
                    </li>
                    <li v-if="hasPermission('read static_pages')">
                        <Link
                            class="nav-link"
                            :href="
                                route('pages.edit', {
                                    slug: 'terms-and-conditions',
                                })
                            "
                            :class="{
                                collapsed: !$page.url.startsWith(
                                    '/pages/terms-and-conditions/edit'
                                ),
                            }"
                        >
                            <i class="bi bi-circle"></i>
                            <span>{{ $t("terms_and_conditions") }}</span>
                        </Link>
                    </li>

                    <li v-if="hasPermission('read static_pages')">
                        <Link
                            class="nav-link"
                            :href="route('pages.edit', { slug: 'contact-us' })"
                            :class="{
                                collapsed: !$page.url.startsWith(
                                    '/pages/contact-us/edit'
                                ),
                            }"
                        >
                            <i class="bi bi-circle"></i>
                            <span>{{ $t("contact_us") }}</span>
                        </Link>
                    </li>
                    <li v-if="hasPermission('read static_pages')">
                        <Link
                            class="nav-link"
                            :href="route('faqs.index')"
                            :class="{
                                collapsed: !$page.url.startsWith('/faqs'),
                            }"
                        >
                            <i class="bi bi-circle"></i>
                            <span>{{ $t("faqs") }}</span>
                        </Link>
                    </li>
                </ul>
            </li>

            <!-- users -->
            <li class="nav-item" v-if="hasPermission('read users')">
                <Link
                    class="nav-link"
                    :href="route('users.index')"
                    :class="{ collapsed: !$page.url.startsWith('/users') }"
                >
                    <i class="bi bi-person"></i>
                    <span>{{ $t("users") }}</span>
                </Link>
            </li>
            <!-- roles -->
            <li
                class="nav-item"
                :hasPermission="['read roles', 'read permissions']"
            >
                <a
                    class="nav-link"
                    data-bs-target="#components-nav"
                    data-bs-toggle="collapse"
                    href="#"
                    :class="{
                        collapsed:
                            !$page.url.startsWith('/roles') &&
                            !$page.url.startsWith('/permissions'),
                    }"
                >
                    <i class="bi bi-lock"></i
                    ><span>{{ $t("roles_control") }}</span
                    ><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul
                    id="components-nav"
                    class="nav-content collapse"
                    data-bs-parent="#sidebar-nav"
                >
                    <li>
                        <Link
                            :href="route('roles.index')"
                            :class="{
                                collapsed: !$page.url.startsWith('/roles'),
                            }"
                        >
                            <i class="bi bi-circle"></i>
                            <span>{{ $t("roles") }}</span>
                        </Link>
                    </li>
                    <!-- <li>
                <Link   :href="route('permissions.index')"  :class="{ 'collapsed':  !$page.url.startsWith('/permissions') }" >
            <i class="bi bi-circle"></i>
            <span>{{$t('permissions') }}</span>
               </Link>
            </li> -->
                </ul>
            </li>
            <!-- settings -->
            <li class="nav-item" v-if="hasPermission('read settings')">
                <Link
                    class="nav-link"
                    :href="route('settings.index')"
                    :class="{ collapsed: !$page.url.startsWith('/settings') }"
                >
                    <i class="bi bi-gear"></i>
                    <span>{{ $t("settings") }}</span>
                </Link>
            </li>
        </ul>
    </aside>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";

const page = usePage();

const hasPermission = (permission) => {
    return page.props.auth_permissions.includes(permission);
};
defineProps({ message: String });
</script>

<script>
export default {
    name: "Sidebar",
};
</script>
