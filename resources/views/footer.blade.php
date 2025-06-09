<div id="menu" class="modal fade p-0">
    <div class="modal-dialog modal-dialog-slideout">
        <div class="modal-content full">
            <div class="modal-header" data-bs-dismiss="modal">
                {{ __('landing.menu.title') }} 
                <i class="icon-close"></i>
            </div>
            <div class="menu modal-body">
                <div class="container-fluid">
                    <!-- Navigation Links -->
                    <div class="mobile-nav-section">
                        <ul class="mobile-nav-list">
                            <li>
                                <a href="{{ route('landing') }}" >
                                    <i class="fas fa-home"></i>
                                    <span>{{ __('landing.nav.home') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('landing') }}#about" data-bs-dismiss="modal">@lang('landing.nav.about')</a>
                            </li>



                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('landing') }}#features" data-bs-dismiss="modal">@lang('landing.features.title')</a>
                            </li>
                            
                            <li>
                                <a href="{{ route('terms') }}" >
                                    <i class="fas fa-file-contract"></i>
                                    <span>{{ __('landing.nav.terms') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('privacy') }}" >
                                    <i class="fas fa-shield-alt"></i>
                                    <span>{{ __('landing.nav.privacy') }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('landing') }}#contact" >
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ __('landing.nav.contact') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Language Switcher -->
                    <div class="mobile-language-section">
                        <h6>
                            <i class="fas fa-globe"></i>
                            {{ __('landing.nav.language') }}
                        </h6>
                        <div class="language-buttons">
                            <a href="{{ route('changeLang', ['lang' => 'en']) }}" 
                               class="language-btn {{ app()->isLocale('en') ? 'active' : '' }}" 
                               data-bs-dismiss="modal">
                                🇺🇸 English
                            </a>
                            <a href="{{ route('changeLang', ['lang' => 'ar']) }}" 
                               class="language-btn {{ app()->isLocale('ar') ? 'active' : '' }}" 
                               data-bs-dismiss="modal">
                                🇸🇦 العربية
                            </a>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="mobile-social-section">
                        <h6>{{ __('landing.footer.description') }}</h6>
                        <div class="social-buttons">
                            <a href="#" class="social-btn" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-btn" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-btn" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="social-btn" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Mobile Menu Styles - Simplified and Fixed */
.modal#menu {
    z-index: 9999;
}

.modal-dialog-slideout {
    position: fixed;
    margin: 0;
    width: 100vw;
    height: 100vh;
    max-width: none;
    {{ app()->isLocale('ar') ? 'right: 0;' : 'left: 0;' }}
    top: 0;
    transform: {{ app()->isLocale('ar') ? 'translateX(100%)' : 'translateX(-100%)' }};
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog-slideout {
    transform: translateX(0) !important;
}

.modal-content.full {
    height: 100vh;
    border-radius: 0;
    border: none;
    background: #d68d2e;
    color: white;
    overflow: hidden;
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1.5rem;
    background: rgba(0, 0, 0, 0.1);
    font-size: 1.5rem;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    color: white;
}

.modal-header .icon-close {
    font-size: 1.8rem;
    color: white;
    transition: transform 0.2s ease;
}

.modal-header .icon-close:hover {
    transform: rotate(90deg);
}

.menu.modal-body {
    padding: 2rem 1rem;
    overflow-y: auto;
    height: calc(100vh - 100px);
}

/* Navigation Section */
.mobile-nav-section {
    margin-bottom: 2rem;
}

.mobile-nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mobile-nav-list li {
    margin-bottom: 0.8rem;
}

.mobile-nav-list li a {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    color: white !important;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: 500;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.mobile-nav-list li a:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-2px);
    color: white !important;
}

.mobile-nav-list li a i {
    font-size: 1.2rem;
    margin-{{ app()->isLocale('ar') ? 'left' : 'right' }}: 1rem;
    opacity: 0.9;
    width: 20px;
    text-align: center;
}

.mobile-nav-list li a span {
    flex: 1;
}

/* Language Section */
.mobile-language-section {
    margin-bottom: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.mobile-language-section h6 {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.mobile-language-section h6 i {
    margin-{{ app()->isLocale('ar') ? 'left' : 'right' }}: 0.5rem;
}

.language-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.language-btn {
    padding: 0.8rem 1.2rem;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: white !important;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    backdrop-filter: blur(10px);
}

.language-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.4);
    color: white !important;
}

.language-btn.active {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    font-weight: 600;
}

/* Social Section */
.mobile-social-section {
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    text-align: center;
}

.mobile-social-section h6 {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.social-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.social-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
    text-decoration: none;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.social-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-3px);
    color: white;
}

