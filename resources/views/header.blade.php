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
                    <a class="nav-link" href="{{ route('landing') }}">{{ __('landing.nav.home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('landing') }}#about">{{ __('landing.nav.about') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('terms') }}">{{ __('landing.nav.terms') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('privacy') }}">{{ __('landing.nav.privacy') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link smooth-anchor" href="{{ route('landing') }}#contact">{{ __('landing.nav.contact') }}</a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="languageDropdown">
                            <i class="fas fa-globe {{ app()->isLocale('ar') ? 'ms-2' : 'me-2' }}"></i>
                            {{ app()->isLocale('ar') ? 'العربية' : 'English' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-{{ app()->isLocale('ar') ? 'start' : 'end' }}" aria-labelledby="languageDropdown">
                            <li class="dropdown-header">{{ __('landing.nav.language') }}</li>
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