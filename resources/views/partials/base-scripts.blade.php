{{-- Base Scripts: Sidebar Toggle, Modal Logout, Modal Delete --}}
<script>
    // ===== SIDEBAR TOGGLE =====
    const sidebar = document.getElementById('sidebar');
    const sidebarOpen = document.getElementById('sidebarOpenBtn');
    const sidebarClose = document.getElementById('sidebarCloseBtn');
    const sbBackdrop = document.getElementById('sidebarBackdrop');
    const pageWrapper = document.getElementById('pageWrapper');
    const railLogo = document.getElementById('railLogoBtn');
    const miniSidebar = document.getElementById('miniSidebar');

    // Sidebar key untuk localStorage (akan disesuaikan dengan role nanti)
    const SIDEBAR_OPEN_KEY = 'app.sidebar.open';
    const setPersist = (isOpen) => {
        try { localStorage.setItem(SIDEBAR_OPEN_KEY, isOpen ? '1' : '0'); } catch {}
    };
    const getPersist = () => {
        try { return localStorage.getItem(SIDEBAR_OPEN_KEY) === '1'; } catch { return false; }
    };

    const openSidebar = () => {
        sidebar.style.transform = 'none';
        miniSidebar && miniSidebar.classList.add('md:hidden');
        pageWrapper.style.marginLeft = (window.matchMedia('(min-width:768px)').matches ? '18rem' : '0');
        sbBackdrop && (sbBackdrop.classList.remove('hidden'));
        setPersist(true);
    };
    const closeSidebar = () => {
        sidebar.style.transform = 'translateX(-100%)';
        miniSidebar && miniSidebar.classList.remove('md:hidden');
        pageWrapper.style.marginLeft = (window.matchMedia('(min-width:768px)').matches ? '4rem' : '0');
        sbBackdrop && (sbBackdrop.classList.add('hidden'));
        setPersist(false);
    };

    sidebarOpen && sidebarOpen.addEventListener('click', openSidebar);
    railLogo && railLogo.addEventListener('click', openSidebar);
    sidebarClose && sidebarClose.addEventListener('click', closeSidebar);
    sbBackdrop && sbBackdrop.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });

    const syncOnResize = () => {
        const isDesktop = window.matchMedia('(min-width: 768px)').matches;
        const persistedOpen = getPersist();

        if (isDesktop) {
            if (persistedOpen) {
                sidebar.style.transform = 'none';
                miniSidebar && miniSidebar.classList.add('md:hidden');
                pageWrapper.style.marginLeft = '18rem';
                sbBackdrop && sbBackdrop.classList.add('hidden');
            } else {
                sidebar.style.transform = 'translateX(-100%)';
                miniSidebar && miniSidebar.classList.remove('md:hidden');
                pageWrapper.style.marginLeft = '4rem';
                sbBackdrop && sbBackdrop.classList.add('hidden');
            }
        } else {
            if (persistedOpen) {
                sidebar.style.transform = 'none';
                miniSidebar && miniSidebar.classList.add('md:hidden');
                pageWrapper.style.marginLeft = '0';
                sbBackdrop && sbBackdrop.classList.remove('hidden');
            } else {
                sidebar.style.transform = 'translateX(-100%)';
                miniSidebar && miniSidebar.classList.add('md:hidden');
                pageWrapper.style.marginLeft = '0';
                sbBackdrop && sbBackdrop.classList.add('hidden');
            }
        }
    };

    const reveal = () => {
        const early = document.getElementById('early-sync');
        if (early) early.remove();
        document.body.style.visibility = 'visible';
    };

    syncOnResize();
    reveal();
    window.addEventListener('resize', syncOnResize);

    // Toggle form "Tambah Progress" per project
    document.querySelectorAll('.btn-toggle-progress').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-target');
            const el = document.getElementById(id);
            if (el) el.classList.toggle('hidden');
        });
    });
</script>

<script>
    // ===== MODAL HANDLERS (Logout & Delete) =====
    (function() {
        let pendingLogoutHref = null;
        let pendingDeleteForm = null;

        const logoutModal = document.getElementById('confirmLogoutModal');
        const deleteModal = document.getElementById('confirmDeleteModal');
        const deleteMsgEl = document.getElementById('confirmDeleteMessage');

        const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
        const cancelLogoutBtn = document.getElementById('cancelLogoutBtn');
        const cancelLogout = document.getElementById('cancelLogout');

        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

        function openModal(modal) {
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeModal(modal) {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        // ====== LOGOUT HANDLER ======
        document.querySelectorAll('[data-confirm-logout="true"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                pendingLogoutHref = this.getAttribute('href');
                openModal(logoutModal);
            });
        });

        confirmLogoutBtn?.addEventListener('click', function(e) {
            e.preventDefault();
            if (pendingLogoutHref) {
                window.location.href = pendingLogoutHref;
            }
        });

        cancelLogoutBtn?.addEventListener('click', function() {
            pendingLogoutHref = null;
            closeModal(logoutModal);
        });

        cancelLogout?.addEventListener('click', function() {
            pendingLogoutHref = null;
            closeModal(logoutModal);
        });

        // Klik di luar card = tutup modal logout
        logoutModal?.addEventListener('click', function(e) {
            if (e.target === logoutModal) {
                pendingLogoutHref = null;
                closeModal(logoutModal);
            }
        });

        // ====== DELETE HANDLER (project / progress) ======
        document.querySelectorAll('form[data-confirm-delete="true"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                pendingDeleteForm = this;

                const msg = this.getAttribute('data-message');
                if (msg && deleteMsgEl) {
                    deleteMsgEl.textContent = msg;
                }

                openModal(deleteModal);
            });
        });

        confirmDeleteBtn?.addEventListener('click', function() {
            if (pendingDeleteForm) {
                const formToSubmit = pendingDeleteForm;
                pendingDeleteForm = null;
                closeModal(deleteModal);
                formToSubmit.submit();
            }
        });

        cancelDeleteBtn?.addEventListener('click', function() {
            pendingDeleteForm = null;
            closeModal(deleteModal);
        });

        // Klik di luar card = tutup modal delete
        deleteModal?.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                pendingDeleteForm = null;
                closeModal(deleteModal);
            }
        });

        // ESC key untuk nutup modal (kalau ada yang kebuka)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (logoutModal && !logoutModal.classList.contains('hidden')) {
                    pendingLogoutHref = null;
                    closeModal(logoutModal);
                }
                if (deleteModal && !deleteModal.classList.contains('hidden')) {
                    pendingDeleteForm = null;
                    closeModal(deleteModal);
                }
            }
        });
    })();
</script>
