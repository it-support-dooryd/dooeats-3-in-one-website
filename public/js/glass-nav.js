// Glassmorphic Navigation JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // User dropdown toggle
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdownMenu = document.getElementById('userDropdownMenu');

    if (userMenuButton && userDropdownMenu) {
        userMenuButton.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdownMenu.style.display = userDropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!userMenuButton.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                userDropdownMenu.style.display = 'none';
            }
        });
    }

    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenuModal = document.getElementById('mobileMenuModal');

    if (mobileMenuToggle && mobileMenuModal) {
        mobileMenuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            mobileMenuModal.style.display = mobileMenuModal.style.display === 'flex' ? 'none' : 'flex';

            // Toggle hamburger animation
            const spans = mobileMenuToggle.querySelectorAll('span');
            if (mobileMenuModal.style.display === 'flex') {
                spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(5px, -5px)';
            } else {
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!mobileMenuToggle.contains(e.target) && !mobileMenuModal.contains(e.target)) {
                mobileMenuModal.style.display = 'none';

                // Reset hamburger animation
                const spans = mobileMenuToggle.querySelectorAll('span');
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });
    }

    // Update cart count dynamically
    function updateCartCount() {
        const cartBadge = document.getElementById('cart-count');
        // This would be populated from your cart logic
        // For now, just showing as an example
        const cartItems = 0; // Replace with actual cart count
        if (cartBadge && cartItems > 0) {
            cartBadge.textContent = cartItems;
            cartBadge.style.display = 'block';
        }
    }

    updateCartCount();

    // Update Location Text from Cookie
    function updateLocationText() {
        const locationTextElement = document.getElementById('current-location-text');
        if (locationTextElement) {
            const addressName = getCookie('address_name');
            if (addressName) {
                // Truncate if too long
                const maxLength = 30;
                const truncatedName = addressName.length > maxLength ? addressName.substring(0, maxLength) + '...' : addressName;
                locationTextElement.textContent = truncatedName;
            }
        }
    }

    // Helper to get cookie
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    updateLocationText();

    // ========================================
    // ENHANCED LOCATION SELECTOR INTERACTION
    // ========================================

    const locationPill = document.querySelector('.pill-location');
    const locationInput = document.getElementById('user_locationnew');
    const chevronIcon = document.querySelector('.location-dropdown-container i.feather-chevron-down');

    if (locationPill && locationInput) {
        // Make the entire pill clickable to focus the input
        locationPill.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Remove readonly to allow editing
            locationInput.removeAttribute('readonly');
            locationInput.focus();

            // Add active state animation
            locationPill.style.transform = 'translateY(0)';

            // Rotate chevron
            if (chevronIcon) {
                chevronIcon.style.transform = 'rotate(180deg)';
            }

            // Trigger autocomplete if available
            if (typeof locationInput.click === 'function') {
                setTimeout(() => {
                    locationInput.click();
                }, 100);
            }
        });

        // Enhanced focus handling
        locationInput.addEventListener('focus', function () {
            locationPill.classList.add('location-active');

            // Add glow effect
            locationPill.style.boxShadow = `
                0 8px 30px rgba(4, 120, 87, 0.35),
                0 4px 20px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.15)
            `;
        });

        // Enhanced blur handling with smooth transition
        locationInput.addEventListener('blur', function () {
            setTimeout(() => {
                locationInput.setAttribute('readonly', 'readonly');
                locationPill.classList.remove('location-active');

                // Reset styles
                locationPill.style.transform = '';
                locationPill.style.boxShadow = '';

                // Reset chevron rotation
                if (chevronIcon) {
                    chevronIcon.style.transform = '';
                }
            }, 200);
        });

        // Add keyboard support
        locationInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                locationInput.blur();
            }
        });

        // Prevent form submission on Enter
        locationInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    }
});
