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

    /* Breadcrumb separator for Arabic */
    .breadcrumb-area .breadcrumb-content .breadcrumb .breadcrumb-item + .breadcrumb-item::before {
        content: "❮";
    }
</style>
@else
<style>
    #content h1 {
        font-size: inherit;
    }
</style>
@endif 