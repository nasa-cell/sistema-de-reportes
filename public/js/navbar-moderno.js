/**
 * Navbar Moderno - Interactividad
 * Maneja la navegación, menú móvil y estado activo
 */

(function() {
    'use strict';

    // Selectores
    const hamburger = document.querySelector('.navbar-hamburger');
    const navMenu = document.querySelector('.navbar-menu');
    const navLinks = document.querySelectorAll('.navbar-menu-link');

    // Inicializar si existen los elementos
    if (!hamburger || !navMenu) {
        console.warn('Navbar elements not found. Make sure you have the correct class names.');
        return;
    }

    /**
     * Toggle del menú móvil
     */
    function toggleMobileMenu() {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    }

    /**
     * Cierra el menú móvil
     */
    function closeMobileMenu() {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
    }

    /**
     * Establece el link activo
     */
    function setActiveLink(link) {
        // Remover clase active de todos los links
        navLinks.forEach(l => l.classList.remove('active'));
        
        // Agregar clase active al link
        link.classList.add('active');
    }

    /**
     * Event Listeners
     */

    // Click en hamburger
    if (hamburger) {
        hamburger.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMobileMenu();
        });
    }

    // Click en links del menú
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            // Establecer link activo
            setActiveLink(link);

            // Cerrar menú móvil
            closeMobileMenu();

            // Log para debugging
            const section = link.dataset.section;
            console.log(`✓ Navegando a: ${section}`);

            // Permitir navegación natural
        });
    });

    // Click fuera del navbar (cerrar menú móvil)
    document.addEventListener('click', (e) => {
        const navbar = e.target.closest('.navbar-moderno, .navbar');
        if (!navbar && navMenu.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    // Cerrar menú móvil al redimensionar
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        }, 250);
    });

    /**
     * Función para establecer el link activo por URL o sección
     * Uso: setActiveLinkBySection('inicio')
     */
    window.setActiveLinkBySection = function(section) {
        const link = document.querySelector(`[data-section="${section}"]`);
        if (link) {
            setActiveLink(link);
            closeMobileMenu();
        }
    };

    /**
     * API pública
     */
    window.navbar = {
        toggle: toggleMobileMenu,
        close: closeMobileMenu,
        setActive: setActiveLink,
        setActiveBySection: window.setActiveLinkBySection
    };

    console.log('✓ Navbar moderno cargado correctamente');
})();
