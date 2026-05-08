function syncSidebar() {
    const currentPath = window.location.pathname;
    
    // Reset All: Hapus kelas active dan text-primary lama
    $('.sidebar .nav-link').removeClass('active text-primary');
    
    // Cari yang benar-benar pas (Exact Match)
    $('.sidebar a.nav-link').each(function() {
        const href = $(this).attr('href');
        if (href) {
            try {
                const linkPath = new URL(href, window.location.origin).pathname;
                
                // Jika path sama persis
                if (linkPath === currentPath) {
                    $(this).addClass('active');
                    
                    // Buka parent collapse jika ada
                    const parentCollapse = $(this).closest('.collapse');
                    if (parentCollapse.length) {
                        parentCollapse.addClass('show');
                        const toggleBtn = parentCollapse.prev('.nav-link');
                        toggleBtn.removeClass('collapsed').addClass('text-primary');
                    }
                }
            } catch(e) { }
        }
    });

    // Special Case: Dashboard
    if (currentPath === '/' || currentPath === '/dashboard') {
        $('.sidebar a[href*="dashboard"]').addClass('active');
    }
}

function initCoreLayout() {
    // 2. Global Sidebar Toggle (Universal)
    const isHidden = localStorage.getItem('sidebarHidden') === 'true';
    if (isHidden && window.innerWidth > 992) {
        $('body').addClass('sidebar-hidden');
    }

    $('#global-sidebar-toggle').off('click').on('click', function() {
        if (window.innerWidth > 992) {
            $('body').toggleClass('sidebar-hidden');
            localStorage.setItem('sidebarHidden', $('body').hasClass('sidebar-hidden'));
        } else {
            $('#sidebar').toggleClass('active');
        }
    });

    // 3. Sidebar Dropdown Persistence (LocalStorage)
    const openMenus = JSON.parse(localStorage.getItem('sidebarOpenMenus') || '[]');
    
    $('.collapse').each(function() {
        const id = $(this).attr('id');
        if (openMenus.includes(id) || $(this).hasClass('show')) {
            $(this).addClass('show');
            $(this).prev('.nav-link').removeClass('collapsed');
        } else {
            $(this).removeClass('show');
            $(this).prev('.nav-link').addClass('collapsed');
        }
    });

    $('.collapse').off('shown.bs.collapse hidden.bs.collapse').on('shown.bs.collapse hidden.bs.collapse', function() {
        const currentOpen = $('.collapse.show').map(function() { return $(this).attr('id'); }).get();
        localStorage.setItem('sidebarOpenMenus', JSON.stringify(currentOpen));
        
        if ($(this).hasClass('show')) {
            $(this).prev('.nav-link').removeClass('collapsed');
        } else {
            $(this).prev('.nav-link').addClass('collapsed');
        }
    });

    // Sync sidebar state with current URL
    syncSidebar();
}

$(document).ready(function() {
    initCoreLayout();
});
