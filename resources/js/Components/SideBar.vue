<template>
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <Link class="nav-link" :href="route('dashboard')" :class="{ collapsed: $page.url !== '/dashboard' }">
                <i class="bi bi-grid"></i>
                <span> {{ $t("dashboard") }} </span>
                </Link>
            </li>

                        <!-- admin roles -->
                        <li class="nav-item" v-if="hasPermission('read roles') || hasPermission('read permissions')">
                <a class="nav-link" data-bs-target="#components-nav" data-bs-toggle="collapse" href="#" :class="{
                    collapsed:
                        !$page.url.startsWith('/roles') &&
                        !$page.url.startsWith('/permissions'),
                }">
                    <i class="bi bi-lock"></i><span>{{ $t("roles_control") }}</span><i
                        class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="components-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
                    <li>
                        <Link :href="route('roles.index')" :class="{
                            collapsed: !$page.url.startsWith('/roles'),
                        }">
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

            <!-- admin users -->
            <li class="nav-item" v-if="hasPermission('read users')">
                <Link class="nav-link" :href="route('users.index')"
                    :class="{ collapsed: !$page.url.startsWith('/users') }">
                <i class="bi bi-person"></i>
                <span>{{ $t("Admins") }}</span>
                </Link>
            </li>


            <li class="nav-item" v-if="hasPermission('read clients')">
                <Link class="nav-link" :href="route('clients.index')"
                    :class="{ collapsed: !$page.url.startsWith('/clients') }">
                <i class="bi bi-person"></i>
                <span>{{ $t("Clients") }}</span>
                </Link>
            </li>

            <!-- Admin vendors -->
            <li class="nav-item" v-if="hasPermission('read vendors')">
                <Link class="nav-link" :href="route('vendors.index')" :class="{
                    collapsed: !$page.url.startsWith('/admin/vendors'),
                }">
                <i class="bi bi-shop"></i>
                <span>{{ $t("vendors") }}</span>
                </Link>
            </li>
            
            <!-- Admin gold pieces -->
            <li class="nav-item" v-if="hasPermission('read gold-pieces')">
                <Link class="nav-link" :href="route('admin.gold-pieces.index')" :class="{
                    collapsed: !$page.url.startsWith('/admin/gold-pieces'),
                }">
                <i class="bi bi-shop"></i>
                <span>{{ $t("Gold pieces") }}</span>
                </Link>
            </li>

