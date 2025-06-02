<!DOCTYPE html>
<html class="no-js" lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<!-- change from 'rtl' to 'ltr' to switch direction -->

<head>
    <meta charset="UTF-8" />
    <meta name="description" content="{{ __('privacy.meta_description') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title  -->
    <title>{{ __('privacy.page_title') }}</title>

    <!-- Favicon  -->
    <link rel="icon" href="{{asset('assets/img/favicon.svg')}}" />

    <!-- ***** All CSS Files ***** -->

    <!-- Style css -->
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}" />

    @if(app()->isLocale('ar'))
    <!-- RTL CSS for Arabic -->
    <style>
        body {
            direction: rtl;
            text-align: right;
        }
        
        .navbar-nav {
            flex-direction: row-reverse;
        }
        
        .breadcrumb {
            justify-content: flex-end;
        }
        
        .content li {
            text-align: right;
        }
        
        .dropdown-menu {
            right: 0;
            left: auto;
        }
        
        #content h1 {
            font-size: inherit;
        }
    </style>
    @else
    <style>
        #content h1 {
            font-size: inherit;
        }
    </style>
    @endif
</head>

<body>
    <div class="main">
        <!-- ***** Preloader Start ***** -->
        <div class="preloader-main">
            <div class="preloader-wapper">
                <svg class="preloader" xmlns="http://www.w3.org/2000/svg" version="1.1" width="600" height="200">
                    <defs>
                        <filter id="goo" x="-40%" y="-40%" height="200%" width="400%">
                            <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
                            <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -8"
                                result="goo" />
                        </filter>
                    </defs>
                    <g filter="url(#goo)">
                        <circle class="dot" cx="50" cy="50" r="25" fill="#D4AF37" />
                        <circle class="dot" cx="50" cy="50" r="25" fill="#D4AF37" />
                    </g>
                </svg>
                <div>
                    <div class="loader-section section-left"></div>
                    <div class="loader-section section-right"></div>
                </div>
            </div>
        </div>
        <!-- ***** Preloader End ***** -->

        <!-- ***** Header Start ***** -->
        @include('header')
        <!-- ***** Header End ***** -->

        <!-- ***** Breadcrumb Area Start ***** -->
        <section id="home" class="breadcrumb-area layout-2 has-overlay overlay-gradient d-flex align-items-center">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Breamcrumb Content -->
                        <div class="breadcrumb-content text-center">
                            <h2 class="text-white">{{ __('privacy.breadcrumb.title') }}</h2>
                            <ol class="breadcrumb d-flex justify-content-center">
                                <li class="breadcrumb-item">
                                    <a class="text-white" href="{{ route('landing') }}">{{ __('privacy.breadcrumb.home') }}</a>
                                </li>
                                <li class="breadcrumb-item text-white active">
                                    {{ __('privacy.breadcrumb.title') }}
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Breadcrumb Area End ***** -->

        <!-- ***** Privacy Policy Content Start ***** -->
        <section class="terms-wrapper">
            <div class="container">
                <div class="content" id="content">
                    <ul>
                        <li>
                            <p>{{ __('privacy.content.welcome') }}</p>
                        </li>
                        <li>
                            <p>{{ __('privacy.content.respect_privacy') }}</p>
                        </li>
                        <li>
                            <p>{{ __('privacy.content.document_info') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('privacy.content.convention.title') }}</h5>
                            <p>{{ __('privacy.content.convention.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('privacy.content.definitions.title') }}</h5>
                            <p>{{ __('privacy.content.definitions.platform') }}</p>
                            <p>{{ __('privacy.content.definitions.user') }}</p>
                            <p>{{ __('privacy.content.definitions.personal_data') }}</p>
                            <p>{{ __('privacy.content.definitions.third_party') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('privacy.content.data_collect.title') }}</h5>
                            <p>{{ __('privacy.content.data_collect.intro') }}</p>
                            <p>{{ __('privacy.content.data_collect.registration') }}</p>
                            <p>{{ __('privacy.content.data_collect.geographic') }}</p>
                            <p>{{ __('privacy.content.data_collect.order') }}</p>
                            <p>{{ __('privacy.content.data_collect.technical') }}</p>
                            <p>{{ __('privacy.content.data_collect.cookies') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('privacy.content.data_usage.title') }}</h5>
                            <p>{{ __('privacy.content.data_usage.intro') }}</p>
                            <p>{{ __('privacy.content.data_usage.enable_services') }}</p>
                            <p>{{ __('privacy.content.data_usage.customize_offers') }}</p>
                            <p>{{ __('privacy.content.data_usage.secure_transactions') }}</p>
                            <p>{{ __('privacy.content.data_usage.send_notifications') }}</p>
                            <p>{{ __('privacy.content.data_usage.improve_experience') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('privacy.content.data_protection.title') }}</h5>
                            <p>{{ __('privacy.content.data_protection.intro') }}</p>
                            <p>{{ __('privacy.content.data_protection.ssl') }}</p>
                            <p>{{ __('privacy.content.data_protection.firewalls') }}</p>
                            <p>{{ __('privacy.content.data_protection.access_permissions') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('privacy.content.user_rights.title') }}</h5>
                            <p>{{ __('privacy.content.user_rights.request_copy') }}</p>
                            <p>{{ __('privacy.content.user_rights.edit_delete') }}</p>
                            <p>{{ __('privacy.content.user_rights.disable_account') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('privacy.content.cookie_policy.title') }}</h5>
                            <p>{{ __('privacy.content.cookie_policy.usage') }}</p>
                            <p>{{ __('privacy.content.cookie_policy.browser_settings') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('privacy.content.contact.title') }}</h5>
                            <p>{{ __('privacy.content.contact.intro') }}</p>
                            <p>{{ __('privacy.content.contact.email') }}</p>
                            <p>{{ __('privacy.content.contact.phone') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('privacy.content.updates.title') }}</h5>
                            <p>{{ __('privacy.content.updates.reserve_right') }}</p>
                            <p>{{ __('privacy.content.updates.continued_use') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('privacy.content.abstract.title') }}</h5>
                            <p>{{ __('privacy.content.abstract.text') }}</p>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- ***** Privacy Policy Content End ***** -->

        <!--====== Footer Area Start ======-->
        @include('footer')
        <!--====== Footer Area End ======-->

        <!--====== Modal Responsive Menu Area Start ======-->
        <div id="menu" class="modal fade p-0">
            <div class="modal-dialog modal-dialog-slideout">
                <div class="modal-content full">
                    <div class="modal-header" data-bs-dismiss="modal">
                        {{ __('privacy.menu.title') }} <i class="icon-close"></i>
                    </div>
                    <div class="menu modal-body">
                        <div class="row w-100">
                            <div class="items p-0 col-12 text-center">
                                <!-- Append [navbar] -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--====== Modal Responsive Menu Area End ======-->

        <!--====== Scroll To Top Area Start ======-->
        <div id="scroll-to-top" class="scroll-to-top">
            <a href="#home" class="smooth-anchor">
                <i class="fa-solid fa-arrow-up"></i>
            </a>
        </div>
        <!--====== Scroll To Top Area End ======-->
    </div>

    <!-- ***** All jQuery Plugins ***** -->

    <!-- jQuery(necessary for all JavaScript plugins) -->
    <script src="{{asset('assets/js/vendor/jquery.min.js')}}"></script>

    <!-- Bootstrap js -->
    <script src="{{asset('assets/js/vendor/popper.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/bootstrap.min.js')}}"></script>

    <!-- Plugins js -->
    <script src="{{asset('assets/js/vendor/slider.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/owl.carousel.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/counterup.js')}}"></script>
    <script src="{{asset('assets/js/vendor/waypoint.js')}}"></script>
    <script src="{{asset('assets/js/vendor/aos.js')}}"></script>
    <script src="{{asset('assets/js/vendor/wow.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/countdown.min.js')}}"></script>
    <script src="{{asset('assets/js/vendor/gallery.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.3/purify.min.js"></script>

    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- Main js -->
    <script src="{{asset('assets/js/main.js')}}"></script>
</body>

</html>