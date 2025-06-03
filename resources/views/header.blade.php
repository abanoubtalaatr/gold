<header id="header">
    <!-- Navbar -->
    <nav data-aos="zoom-out" data-aos-delay="800" class="navbar gameon-navbar navbar-expand">
        <div class="container header">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="assets/images/logo.svg" alt="Gold Station" width="100" />
            </a>

            <div class="ms-auto"></div>

            <!-- Navbar Nav -->
            <ul class="navbar-nav items ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('landing') }}">@lang('landing.nav.home')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('landing') }}#about">@lang('landing.nav.about')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/terms') }}">@lang('landing.nav.terms')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/privacy') }}">@lang('landing.nav.privacy')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="{{ route('landing') }}#contact">@lang('landing.nav.contact')</a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <!-- Language Switcher Dropdown -->
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="me-1">@lang('landing.nav.language')</span>
                            <i class="fas fa-globe fa-sm"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                            <li>
                                <a class="dropdown-item text-black {{ app()->getLocale() == 'en' ? 'active' : '' }}" 
                                   href="{{ route('changeLang', ['lang' => 'en']) }}">
                                    <i class="flag-icon flag-icon-us me-2"></i>
                                    @lang('english')
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-black {{ app()->getLocale() == 'ar' ? 'active' : '' }}" 
                                   href="{{ route('changeLang', ['lang' => 'ar']) }}">
                                    <i class="flag-icon flag-icon-sa me-2"></i>
                                    @lang('arabic')
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

    <!-- Language Switcher Styles -->
    <style>
        .dropdown-menu .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .dropdown-menu .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #495057;
        }
        
        .dropdown-menu .dropdown-item.active {
            background-color: #007bff;
            color: white;
        }
        
        .flag-icon {
            width: 20px;
            height: 15px;
            background-size: cover;
            display: inline-block;
            border-radius: 2px;
        }
        
        .flag-icon-us {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30"><rect width="60" height="30" fill="%23b22234"/><path d="M0,2.31h60v2.31h-60zm0,4.61h60v2.31h-60zm0,4.61h60v2.31h-60zm0,4.61h60v2.31h-60zm0,4.61h60v2.31h-60zm0,4.61h60v2.31h-60z" fill="%23fff"/><rect width="24" height="15.4" fill="%233c3b6e"/></svg>');
        }
        
        .flag-icon-sa {
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30"><rect width="60" height="30" fill="%23006C35"/><text x="30" y="18" text-anchor="middle" fill="white" font-size="12" font-family="Arial">ع</text></svg>');
        }
        
        /* RTL Support */
        @if(app()->getLocale() == 'ar')
            .navbar-nav {
                direction: rtl;
            }
            
            .dropdown-menu-end {
                right: auto !important;
                left: 0 !important;
            }
            
            .me-1, .me-2 {
                margin-right: 0 !important;
                margin-left: 0.25rem !important;
            }
            
            .ms-auto {
                margin-left: 0 !important;
                margin-right: auto !important;
            }
        @endif
    </style>
</header>