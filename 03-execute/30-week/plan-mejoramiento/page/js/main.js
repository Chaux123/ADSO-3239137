
/*
 * Toy Library - Public Landing Page
 * Main JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
  const menuButton = document.querySelector('.menu-toggle');

  const navigation = document.querySelector(
    '#main-navigation'
  );

  const copyright = document.querySelector(
    '#copyright'
  );

  // Mobile navigation
  if (menuButton && navigation) {
    function closeMenu() {
      navigation.classList.remove('is-open');

      menuButton.setAttribute(
        'aria-expanded',
        'false'
      );

      menuButton.setAttribute(
        'aria-label',
        'Abrir menú'
      );
    }

    function openMenu() {
      navigation.classList.add('is-open');

      menuButton.setAttribute(
        'aria-expanded',
        'true'
      );

      menuButton.setAttribute(
        'aria-label',
        'Cerrar menú'
      );
    }

    menuButton.addEventListener('click', () => {
      const isOpen = navigation.classList.contains(
        'is-open'
      );

      if (isOpen) {
        closeMenu();
      } else {
        openMenu();
      }
    });

    // Close menu after selecting a link.
    navigation.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        closeMenu();
      });
    });

    // Close menu using the Escape key.
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeMenu();
      }
    });

    // Close menu when clicking outside.
    document.addEventListener('click', (event) => {
      const clickedInsideNavigation =
        navigation.contains(event.target);

      const clickedMenuButton =
        menuButton.contains(event.target);

      if (
        !clickedInsideNavigation &&
        !clickedMenuButton
      ) {
        closeMenu();
      }
    });

    // Reset mobile menu on larger screens.
    const desktopMedia = window.matchMedia(
      '(min-width: 769px)'
    );

    function handleScreenChange(event) {
      if (event.matches) {
        closeMenu();
      }
    }

    desktopMedia.addEventListener(
      'change',
      handleScreenChange
    );
  }

  // Automatically update the footer year.
  if (copyright) {
    const currentYear = new Date().getFullYear();

    copyright.textContent =
      `© ${currentYear} Toy Library. Todos los derechos reservados.`;
  }
});
