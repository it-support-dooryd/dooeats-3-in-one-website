/**
 * Google Maps Loader Utility
 * Ensures Google Maps API is loaded before using it
 */

(function(window) {
    'use strict';

    const GoogleMapsLoader = {
        isLoaded: false,
        isLoading: false,
        loadPromise: null,
        listeners: [],

        /**
         * Wait for Google Maps to be loaded
         * @param {number} maxWait - Maximum wait time in milliseconds
         * @returns {Promise<boolean>}
         */
        waitForGoogleMaps: function(maxWait = 15000) {
            // If already loaded, return immediately
            if (this.isLoaded && typeof google !== 'undefined' && google.maps && google.maps.Geocoder) {
                return Promise.resolve(true);
            }

            // If already waiting, return the existing promise
            if (this.loadPromise) {
                return this.loadPromise;
            }

            // Create new promise
            this.loadPromise = new Promise((resolve) => {
                const startTime = Date.now();
                const checkInterval = setInterval(() => {
                    if (typeof google !== 'undefined' && 
                        google.maps && 
                        google.maps.Geocoder &&
                        (typeof mapType === 'undefined' || mapType === 'google')) {
                        this.isLoaded = true;
                        clearInterval(checkInterval);
                        this.notifyListeners();
                        resolve(true);
                    } else if (Date.now() - startTime > maxWait) {
                        clearInterval(checkInterval);
                        this.loadPromise = null;
                        resolve(false);
                    }
                }, 100);
            });

            return this.loadPromise;
        },

        /**
         * Check if Google Maps is available
         * @returns {boolean}
         */
        isAvailable: function() {
            return typeof google !== 'undefined' && 
                   google.maps && 
                   google.maps.Geocoder &&
                   (typeof mapType === 'undefined' || mapType === 'google');
        },

        /**
         * Add listener for when Google Maps is loaded
         */
        onLoad: function(callback) {
            if (this.isLoaded) {
                callback();
            } else {
                this.listeners.push(callback);
            }
        },

        /**
         * Notify all listeners
         */
        notifyListeners: function() {
            this.listeners.forEach(callback => {
                try {
                    callback();
                } catch (e) {
                    console.error('[GoogleMapsLoader] Listener error:', e);
                }
            });
            this.listeners = [];
        },

        /**
         * Initialize - start checking for Google Maps
         */
        init: function() {
            // Check periodically if Google Maps is loaded
            const checkInterval = setInterval(() => {
                if (this.isAvailable()) {
                    this.isLoaded = true;
                    clearInterval(checkInterval);
                    this.notifyListeners();
                }
            }, 500);

            // Stop checking after 30 seconds
            setTimeout(() => {
                clearInterval(checkInterval);
            }, 30000);
        }
    };

    // Initialize on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            GoogleMapsLoader.init();
        });
    } else {
        GoogleMapsLoader.init();
    }

    // Export to window
    window.GoogleMapsLoader = GoogleMapsLoader;

})(window);
