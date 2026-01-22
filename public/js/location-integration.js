/**
 * Location Integration
 * Integrates LocationService with checkout, address management, and order tracking
 */

(function(window) {
    'use strict';

    const LocationIntegration = {
        /**
         * Initialize location integration for checkout
         */
        initCheckout: function() {
            // Replace getCurrentLocationAddress1 function
            window.getCurrentLocationAddress1 = async function() {
                if (!window.LocationService) {
                    console.error('[LocationIntegration] LocationService not available');
                    return;
                }

                // Show loading
                if (window.LocationUI) {
                    LocationUI.setButtonLoading('button[onclick="getCurrentLocationAddress1()"]', true, 'Getting location...');
                }

                try {
                    const result = await LocationService.getCurrentLocation({
                        enableHighAccuracy: true,
                        timeout: 15000,
                        retries: 2,
                        fallbackToNetwork: true,
                        fallbackToLastKnown: true
                    });

                    if (result.success) {
                        await handleCheckoutLocationSuccess(result);
                    } else {
                        handleCheckoutLocationError(result);
                    }
                } catch (error) {
                    console.error('[LocationIntegration] Checkout location error:', error);
                    handleCheckoutLocationError({
                        success: false,
                        error: 'UNEXPECTED_ERROR',
                        message: 'An error occurred while getting your location.'
                    });
                } finally {
                    if (window.LocationUI) {
                        LocationUI.setButtonLoading('button[onclick="getCurrentLocationAddress1()"]', false);
                    }
                }
            };

            // Handle successful location for checkout
            async function handleCheckoutLocationSuccess(result) {
                try {
                    // Reverse geocode to get address
                    const addressData = await reverseGeocodeForCheckout(result.latitude, result.longitude);
                    
                    // Update form fields
                    $('#address_line1').val(addressData.address_line1 || addressData.formatted_address || '');
                    $('#address_line2').val(addressData.address_line2 || '');
                    $('#address_city').val(addressData.city || '');
                    $('#address_country').val(addressData.country || '');
                    $('#address_zipcode').val(addressData.zip || '');
                    $('#address_lat').val(result.latitude);
                    $('#address_lng').val(result.longitude);

                    // Sync location
                    if (window.LocationSync) {
                        LocationSync.syncToAllFeatures({
                            latitude: result.latitude,
                            longitude: result.longitude,
                            address_line1: addressData.address_line1 || addressData.formatted_address || '',
                            address_line2: addressData.address_line2 || '',
                            address_city: addressData.city || '',
                            address_country: addressData.country || '',
                            address_zip: addressData.zip || '',
                            source: result.source
                        });
                    }

                    // Show success message
                    if (window.LocationUI) {
                        LocationUI.showSuccess(result, '#checkoutLocationSuccess');
                    }

                    // Trigger change to update delivery charges
                    $('#address_lat, #address_lng').trigger('change');
                } catch (error) {
                    console.error('[LocationIntegration] Reverse geocode error:', error);
                    // Still update coordinates
                    $('#address_lat').val(result.latitude);
                    $('#address_lng').val(result.longitude);
                    $('#address_lat, #address_lng').trigger('change');
                }
            }

            // Handle location error for checkout
            function handleCheckoutLocationError(errorResult) {
                if (window.LocationErrorHandler) {
                    LocationErrorHandler.showError(errorResult, {
                        container: '#checkoutLocationError',
                        onRetry: () => {
                            window.getCurrentLocationAddress1();
                        },
                        onManual: () => {
                            // Focus on address input
                            $('#address_line1').focus();
                        },
                        onMap: () => {
                            if (window.LocationUI) {
                                LocationUI.showMapSelector({
                                    onSelect: (location) => {
                                        handleCheckoutLocationSuccess({
                                            success: true,
                                            latitude: location.latitude,
                                            longitude: location.longitude,
                                            source: 'manual'
                                        });
                                    }
                                });
                            }
                        }
                    });
                } else {
                    alert(errorResult.message || 'Unable to get your location. Please enter it manually.');
                }
            }

            // Reverse geocode for checkout
            async function reverseGeocodeForCheckout(lat, lng) {
                // Wait for Google Maps if it should be available
                if (typeof mapType !== 'undefined' && mapType === 'google') {
                    if (window.GoogleMapsLoader) {
                        await window.GoogleMapsLoader.waitForGoogleMaps();
                    } else {
                        // Fallback wait
                        let waited = 0;
                        while (waited < 10000 && !(typeof google !== 'undefined' && google.maps && google.maps.Geocoder)) {
                            await new Promise(resolve => setTimeout(resolve, 100));
                            waited += 100;
                        }
                    }
                }
                
                if (typeof mapType !== 'undefined' && mapType === 'google' && window.GoogleMapsLoader && window.GoogleMapsLoader.isAvailable()) {
                    return new Promise((resolve, reject) => {
                        const geocoder = new google.maps.Geocoder();
                        const latlng = { lat: lat, lng: lng };
                        
                        geocoder.geocode({ location: latlng }, (results, status) => {
                            if (status === 'OK' && results[0]) {
                                const result = results[0];
                                const addressData = {
                                    formatted_address: result.formatted_address,
                                    address_line1: result.formatted_address
                                };
                                
                                result.address_components.forEach(component => {
                                    if (component.types.includes('street_number') || component.types.includes('route')) {
                                        addressData.address_line1 = (addressData.address_line1 || '') + component.long_name + ' ';
                                    } else if (component.types.includes('locality')) {
                                        addressData.city = component.long_name;
                                    } else if (component.types.includes('administrative_area_level_1')) {
                                        addressData.state = component.long_name;
                                    } else if (component.types.includes('country')) {
                                        addressData.country = component.long_name;
                                    } else if (component.types.includes('postal_code')) {
                                        addressData.zip = component.long_name;
                                    }
                                });
                                
                                resolve(addressData);
                            } else {
                                reject(new Error('Geocoding failed'));
                            }
                        });
                    });
                } else {
                    // Use OpenStreetMap Nominatim
                    return fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.address) {
                                return {
                                    formatted_address: data.display_name,
                                    address_line1: data.display_name,
                                    city: data.address.city || data.address.town || data.address.village || '',
                                    state: data.address.state || '',
                                    country: data.address.country || '',
                                    zip: data.address.postcode || ''
                                };
                            }
                            throw new Error('No address data');
                        });
                }
            }
        },

        /**
         * Initialize location integration for address management
         */
        initAddressManagement: function() {
            // Replace getCurrentDeliveryLocation function
            window.getCurrentDeliveryLocation = async function() {
                if (!window.LocationService) {
                    console.error('[LocationIntegration] LocationService not available');
                    return;
                }

                try {
                    const result = await LocationService.getCurrentLocation({
                        enableHighAccuracy: true,
                        timeout: 15000,
                        retries: 2,
                        fallbackToNetwork: true,
                        fallbackToLastKnown: true
                    });

                    if (result.success) {
                        await handleAddressLocationSuccess(result);
                    } else {
                        handleAddressLocationError(result);
                    }
                } catch (error) {
                    console.error('[LocationIntegration] Address location error:', error);
                    handleAddressLocationError({
                        success: false,
                        error: 'UNEXPECTED_ERROR',
                        message: 'An error occurred while getting your location.'
                    });
                }
            };

            // Handle successful location for address management
            async function handleAddressLocationSuccess(result) {
                try {
                    // Reverse geocode
                    const addressData = await reverseGeocodeForAddress(result.latitude, result.longitude);
                    
                    // Update form fields
                    $('#locality').val(addressData.formatted_address || '');
                    $('#locality').attr('lat', result.latitude);
                    $('#locality').attr('lng', result.longitude);
                    $('#address_city').val(addressData.city || '');
                    $('#address_country').val(addressData.country || '');
                    $('#address_zipcode').val(addressData.zip || '');

                    // Sync location
                    if (window.LocationSync) {
                        LocationSync.syncToAllFeatures({
                            latitude: result.latitude,
                            longitude: result.longitude,
                            address_name: addressData.formatted_address || '',
                            address_city: addressData.city || '',
                            address_country: addressData.country || '',
                            address_zip: addressData.zip || '',
                            source: result.source
                        });
                    }
                } catch (error) {
                    console.error('[LocationIntegration] Reverse geocode error:', error);
                }
            }

            // Handle location error for address management
            function handleAddressLocationError(errorResult) {
                if (window.LocationErrorHandler) {
                    LocationErrorHandler.showError(errorResult, {
                        container: '#addressLocationError',
                        onRetry: () => {
                            window.getCurrentDeliveryLocation();
                        },
                        onManual: () => {
                            $('#locality').focus();
                        }
                    });
                }
            }

            // Reverse geocode for address management
            async function reverseGeocodeForAddress(lat, lng) {
                // Wait for Google Maps if it should be available
                if (typeof mapType !== 'undefined' && mapType === 'google') {
                    if (window.GoogleMapsLoader) {
                        await window.GoogleMapsLoader.waitForGoogleMaps();
                    } else {
                        // Fallback wait
                        let waited = 0;
                        while (waited < 10000 && !(typeof google !== 'undefined' && google.maps && google.maps.Geocoder)) {
                            await new Promise(resolve => setTimeout(resolve, 100));
                            waited += 100;
                        }
                    }
                }
                
                if (typeof mapType !== 'undefined' && mapType === 'google' && window.GoogleMapsLoader && window.GoogleMapsLoader.isAvailable()) {
                    return new Promise((resolve, reject) => {
                        const geocoder = new google.maps.Geocoder();
                        const latlng = { lat: lat, lng: lng };
                        
                        geocoder.geocode({ location: latlng }, (results, status) => {
                            if (status === 'OK' && results[0]) {
                                const result = results[0];
                                const addressData = {
                                    formatted_address: result.formatted_address
                                };
                                
                                result.address_components.forEach(component => {
                                    if (component.types.includes('locality')) {
                                        addressData.city = component.long_name;
                                    } else if (component.types.includes('country')) {
                                        addressData.country = component.long_name;
                                    } else if (component.types.includes('postal_code')) {
                                        addressData.zip = component.long_name;
                                    }
                                });
                                
                                resolve(addressData);
                            } else {
                                reject(new Error('Geocoding failed'));
                            }
                        });
                    });
                } else {
                    return fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.address) {
                                return {
                                    formatted_address: data.display_name,
                                    city: data.address.city || data.address.town || data.address.village || '',
                                    country: data.address.country || '',
                                    zip: data.address.postcode || ''
                                };
                            }
                            throw new Error('No address data');
                        });
                }
            }
        },

        /**
         * Initialize order tracking location updates
         */
        initOrderTracking: function() {
            // This would be called when order tracking page loads
            if (window.OrderTracker) {
                // Watch position for real-time updates
                const watchId = LocationService.watchPosition((result) => {
                    if (result.success && window.OrderTracker.updateLocation) {
                        window.OrderTracker.updateLocation({
                            latitude: result.latitude,
                            longitude: result.longitude,
                            accuracy: result.accuracy,
                            timestamp: result.timestamp
                        });
                    }
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000
                });

                // Store watch ID for cleanup
                window.OrderTracker.watchId = watchId;
            }
        },

        /**
         * Initialize all integrations
         */
        init: function() {
            // Wait for LocationService to be available
            if (typeof window.LocationService === 'undefined') {
                console.warn('[LocationIntegration] LocationService not loaded yet');
                return;
            }

            // Initialize based on current page
            if ($('#checkout-form').length || $('#address_line1').length) {
                this.initCheckout();
            }

            if ($('#delivery-address-form').length || $('#locality').length) {
                this.initAddressManagement();
            }

            if ($('#order-tracking').length) {
                this.initOrderTracking();
            }
        }
    };

    // Initialize on DOM ready
    $(document).ready(function() {
        // Wait a bit for all scripts to load
        setTimeout(() => {
            LocationIntegration.init();
        }, 500);
    });

    // Export to window
    window.LocationIntegration = LocationIntegration;

})(window);
