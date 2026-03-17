{{-- Early Sync Script: Mencegah glitch sidebar saat load --}}
<script>
    (function() {
        try {
            // Deteksi role dari localStorage key
            var sidebarKey = localStorage.getItem('dig.sidebar.open') !== null 
                ? 'dig.sidebar.open' 
                : (localStorage.getItem('it.sidebar.open') !== null ? 'it.sidebar.open' : 'dig.sidebar.open');
            
            var persisted = localStorage.getItem(sidebarKey) === '1';
            var isDesktop = window.matchMedia('(min-width: 768px)').matches;
            var pageWrapperML = (persisted && isDesktop) ? '18rem' : '4rem';
            var sidebarTransform = (persisted ? 'none' : 'translateX(-100%)');
            var showBackdrop = (!isDesktop && persisted);

            var css = '' +
                'body{visibility:hidden}' +
                '#pageWrapper{margin-left:' + pageWrapperML + ' !important;}' +
                '#sidebar{transform:' + sidebarTransform + ' !important;}' +
                (showBackdrop ? '#sidebarBackdrop{display:block !important;}' : '');
            var s = document.createElement('style');
            s.id = 'early-sync';
            s.appendChild(document.createTextNode(css));
            document.head.appendChild(s);
        } catch (e) {}
    })();
</script>
