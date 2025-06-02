<!DOCTYPE html>
<html class="no-js" lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<!-- change from 'rtl' to 'ltr' to switch direction -->

<head>
    <meta charset="UTF-8" />
    <meta name="description" content="{{ __('terms.meta_description') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title  -->
    <title>{{ __('terms.page_title') }}</title>

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
        <header id="header">
            <!-- Navbar -->
            <nav data-aos="zoom-out" data-aos-delay="800" class="navbar gameon-navbar navbar-expand">
                <div class="container header">
                    <!-- Logo -->
                    <a class="navbar-brand" href="{{ route('landing') }}">
                        <img src="{{asset('assets/images/logo.svg')}}" alt="Gold Station" width="100" />
                    </a>

                    <div class="ms-auto"></div>

                    <!-- Navbar Nav -->
                    <ul class="navbar-nav items ms-auto">
                        <li class="nav-item">
                            <a class="nav-link smooth-anchor" href="{{ route('landing') }}#home">{{ __('terms.nav.home') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('landing') }}#about">{{ __('terms.footer.links.about') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('terms') }}">{{ __('terms.nav.terms') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('privacy') }}">{{ __('terms.nav.privacy') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link smooth-anchor" href="{{ route('landing') }}#contact">{{ __('terms.nav.contact') }}</a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="languageDropdown">
                                    <i class="fas fa-globe {{ app()->isLocale('ar') ? 'ms-2' : 'me-2' }}"></i>
                                    {{ app()->isLocale('ar') ? 'العربية' : 'English' }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-{{ app()->isLocale('ar') ? 'start' : 'end' }}" aria-labelledby="languageDropdown">
                                    <li class="dropdown-header">{{ __('terms.nav.language') }}</li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-black {{ app()->isLocale('en') ? 'active' : '' }}" href="{{ route('changeLang', ['lang' => 'en']) }}">
                                            <i class="fas fa-flag-usa me-2"></i> English
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-black {{ app()->isLocale('ar') ? 'active' : '' }}" href="{{ route('changeLang', ['lang' => 'ar']) }}">
                                            <i class="fas fa-flag me-2"></i> العربية
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>

                    <!-- Navbar Toggler -->
                    <ul class="navbar-nav toggle">
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#menu">
                                <i class="icon-menu m-0"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ***** Header End ***** -->

        <!-- ***** Breadcrumb Area Start ***** -->
        <section id="home" class="breadcrumb-area layout-2 has-overlay overlay-gradient d-flex align-items-center">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Breamcrumb Content -->
                        <div class="breadcrumb-content text-center">
                            <h2 class="text-white">{{ __('terms.breadcrumb.title') }}</h2>
                            <ol class="breadcrumb d-flex justify-content-center">
                                <li class="breadcrumb-item">
                                    <a class="text-white" href="{{ route('landing') }}">{{ __('terms.breadcrumb.home') }}</a>
                                </li>
                                <li class="breadcrumb-item text-white active">
                                    {{ __('terms.breadcrumb.title') }}
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Breadcrumb Area End ***** -->

        <!-- ***** Terms & Conditions Content Start ***** -->
        <section class="terms-wrapper">
            <div class="container">
                <div class="content" id="content">
                    <ul>
                        <li>
                            <h5>{{ __('terms.content.registration.title') }}</h5>
                            <p>{{ __('terms.content.registration.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.content_use.title') }}</h5>
                            <p>{{ __('terms.content.content_use.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.user_obligations.title') }}</h5>
                            <p>{{ __('terms.content.user_obligations.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.offer_policy.title') }}</h5>
                            <p>{{ __('terms.content.offer_policy.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.updates_maintenance.title') }}</h5>
                            <p>{{ __('terms.content.updates_maintenance.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.complaints_feedback.title') }}</h5>
                            <p>{{ __('terms.content.complaints_feedback.text') }}</p>
                        </li>
                        <li>
                            <h5>{{ __('terms.content.account_termination.title') }}</h5>
                            <p>{{ __('terms.content.account_termination.text') }}</p>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- ***** Terms & Conditions Content End ***** -->

        <!--====== Footer Area Start ======-->
        <footer class="footer-area p-0">
            <!-- Footer Top -->
            <div class="footer-top">
                <div class="container">
                    <div class="row items">
                        <div class="col-12 col-sm-6 col-lg-4 item">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <!-- Logo -->
                                <a class="navbar-brand mb-6" href="{{ route('landing') }}">
                                    <img class="logo" src="{{asset('assets/images/logo.svg')}}" alt="" height="35" />
                                </a>
                                <p class="slug mt-3">
                                    {{ __('terms.footer.description') }}
                                </p>

                                <hr />

                                <!-- Social Icons -->
                                <div class="social-icons d-flex mt-3">
                                    <a class="icon has-overlay" href="#">
                                        <i class="fa-brands fa-facebook-f"></i>
                                        <i class="fa-brands fa-facebook-f"></i>
                                    </a>
                                    <a class="icon has-overlay" href="#">
                                        <i class="fa-brands fa-x-twitter"></i>
                                        <i class="fa-brands fa-x-twitter"></i>
                                    </a>
                                    <a class="icon has-overlay" href="#">
                                        <i class="fa-brands fa-linkedin-in"></i>
                                        <i class="fa-brands fa-linkedin-in"></i>
                                    </a>
                                    <a class="icon has-overlay" href="#">
                                        <i class="fa-brands fa-github"></i>
                                        <i class="fa-brands fa-github"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 item">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <h4 class="footer-title mt-0">
                                    {{ __('terms.footer.useful_links') }}
                                </h4>

                                <!-- Navigation -->
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('landing') }}">{{ __('privacy.nav.home') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('landing') }}#about">{{ __('terms.footer.links.about') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('privacy') }}">{{ __('terms.footer.links.privacy') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('terms') }}">{{ __('terms.footer.links.terms') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('landing') }}#contact">{{ __('terms.footer.links.contact') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4 item">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <h4 class="footer-title mt-0">{{ __('terms.footer.download_title') }}</h4>

                                <!-- Download Button -->
                                <div class="button-group download-button">
                                    <a href="#">
                                        <img src="{{asset('assets/img/content/google-play-black.png')}}" alt="" />
                                    </a>
                                    <a href="#">
                                        <img src="{{asset('assets/img/content/app-store-black.png')}}" alt="" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <!-- Copyright Area -->
                            <div
                                class="copyright-area d-flex flex-wrap justify-content-center align-items-center text-center py-4">
                                <span>{{ __('terms.footer.copyright') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!--====== Footer Area End ======-->

        <!--====== Modal Responsive Menu Area Start ======-->
        <div id="menu" class="modal fade p-0">
            <div class="modal-dialog modal-dialog-slideout">
                <div class="modal-content full">
                    <div class="modal-header" data-bs-dismiss="modal">
                        {{ __('terms.menu.title') }} <i class="icon-close"></i>
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