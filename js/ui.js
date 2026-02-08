document.addEventListener('DOMContentLoaded', () => {

  /* ===============================
     MENÚ DESPLEGABLE SUPERIOR
  =============================== */
  const btnDropdownTop = document.getElementById('btn-dropdown-menu-top');
  const dropdownTop   = document.getElementById('dropdownMenu-top');

  if (btnDropdownTop && dropdownTop) {
    btnDropdownTop.addEventListener('click', () => {
      const abierto = !dropdownTop.classList.contains('hidden');
      dropdownTop.classList.toggle('hidden');
      btnDropdownTop.setAttribute('aria-expanded', String(!abierto));
    });
  }

  /* ===============================
     MENÚ HAMBURGUESA (MÓVIL)
  =============================== */
  const btnMobile = document.getElementById('btn-mobile-menu');
  const mobileMenu = document.getElementById('mobileMenu');

  if (btnMobile && mobileMenu) {
    btnMobile.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  }

  /* ===============================
     MENÚ DESPLEGABLE INFERIOR
  =============================== */
  const btnDropdown = document.getElementById('btn-dropdown-menu');
  const dropdownMenu = document.getElementById('dropdownMenu');

  if (btnDropdown && dropdownMenu) {
    btnDropdown.addEventListener('click', () => {
      dropdownMenu.classList.toggle('hidden');
    });
  }

  /* ===============================
     MENÚ FOOTER / LEGAL
  =============================== */
  const btnDropdownFooter = document.getElementById('btn-dropdown-menu-footer');
  const dropdownFooter   = document.getElementById('dropdownMenu-footer');

  if (btnDropdownFooter && dropdownFooter) {
    btnDropdownFooter.addEventListener('click', () => {
      dropdownFooter.classList.toggle('hidden');
    });
  }

  /* ===============================
     COOKIES
  =============================== */
  const btnCookie = document.getElementById('btn-cookie-accept');
  const cookieBanner = document.getElementById('cookie-banner');

  if (btnCookie && cookieBanner) {
    btnCookie.addEventListener('click', () => {
      cookieBanner.style.display = 'none';
    });
  }

});
