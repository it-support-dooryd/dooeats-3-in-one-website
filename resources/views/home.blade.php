@include('layouts.app')
@include('layouts.header')

<!-- Minimalist Landing Page -->
<div class="landing-hero">
    <div class="container">
        <div class="hero-content">
            <!-- Hero Text -->
            <div class="hero-text-section">
                <h1 class="hero-title">
                    Delicious Food,
                    <span class="hero-title-accent">Delivered Fast</span>
                </h1>
                <p class="hero-subtitle">
                    Discover amazing restaurants near you and get your favorite meals delivered to your doorstep
                </p>
                
                <!-- Location Search Box -->
                <div class="hero-location-box">
                    <div class="location-input-wrapper">
                        <i class="feather-map-pin location-icon"></i>
                        <input 
                            type="text" 
                            id="landing_location_input" 
                            class="location-search-input" 
                            placeholder="Enter your delivery address"
                        >
                        <button class="location-search-btn" onclick="navigateToDiscovery()">
                            <span class="btn-text">Find Restaurants</span>
                            <i class="feather-arrow-right btn-icon"></i>
                        </button>
                    </div>
                    <p class="location-hint">
                        <i class="feather-info"></i>
                        We'll show you the best restaurants in your area
                    </p>
                </div>
            </div>

            <!-- Hero Visual -->
            <div class="hero-visual-section">
                <div class="hero-image-container">
                    <div class="floating-card card-1">
                        <i class="feather-shopping-bag"></i>
                        <span>Fast Delivery</span>
                    </div>
                    <div class="floating-card card-2">
                        <i class="feather-star"></i>
                        <span>Top Rated</span>
                    </div>
                    <div class="floating-card card-3">
                        <i class="feather-percent"></i>
                        <span>Great Offers</span>
                    </div>
                    <div class="hero-main-circle"></div>
                </div>
            </div>
        </div>

        <!-- Quick Features -->
        <div class="quick-features">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="feather-zap"></i>
                </div>
                <h3>Quick Delivery</h3>
                <p>Get your food delivered in 30 minutes or less</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="feather-award"></i>
                </div>
                <h3>Quality Food</h3>
                <p>Only the best restaurants in your neighborhood</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="feather-shield"></i>
                </div>
                <h3>Safe & Secure</h3>
                <p>Your orders are protected and tracked</p>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<!-- Landing Page Styles -->
<style>
/* ========================================
   MINIMALIST LANDING PAGE DESIGN
   ======================================== */

.landing-hero {
    min-height: calc(100vh - 120px);
    background: var(--bg-body);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

/* Subtle background pattern */
.landing-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(4, 120, 87, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(5, 150, 105, 0.05) 0%, transparent 50%);
    pointer-events: none;
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
    position: relative;
    z-index: 1;
}

/* Hero Text Section */
.hero-text-section {
    animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-title {
    font-size: 64px;
    font-weight: 800;
    line-height: 1.1;
    color: var(--text-main);
    margin-bottom: 24px;
    letter-spacing: -1px;
}

.hero-title-accent {
    display: block;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 20px;
    color: var(--text-muted);
    margin-bottom: 48px;
    line-height: 1.6;
    max-width: 500px;
}

/* Location Search Box */
.hero-location-box {
    background: linear-gradient(135deg, rgba(30, 30, 30, 0.95) 0%, rgba(45, 45, 45, 0.95) 100%);
    border-radius: 24px;
    padding: 32px;
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(4, 120, 87, 0.2);
    transition: all 0.3s ease;
}

.hero-location-box:hover {
    border-color: rgba(4, 120, 87, 0.4);
    box-shadow: 
        0 25px 70px rgba(4, 120, 87, 0.15),
        0 20px 60px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

.location-input-wrapper {
    display: flex;
    align-items: center;
    gap: 16px;
    background: rgba(45, 45, 45, 0.8);
    border-radius: 60px;
    padding: 8px 8px 8px 24px;
    border: 2px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.location-input-wrapper:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(4, 120, 87, 0.1);
}

.location-icon {
    color: var(--primary-color);
    font-size: 24px;
    flex-shrink: 0;
}

.location-search-input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    color: var(--text-main);
    font-size: 16px;
    font-weight: 500;
    padding: 12px 0;
}

.location-search-input::placeholder {
    color: var(--text-muted);
    opacity: 0.7;
}

.location-search-btn {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: #fff;
    border: none;
    border-radius: 50px;
    padding: 14px 32px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(4, 120, 87, 0.3);
}

.location-search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(4, 120, 87, 0.4);
}

.location-search-btn:active {
    transform: translateY(0);
}

.btn-icon {
    font-size: 18px;
    transition: transform 0.3s ease;
}

.location-search-btn:hover .btn-icon {
    transform: translateX(4px);
}

