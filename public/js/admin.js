/**
 * Admin panel sidebar: on desktop the sidebar burger hides the sidebar (full width content)
 * and the topbar burger brings it back; on mobile the topbar burger slides it in over an overlay.
 */
(function () {
    "use strict";

    const wrapper = document.querySelector(".admin-wrapper");
    const sidebar = document.getElementById("adminSidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const sidebarBurger = document.getElementById("sidebarBurger");
    const topbarBurger = document.getElementById("sidebarBurgerTopbar");

    if (!wrapper || !sidebar) {
        return;
    }

    const isMobile = () => window.innerWidth <= 1024;

    const setMobileOpen = (open) => {
        sidebar.classList.toggle("active", open);
        overlay?.classList.toggle("active", open);
        document.body.style.overflow = open ? "hidden" : "";
    };

    sidebarBurger?.addEventListener("click", () => {
        if (!isMobile()) {
            wrapper.classList.add("fullscreen");
        }
    });

    topbarBurger?.addEventListener("click", () => {
        if (isMobile()) {
            setMobileOpen(!sidebar.classList.contains("active"));
        } else {
            wrapper.classList.remove("fullscreen");
        }
    });

    overlay?.addEventListener("click", () => setMobileOpen(false));

    window.addEventListener("resize", () => {
        if (!isMobile() && sidebar.classList.contains("active")) {
            setMobileOpen(false);
        }
    });
})();
