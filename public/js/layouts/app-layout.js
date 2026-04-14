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
    // 1. Sidebar Toggle (Mobile)
    $('#btn-toggle').off('click').on('click', function() {
        $('#sidebar').toggleClass('active');
    });

    // 2. Sidebar Dropdown Persistence (LocalStorage)
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

    // 3. Global Notifications (Swal)
    if (window.flashMessages) {
        if (window.flashMessages.success && window.flashMessages.success !== "" && window.flashMessages.success !== "null") {
            Swal.fire({ 
                icon: 'success', 
                title: 'Berhasil!', 
                text: window.flashMessages.success, 
                timer: 2500, 
                showConfirmButton: false,
                background: 'rgba(255, 255, 255, 0.95)',
                backdrop: 'rgba(0, 88, 168, 0.05)'
            });
            window.flashMessages.success = "null";
        }
        if (window.flashMessages.error && window.flashMessages.error !== "" && window.flashMessages.error !== "null") {
            Swal.fire({ 
                icon: 'error', 
                title: 'Gagal!', 
                text: window.flashMessages.error, 
                confirmButtonColor: '#0058a8' 
            });
            window.flashMessages.error = "null";
        }
    }
}

// --- INITIALIZE SWUP ---
const swup = new Swup({
    containers: ["#swup"],
    animationSelector: '[class*="transition-"]',
    cache: true
});

swup.hooks.on('content:replace', () => {
    initCoreLayout();
    if (typeof window.initPageScripts === 'function') {
        window.initPageScripts();
    }
});

// Progress Bar Logic
swup.hooks.on('visit:start', () => {
    $('#progress-bar').css('width', '30%');
});
swup.hooks.on('visit:end', () => {
    $('#progress-bar').css('width', '100%');
    setTimeout(() => $('#progress-bar').css('width', '0'), 300);
});

$(document).ready(function() {
    initCoreLayout();
});
