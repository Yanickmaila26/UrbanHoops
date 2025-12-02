document.addEventListener('DOMContentLoaded', function () {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
            if (mobileMenu.classList.contains('hidden')) {
                menuIcon.innerHTML = '<path d="M4 6h16M4 12h16M4 18h16"></path>';
            } else {
                menuIcon.innerHTML = '<path d="M6 18L18 6M6 6l12 12"></path>';
            }
        });

        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function () {
                mobileMenu.classList.add('hidden');
                menuIcon.innerHTML = '<path d="M4 6h16M4 12h16M4 18h16"></path>';
            });
        });
    }
});