.location-hint {
    margin-top: 20px;
    margin-bottom: 0;
    color: var(--text-muted);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.location-hint i {
    font-size: 16px;
    color: var(--primary-color);
}

/* Hero Visual Section */
.hero-visual-section {
    position: relative;
    animation: fadeInRight 0.8s ease-out 0.2s both;
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.hero-image-container {
    position: relative;
    width: 100%;
    height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-main-circle {
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(4, 120, 87, 0.15) 0%, rgba(5, 150, 105, 0.15) 100%);
    border: 2px solid rgba(4, 120, 87, 0.2);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.8;
    }
    50% {
        transform: scale(1.05);
        opacity: 1;
    }
}

.floating-card {
    position: absolute;
    background: linear-gradient(135deg, rgba(30, 30, 30, 0.95) 0%, rgba(45, 45, 45, 0.95) 100%);
    border-radius: 20px;
    padding: 20px 28px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(4, 120, 87, 0.2);
    animation: float 3s ease-in-out infinite;
}

.floating-card i {
    font-size: 24px;
    color: var(--primary-color);
}

.floating-card span {
    color: var(--text-main);
    font-weight: 600;
    font-size: 16px;
}

.card-1 {
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.card-2 {
    top: 50%;
    right: 5%;
    animation-delay: 1s;
}

.card-3 {
    bottom: 15%;
    left: 15%;
    animation-delay: 2s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

/* Quick Features */
.quick-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
    margin-top: 100px;
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

.feature-item {
    text-align: center;
    padding: 40px 24px;
    background: linear-gradient(135deg, rgba(30, 30, 30, 0.6) 0%, rgba(45, 45, 45, 0.6) 100%);
    border-radius: 20px;
    border: 2px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-8px);
    border-color: rgba(4, 120, 87, 0.3);
    box-shadow: 0 15px 40px rgba(4, 120, 87, 0.15);
}

.feature-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 24px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(4, 120, 87, 0.3);
}

.feature-icon i {
    font-size: 32px;
    color: #fff;
}

.feature-item h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 12px;
}

.feature-item p {
    font-size: 15px;
    color: var(--text-muted);
    line-height: 1.6;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .hero-title {
        font-size: 56px;
    }
}

@media (max-width: 992px) {
    .hero-content {
        grid-template-columns: 1fr;
        gap: 60px;
    }
    
    .hero-title {
        font-size: 48px;
    }
    
    .hero-visual-section {
        display: none;
    }
    
    .quick-features {
        grid-template-columns: 1fr;
        gap: 24px;
    }
}

@media (max-width: 768px) {
    .landing-hero {
        padding: 40px 0;
    }
    
    .hero-title {
        font-size: 40px;
    }
    
    .hero-subtitle {
        font-size: 18px;
    }
    
    .hero-location-box {
        padding: 24px;
    }
    
    .location-input-wrapper {
        flex-direction: column;
        padding: 16px;
        gap: 12px;
    }
    
    .location-search-input {
        width: 100%;
        text-align: center;
    }
    
    .location-search-btn {
        width: 100%;
        justify-content: center;
    }
    
    .btn-text {
        display: block;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 32px;
    }
    
    .hero-subtitle {
        font-size: 16px;
    }
}
</style>

<!-- Landing Page JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const locationInput = document.getElementById('landing_location_input');
    
    // Initialize Google Places Autocomplete if available
    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
        const autocomplete = new google.maps.places.Autocomplete(locationInput, {
            types: ['address']
        });
        
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                // Store location and navigate
                storeLocationAndNavigate(place);
            }
        });
    }
    
    // Handle Enter key
    locationInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            navigateToDiscovery();
        }
    });
});

function navigateToDiscovery() {
    const locationInput = document.getElementById('landing_location_input');
    const address = locationInput.value.trim();
    
    if (address) {
        // If location is provided, try to geocode it
        if (typeof google !== 'undefined' && google.maps) {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: address }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const location = results[0].geometry.location;
                    const lat = location.lat();
                    const lng = location.lng();
                    
                    // Store in cookies
                    setCookie('address_lat', lat, 365);
                    setCookie('address_lng', lng, 365);
                    setCookie('address_name', address, 365);
                    
                    // Navigate to restaurants page
                    window.location.href = '{{ url("restaurants") }}';
                } else {
                    // If geocoding fails, still navigate
                    window.location.href = '{{ url("restaurants") }}';
                }
            });
        } else {
            // If Google Maps not available, just navigate
            window.location.href = '{{ url("restaurants") }}';
        }
    } else {
        // No address provided, navigate to restaurants page anyway
        window.location.href = '{{ url("restaurants") }}';
    }
}

function storeLocationAndNavigate(place) {
    const lat = place.geometry.location.lat();
    const lng = place.geometry.location.lng();
    const address = place.formatted_address;
    
    // Store in cookies
    setCookie('address_lat', lat, 365);
    setCookie('address_lng', lng, 365);
    setCookie('address_name', address, 365);
    
    // Navigate to restaurants page
    window.location.href = '{{ url("restaurants") }}';
}

function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
}
</script>
