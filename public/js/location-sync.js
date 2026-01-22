/**
 * Location Sync Utility
 * Ensures location consistency across checkout, order tracking, and address management
 */

(function(window) {
    'use strict';

    const LocationSync = {
        storageKey: 'dooeats_location_data',
        syncListeners: [],

        /**
         * Save location to all storage mechanisms
         */
        saveLocation: function(locationData) {
            if (!locationData || !locationData.latitude || !locationData.longitude) {
                console.warn('[LocationSync] Invalid location data');
                return false;
            }

            const syncData = {
                latitude: parseFloat(locationData.latitude),
                longitude: parseFloat(locationData.longitude),
                address_name: locationData.address_name || locationData.name || '',
                address_city: locationData.address_city || locationData.city || '',
                address_state: locationData.address_state || locationData.state || '',
                address_country: locationData.address_country || locationData.country || '',
                address_zip: locationData.address_zip || locationData.zip || '',
                address_line1: locationData.address_line1 || '',
                address_line2: locationData.address_line2 || '',
                timestamp: Date.now(),
                source: locationData.source || 'unknown'
            };

            // Save to cookies (for backward compatibility)
            if (typeof setCookie === 'function') {
                setCookie('address_lat', syncData.latitude, 365);
                setCookie('address_lng', syncData.longitude, 365);
                setCookie('address_name', syncData.address_name, 365);
                setCookie('address_city', syncData.address_city, 365);
                setCookie('address_state', syncData.address_state, 365);
                setCookie('address_country', syncData.address_country, 365);
                setCookie('address_zip', syncData.address_zip, 365);
            }

            // Save to localStorage
            try {
                localStorage.setItem(this.storageKey, JSON.stringify(syncData));
            } catch (e) {
                console.error('[LocationSync] Failed to save to localStorage:', e);
            }

            // Save to sessionStorage
            try {
                sessionStorage.setItem('current_location', JSON.stringify(syncData));
            } catch (e) {
                console.error('[LocationSync] Failed to save to sessionStorage:', e);
            }

            // Update hidden form fields if they exist
            $('#address_lat').val(syncData.latitude);
            $('#address_lng').val(syncData.longitude);
            $('#address_city').val(syncData.address_city);
            $('#address_country').val(syncData.address_country);
            $('#address_zipcode').val(syncData.address_zip);

            // Notify listeners
            this.notifyListeners(syncData);

            // Trigger custom event
            $(document).trigger('location:updated', [syncData]);

            return true;
        },

        /**
         * Get current location from storage
         */
        getLocation: function() {
            // Try localStorage first
            try {
                const stored = localStorage.getItem(this.storageKey);
                if (stored) {
                    const data = JSON.parse(stored);
                    // Check if data is still fresh (less than 1 hour old)
                    if (Date.now() - data.timestamp < 3600000) {
                        return data;
                    }
                }
            } catch (e) {
                console.error('[LocationSync] Failed to read from localStorage:', e);
            }

            // Fallback to cookies
            if (typeof getCookie === 'function') {
                const lat = getCookie('address_lat');
                const lng = getCookie('address_lng');
                
                if (lat && lng) {
                    return {
                        latitude: parseFloat(lat),
                        longitude: parseFloat(lng),
                        address_name: getCookie('address_name') || '',
                        address_city: getCookie('address_city') || '',
                        address_state: getCookie('address_state') || '',
                        address_country: getCookie('address_country') || '',
                        address_zip: getCookie('address_zip') || '',
                        timestamp: Date.now(),
                        source: 'cookie'
                    };
                }
            }

            return null;
        },

        /**
         * Clear location from all storage
         */
        clearLocation: function() {
            // Clear cookies
            if (typeof setCookie === 'function') {
                setCookie('address_lat', '', -1);
                setCookie('address_lng', '', -1);
                setCookie('address_name', '', -1);
                setCookie('address_city', '', -1);
                setCookie('address_state', '', -1);
                setCookie('address_country', '', -1);
                setCookie('address_zip', '', -1);
            }

            // Clear localStorage
            try {
                localStorage.removeItem(this.storageKey);
            } catch (e) {
                console.error('[LocationSync] Failed to clear localStorage:', e);
            }

            // Clear sessionStorage
            try {
                sessionStorage.removeItem('current_location');
            } catch (e) {
                console.error('[LocationSync] Failed to clear sessionStorage:', e);
            }

            // Clear form fields
            $('#address_lat').val('');
            $('#address_lng').val('');
            $('#address_city').val('');
            $('#address_country').val('');
            $('#address_zipcode').val('');

            // Notify listeners
            this.notifyListeners(null);

            // Trigger custom event
            $(document).trigger('location:cleared');
        },

        /**
         * Sync location across all features
         */
        syncToAllFeatures: function(locationData) {
            this.saveLocation(locationData);

            // Update checkout if on checkout page
            if ($('#checkout-form').length) {
                this.updateCheckoutLocation(locationData);
            }

            // Update address management if on address page
            if ($('#delivery-address-form').length) {
                this.updateAddressManagement(locationData);
            }

            // Update order tracking if active
            if (window.OrderTracker && typeof window.OrderTracker.updateLocation === 'function') {
                window.OrderTracker.updateLocation(locationData);
            }

            // Update home page restaurant list if on home
            if ($('#restaurant-list').length) {
                $(document).trigger('location:updated', [locationData]);
            }
        },

        /**
         * Update checkout form with location
         */
        updateCheckoutLocation: function(locationData) {
            $('#address_lat').val(locationData.latitude);
            $('#address_lng').val(locationData.longitude);
            $('#address_line1').val(locationData.address_line1 || locationData.address_name || '');
            $('#address_city').val(locationData.address_city || '');
            $('#address_country').val(locationData.address_country || '');
            $('#address_zipcode').val(locationData.address_zip || '');

            // Trigger change events to update delivery charges
            $('#address_lat, #address_lng').trigger('change');
        },

        /**
         * Update address management form
         */
        updateAddressManagement: function(locationData) {
            $('#locality').val(locationData.address_name || '');
            $('#locality').attr('lat', locationData.latitude);
            $('#locality').attr('lng', locationData.longitude);
            $('#address_city').val(locationData.address_city || '');
            $('#address_country').val(locationData.address_country || '');
            $('#address_zipcode').val(locationData.address_zip || '');
        },

        /**
         * Add sync listener
         */
        addListener: function(callback) {
            if (typeof callback === 'function') {
                this.syncListeners.push(callback);
            }
        },

        /**
         * Remove sync listener
         */
        removeListener: function(callback) {
            this.syncListeners = this.syncListeners.filter(listener => listener !== callback);
        },

        /**
         * Notify all listeners
         */
        notifyListeners: function(locationData) {
            this.syncListeners.forEach(listener => {
                try {
                    listener(locationData);
                } catch (e) {
                    console.error('[LocationSync] Listener error:', e);
                }
            });
        },

        /**
         * Initialize location sync
         */
        init: function() {
            // Listen for location updates from LocationService
            if (window.LocationService) {
                LocationService.addListener((result) => {
                    if (result.success) {
                        this.syncToAllFeatures({
                            latitude: result.latitude,
                            longitude: result.longitude,
                            source: result.source,
                            accuracy: result.accuracy
                        });
                    }
                });
            }

            // Listen for manual location updates
            $(document).on('location:manual', (e, locationData) => {
                this.syncToAllFeatures(locationData);
            });

            // Restore location on page load
            const savedLocation = this.getLocation();
            if (savedLocation) {
                this.syncToAllFeatures(savedLocation);
            }
        }
    };

    // Initialize on DOM ready
    $(document).ready(function() {
        LocationSync.init();
    });

    // Export to window
    window.LocationSync = LocationSync;

})(window);