<!--             
            <li class="nav-item" v-if="hasPermission('read orders')">
                <Link class="nav-link" :href="route('admin.rental-requests.index')" :class="{
                    collapsed: !$page.url.startsWith('/admin/rental-requests'),
                }">
                <i class="bi bi-shop"></i>
                <span>{{ $t("Rental Requests") }}</span>
                </Link>
            </li> -->
            <!-- <li class="nav-item" v-if="hasPermission('read orders')">
                <Link class="nav-link" :href="route('admin.orders.rental.index')" :class="{
                    collapsed: !$page.url.startsWith('/admin/orders/rental'),
                }">
                <i class="bi bi-shop"></i>
                <span>{{ $t("Rental Orders") }}</span>
                </Link>
            </li> -->

            <li class="nav-item" v-if="hasPermission('read orders')">
                <Link class="nav-link" :href="route('admin.orders.sale.index')" :class="{
                    collapsed: !$page.url.startsWith('/admin/orders/sale'),
                }">
                <i class="bi bi-shop"></i>
                <span>{{ $t("Sales Orders") }}</span>
                </Link>
            </li>

        
            <!-- admin wallet -->
            <!-- <li class="nav-item" v-if="hasPermission('read wallet')">
                <Link class="nav-link" :href="route('wallet.index')"
                    :class="{ collapsed: !$page.url.startsWith('/admin/wallet') }">
                <i class="bi bi-wallet"></i>
                <span>{{ $t("Wallet") }}</span>
                </Link>
            </li> -->

            <!-- admin settlement requests -->
            <!-- <li class="nav-item" v-if="hasPermission('read settlement-requests')">
                <Link class="nav-link" :href="route('admin.settlement-requests-vendor.index')"
                    :class="{ collapsed: !$page.url.startsWith('/admin/settlement-requests-vendor') }">
                <i class="bi bi-wallet"></i>
                <span>{{ $t("Settlement Requests for vendors") }}</span>
                </Link>
            </li>

            <li class="nav-item" v-if="hasPermission('read settlement-requests')">
                <Link class="nav-link" :href="route('admin.settlement-requests-user.index')"
                    :class="{ collapsed: !$page.url.startsWith('/admin/settlement-requests-user') }">
                <i class="bi bi-wallet"></i>
                <span>{{ $t("Settlement Requests for users") }}</span>
                </Link>
            </li> -->


            <!-- vendor admins -->
            <li class="nav-item" v-if="hasPermission('vendor read users')">
                <Link class="nav-link" :href="route('vendor.users.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/users') }">
                <i class="bi bi-person"></i>
                <span>{{ $t("vendor_admins") }}</span>
                </Link>
            </li>

            <!-- vendor roles -->
            <li class="nav-item" v-if="hasPermission('vendor read roles')">
                <Link class="nav-link" :href="route('vendor.roles.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/roles') }">
                <i class="bi bi-person-badge"></i>
                <span>{{ $t("roles") }}</span>
                </Link>
            </li>

            <!-- Vendor sale orders -->

            <li class="nav-item" v-if="hasPermission('vendor read orders')">
                <Link class="nav-link" :href="route('vendor.orders.sale.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/sale-orders') }">
                <i class="bi bi-cart"></i>
                <span>{{ $t("sales_orders") }}</span>
                </Link>
            </li>


            <!-- Vendor rental orders -->

            <!-- <li class="nav-item" v-if="hasPermission('vendor read orders')">
                <Link class="nav-link" :href="route('vendor.orders.rental.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/rental-orders') }">
                <i class="bi bi-cart-check"></i>
                <span>{{ $t("rental_orders") }}</span>
                </Link>
            </li> -->
            <!-- Vendor rental requests -->

            <!-- <li class="nav-item" v-if="hasPermission('vendor read rental-requests')">
                <Link class="nav-link" :href="route('vendor.rental-requests.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/rental-requests') }">
                <i class="bi bi-calendar-check"></i>
                <span>{{ $t("rental_requests") }}</span>
                </Link>
            </li> -->

            <!-- Vendor branches -->
            <li class="nav-item" v-if="hasPermission('vendor read branches')">
                <Link class="nav-link" :href="route('vendor.branches.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/branches') }">
                <i class="bi bi-building"></i>
                <span>{{ $t("branches") }}</span>
                </Link>
            </li>

            


            <!-- Complaints -->
            <li class="nav-item" v-if="hasPermission('vendor read complaints')">
                <Link class="nav-link" :href="route('vendor.contacts.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/contacts') }">
                <i class="bi bi-person-lines-fill"></i>
                <span>{{ $t("Complaints") }}</span>
                </Link>
            </li>


                        <!-- Complaints -->
            <li class="nav-item" v-if="hasPermission('vendor read complaints')">
                <Link class="nav-link" :href="route('vendor.admin.complaints.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/admin/complaints') }">
                <i class="bi bi-person-lines-fill"></i>
                <span>{{ $t("Admin Complaints") }}</span>
                </Link>
            </li>

         <!-- vendor transactions -->
            <li class="nav-item" v-if="hasPermission('vendor read transactions')">
                <Link class="nav-link" :href="route('vendor.transactions.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/transactions') }">
                <i class="bi bi-person-lines-fill"></i>
                <span>{{ $t("Transactions") }}</span>
                </Link>
            </li>
            <!-- Vendor wallet -->
            <!-- <li class="nav-item" v-if="hasPermission('vendor read wallet')">
                <Link class="nav-link" :href="route('vendor.wallet.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/wallet') }">
                <i class="bi bi-wallet"></i>
                <span>{{ $t("Wallet") }}</span>
                </Link>
            </li> -->

            

            <!-- vendor Settlement Requests -->
            <!-- <li class="nav-item" v-if="hasPermission('vendor read settlement-requests')">
                <Link class="nav-link" :href="route('vendor.settlement-requests.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/settlement-requests') }">
                <i class="bi bi-wallet"></i>
                <span>{{ $t("Settlement Requests") }}</span>
                </Link>
            </li> -->


            <!-- Reports -->
            <li class="nav-item" v-if="hasPermission('vendor read reports')">
                <Link class="nav-link" :href="route('vendor.reports.index')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/reports') }">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>{{ $t("Reports") }}</span>
                </Link>
            </li>

            <!-- Store Information -->
            <li class="nav-item" v-if="hasPermission('vendor read users')">
                <Link class="nav-link" :href="route('vendor.store.show')"
                    :class="{ collapsed: !$page.url.startsWith('/vendor/store/show') }">
                <i class="bi bi-shop"></i>
                <span>{{ $t("Store Information") }}</span>
                </Link>
            </li>

            <!--Admin Complaints -->
            <li class="nav-item" v-if="hasPermission('read complaints')">
                <Link class="nav-link" :href="route('admin.complaints.index')"
                    :class="{ collapsed: !$page.url.startsWith('/admin/complaints') }">
                <i class="bi bi-person-lines-fill"></i>
                <span>{{ $t("Complaints") }}</span>
                </Link>
            </li>


            <!-- banners -->
            <li class="nav-item" v-if="hasPermission('read banners')">
                <Link class="nav-link" :href="route('banners.index')" :class="{
                    collapsed: !$page.url.startsWith('/banners'),
                }">
                <i class="bi bi-image"></i>
                <span>{{ $t("banners") }}</span>
                </Link>
            </li>


            <!-- Reports -->
            <li class="nav-item" v-if="hasPermission('read reports')">
                <Link class="nav-link" :href="route('admin.reports.index')"
                    :class="{ collapsed: !$page.url.startsWith('admin/reports') }">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>{{ $t("Reports") }}</span>
                </Link>
            </li>

            <li class="nav-item" v-if="hasPermission('read cities')">
                <Link class="nav-link" :href="route('admin.cities.index')"
                    :class="{ collapsed: !$page.url.startsWith('/admin/cities') }">
                <i class="bi bi-buildings"></i>
                <span>{{ $t("Cities") }}</span>
                </Link>
            </li>

            <!-- ------------------------------------------------- -->
            <li class="nav-item" v-if="hasPermission('read static_pages')">
                <a class="nav-link collapsed" data-bs-target="#pages-dropdown" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-file-earmark"></i>
                    <span>{{ $t("pages") }}</span>
                    <i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="pages-dropdown" class="nav-content collapse" data-bs-parent="#sidebar">
                    <li v-if="hasPermission('read static_pages')">
                        <Link class="nav-link" :href="route('pages.edit', { slug: 'about-us' })" :class="{
                            collapsed: !$page.url.startsWith(
                                '/pages/about-us/edit'
                            ),
                        }">
                        <i class="bi bi-circle"></i>
                        <span>{{ $t("about_us") }}</span>
                        </Link>
                    </li>
                    <li v-if="hasPermission('read static_pages')">
                        <Link class="nav-link" :href="route('pages.edit', { slug: 'privacy-policy' })
                            " :class="{
                                collapsed: !$page.url.startsWith(
                                    '/pages/privacy-policy/edit'
                                ),
                            }">
                        <i class="bi bi-circle"></i>
                        <span>{{ $t("privacy_policy") }}</span>
                        </Link>
                    </li>
                    <li v-if="hasPermission('read static_pages')">
                        <Link class="nav-link" :href="route('pages.edit', {
                            slug: 'terms-and-conditions',
                        })
                            " :class="{
                                collapsed: !$page.url.startsWith(
                                    '/pages/terms-and-conditions/edit'
                                ),
                            }">
                        <i class="bi bi-circle"></i>
                        <span>{{ $t("terms_and_conditions") }}</span>
                        </Link>
                    </li>

                    <li v-if="hasPermission('read static_pages')">
                        <Link class="nav-link" :href="route('pages.edit', { slug: 'contact-us' })" :class="{
                            collapsed: !$page.url.startsWith(
                                '/pages/contact-us/edit'
                            ),
                        }">
                        <i class="bi bi-circle"></i>
                        <span>{{ $t("contact_us") }}</span>
                        </Link>
                    </li>
                    <li v-if="hasPermission('read static_pages')">
                        <Link class="nav-link" :href="route('faqs.index')" :class="{
                            collapsed: !$page.url.startsWith('/faqs'),
                        }">
                        <i class="bi bi-circle"></i>
                        <span>{{ $t("faqs") }}</span>
                        </Link>
                    </li>
                </ul>
            </li>


            <!-- settings -->
            <li class="nav-item" v-if="hasPermission('read settings')">
                <Link class="nav-link" :href="route('settings.index')"
                    :class="{ collapsed: !$page.url.startsWith('/settings') }">
                <i class="bi bi-gear"></i>
                <span>{{ $t("Landing page settings") }}</span>
                </Link>
            </li>

            <!-- i want to get all permissions for current user 
            and if the user has read app_settings permission, show the app settings link -->
            
            <li class="nav-item" v-if="hasPermission('read app_settings')">
                <Link class="nav-link" :href="route('system-settings')"
                    :class="{ collapsed: !$page.url.startsWith('/app-settings') }">
                <i class="bi bi-gear"></i>
                <span>{{ $t("App settings") }}</span>
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