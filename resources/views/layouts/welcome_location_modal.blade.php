<div class="modal fade welcome-location-modal" id="welcomeLocationModal" tabindex="-1" role="dialog" aria-labelledby="welcomeLocationModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 25px; border: 4px solid #D62828; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); width: 552px; height: 600px;">
            <div class="modal-body text-center d-flex flex-column align-items-center justify-content-center p-0">
                
                <!-- Icon (Chef Hat) -->
                <div class="mb-3">
                     <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="#D62828" stroke="currentColor" stroke-width="0" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2C8 2 6 5 6 5s-2 0-3 2c-1.5 3 1.5 6 1.5 6H19.5c0 0 3-3 1.5-6-1-2-3-2-3-2s-2-3-6-3z"/>
                        <rect x="6" y="14" width="12" height="6" rx="1"/>
                        <line x1="6" y1="17" x2="18" y2="17" stroke="#fff" stroke-width="2"/>
                     </svg>
                </div>

                <!-- Title -->
                <h4 class="font-weight-bold mb-2" style="color: #000; font-size: 26px; line-height: 1.2;">Setup Your Delivery<br>Location</h4>
                
                <!-- Subtitle (Red) -->
                <h6 class="font-weight-bold mb-3" style="color: #D62828; font-size: 16px;">Welcome to DooEats</h6>
                
                <!-- Description -->
                <p class="text-muted mb-4" style="font-size: 14px; max-width: 90%; color: #888;">
                    To provide the best experience accurate delivery times.
                </p>

                <!-- Input -->
                <div class="location-search-wrapper w-100 mb-4" style="position: relative;">
                    <div class="input-group d-flex align-items-center" style="background: #F3F3F3; border-radius: 12px; padding: 8px 15px;">
                        <div class="input-group-prepend mr-2">
                            <span class="text-danger"><i class="feather-map-pin" style="color: #D62828; font-size: 18px;"></i></span>
                        </div>
                        <input type="text" id="welcome_modal_address" class="form-control border-0 bg-transparent location-search-input p-0" placeholder="Enter your delivery address..." style="box-shadow: none; font-size: 15px; color: #333; height: auto;">
                    </div>
                </div>

                <!-- Button -->
                <button type="button" class="btn btn-danger w-100 rounded-pill font-weight-bold py-3 mb-3 shadow-sm" id="btn_confirm_location" style="background-color: #D62828; border-color: #D62828; font-size: 16px;">
                    Confirm Location
                </button>

                <!-- Link -->
                <a href="javascript:void(0)" onclick="useCurrentLocation()" class="font-weight-bold mt-2" style="text-decoration: none; color: #D62828; font-size: 15px;">
                    Use My Current Location
                </a>

                <!-- Hidden inputs -->
                <input type="hidden" id="welcome_modal_lat">
                <input type="hidden" id="welcome_modal_lng">
                <input type="hidden" id="welcome_modal_address_name">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if location is already set in cookies
        const addressName = getCookie('address_name');
        if (!addressName) {
            $('#welcomeLocationModal').modal('show');
            
            // Poll for Google Maps
            const checkGoogleMaps = setInterval(function() {
                if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                    initWelcomeAutocomplete();
                    clearInterval(checkGoogleMaps);
                }
            }, 500);
        }

        $('#btn_confirm_location').on('click', function() {
            const address = $('#welcome_modal_address').val();
            const lat = $('#welcome_modal_lat').val();
            const lng = $('#welcome_modal_lng').val();
            const address_name = $('#welcome_modal_address_name').val() || address;

            if (!address || !lat || !lng) {
                alert('Please select a valid address from the suggestions.');
                return;
            }

            saveLocationAndContinue(address, lat, lng, address_name);
        });
    });

    function initWelcomeAutocomplete() {
        const input = document.getElementById('welcome_modal_address');
        const autocomplete = new google.maps.places.Autocomplete(input);
        
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            $('#welcome_modal_lat').val(place.geometry.location.lat());
            $('#welcome_modal_lng').val(place.geometry.location.lng());
            
            // Extract a shorter address name if possible
            let addressName = place.name || place.formatted_address;
            $('#welcome_modal_address_name').val(addressName);
        });
    }

    function saveLocationAndContinue(address, lat, lng, address_name) {
        // Core cookies
        setCookie('address_name', address_name, 365);
        setCookie('address_lat', lat, 365);
        setCookie('address_lng', lng, 365);
        
        // Granular compatibility cookies
        setCookie('address_name1', address, 365);
        
        // Extract city/country from address if possible
        const addressParts = address.split(',');
        if (addressParts.length >= 2) {
            const city = addressParts[addressParts.length - 2].trim();
            const country = addressParts[addressParts.length - 1].trim();
            setCookie('address_city', city, 365);
            setCookie('address_country', country, 365);
        }

        // Save to Recent Locations (consistent with location-selector.js if it exists)
        saveToRecentLocationsLocal({
            name: address_name,
            address: address,
            lat: lat,
            lng: lng,
            timestamp: Date.now()
        });
        
        // Update header display if it exists
        const headerDisplay = document.getElementById('headerLocationDisplay');
        if (headerDisplay) {
            headerDisplay.innerText = address_name;
        }

        // Hide modals
        $('#welcomeLocationModal').modal('hide');
        $('#headerLocationModal').modal('hide');
        
        // Reload to update content based on location
        location.reload();
    }

    function saveToRecentLocationsLocal(locationData) {
        try {
            let recent = localStorage.getItem('recent_locations');
            recent = recent ? JSON.parse(recent) : [];
            
            // Remove duplicates
            recent = recent.filter(loc => loc.address !== locationData.address);
            
            // Add to front
            recent.unshift(locationData);
            
            // Keep top 5
            recent = recent.slice(0, 5);
            
            localStorage.setItem('recent_locations', JSON.stringify(recent));
        } catch (e) {
            console.error('Error saving recent location', e);
        }
    }

    function useCurrentLocation() {
        if (navigator.geolocation) {
            const btn = document.querySelector('.btn-set-location');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<span>Locating...</span> <div class="spinner-border spinner-border-sm" role="status"></div>';
            btn.disabled = true;

            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                const geocoder = new google.maps.Geocoder();
                const latlng = { lat: lat, lng: lng };
                
                geocoder.geocode({ location: latlng }, function(results, status) {
                    if (status === "OK") {
                        if (results[0]) {
                            const address = results[0].formatted_address;
                            let addressName = "";
                            
                            // Try to get a cleaner city/area name
                            for (let component of results[0].address_components) {
                                if (component.types.includes("sublocality") || component.types.includes("locality")) {
                                    addressName = component.long_name;
                                    break;
                                }
                            }
                            if (!addressName) addressName = address.split(',')[0];

                            saveLocationAndContinue(address, lat, lng, addressName);
                        }
                    } else {
                        alert("Could not determine address: " + status);
                        btn.innerHTML = originalContent;
                        btn.disabled = false;
                    }
                });
            }, function(error) {
                alert("Error getting location: " + error.message);
                btn.innerHTML = originalContent;
                btn.disabled = false;
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    // Helper functions for cookies
    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
</script>