/* Responsive */
@media (max-width: 991px) {
    .navbar-nav.items {
        display: none !important;
    }
    
    .navbar-nav.toggle {
        display: flex !important;
    }
}

@media (min-width: 992px) {
    .navbar-nav.toggle {
        display: none !important;
    }
}

/* Dark overlay */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.8) !important;
}

/* Custom scrollbar */
.menu.modal-body::-webkit-scrollbar {
    width: 6px;
}

.menu.modal-body::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.menu.modal-body::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

.menu.modal-body::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    console.log('Modal navigation script loaded');
    
    // Simple solution: Handle modal navigation directly and prevent jQuery errors
    $('#menu .modal-body a[href*="#"]').each(function() {
        const $link = $(this);
        const originalHref = $link.attr('href');
        
        console.log('Processing link:', originalHref);
        
        // Store the original href in a data attribute
        $link.attr('data-original-href', originalHref);
        
        // Replace problematic URLs with just the hash part for jQuery compatibility
        if (originalHref.includes('#')) {
            const hashPart = originalHref.substring(originalHref.indexOf('#'));
            $link.attr('href', hashPart);
            console.log('Updated href to:', hashPart);
        }
    });
    
    // Handle clicks on modal navigation links
    $('#menu .modal-body a').on('click', function(e) {
        const $link = $(this);
        const originalHref = $link.attr('data-original-href') || $link.attr('href');
        const isExternal = $link.attr('target') === '_blank';
        
        console.log('Link clicked:', originalHref, 'External:', isExternal);
        
        // Don't interfere with external links
        if (isExternal) {
            console.log('External link, allowing default behavior');
            return;
        }
        
        // Handle anchor links
        if (originalHref && originalHref.includes('#')) {
            const hashPart = originalHref.substring(originalHref.indexOf('#'));
            const urlWithoutHash = originalHref.substring(0, originalHref.indexOf('#'));
            const currentPathname = window.location.pathname;
            const currentOrigin = window.location.origin;
            
            console.log('Hash part:', hashPart);
            console.log('URL without hash:', urlWithoutHash);
            console.log('Current pathname:', currentPathname);
            
            // Check if we're navigating to the same page or to the landing page
            const isLandingPage = urlWithoutHash === currentOrigin || urlWithoutHash === currentOrigin + '/' || urlWithoutHash === '';
            const isCurrentPage = currentPathname === '/' || currentPathname === '';
            
            console.log('Is landing page URL:', isLandingPage);
            console.log('Is current page landing:', isCurrentPage);
            
            // If we're already on the landing page and clicking an anchor link
            if (isLandingPage && isCurrentPage) {
                console.log('Same page anchor navigation');
                e.preventDefault();
                e.stopPropagation();
                
                // Close the modal
                $('#menu').modal('hide');
                
                // Navigate after modal is hidden
                $('#menu').on('hidden.bs.modal.modalNav', function() {
                    $(this).off('hidden.bs.modal.modalNav');
                    
                    console.log('Modal hidden, scrolling to:', hashPart);
                    const targetElement = $(hashPart);
                    if (targetElement.length) {
                        $('html, body').animate({
                            scrollTop: targetElement.offset().top
                        }, 500);
                        
                        // Update URL
                        if (window.history.pushState) {
                            window.history.pushState(null, null, hashPart);
                        }
                    }
                });
                return false;
            } 
            // If we need to navigate to landing page with anchor
            else if (isLandingPage && !isCurrentPage) {
                console.log('Navigate to landing page with anchor');
                e.preventDefault();
                e.stopPropagation();
                
                // Close the modal and navigate to the landing page
                $('#menu').modal('hide');
                
                $('#menu').on('hidden.bs.modal.modalNav', function() {
                    $(this).off('hidden.bs.modal.modalNav');
                    console.log('Modal hidden, navigating to landing page with hash:', originalHref);
                    window.location.href = originalHref;
                });
                return false;
            }
        }
        
        // Handle full page navigation (terms, privacy, etc.) - no anchors
        if (originalHref && !originalHref.includes('#') && originalHref !== '') {
            console.log('Handling full page navigation to:', originalHref);
            
            // Let Bootstrap handle the modal dismissal for these
            if ($link.attr('data-bs-dismiss') === 'modal') {
                console.log('Has data-bs-dismiss, allowing default behavior');
                return; // Let the browser handle navigation normally
            }
            
            // For links without data-bs-dismiss, close modal manually
            console.log('No data-bs-dismiss, closing modal manually');
            e.preventDefault();
            $('#menu').modal('hide');
            
            setTimeout(function() {
                window.location.href = originalHref;
            }, 300);
        }
    });
});
</script>