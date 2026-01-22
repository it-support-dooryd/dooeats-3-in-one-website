/**
 * Comprehensive Location Service
 * Handles GPS, network fallback, retry mechanisms, error handling, and logging
 */

(function(window) {
    'use strict';

    // Configuration
    const CONFIG = {
        MAX_RETRIES: 3,
        RETRY_DELAY: 2000, // 2 seconds
        GPS_TIMEOUT: 15000, // 15 seconds
        GPS_MAX_AGE: 60000, // 1 minute
        HIGH_ACCURACY: true,
        NETWORK_FALLBACK_ENABLED: true,
        LOGGING_ENABLED: true
    };

    // Location Service State
    const LocationService = {
        currentLocation: null,
        lastKnownLocation: null,
        retryCount: 0,
        isRequesting: false,
        listeners: [],
        errorLog: []
    };

    /**
     * Main function to get current location with all fallbacks
     * @param {Object} options - Configuration options
     * @returns {Promise<LocationResult>}
     */
    LocationService.getCurrentLocation = async function(options = {}) {
        const opts = {
            enableHighAccuracy: options.enableHighAccuracy !== false ? CONFIG.HIGH_ACCURACY : false,
            timeout: options.timeout || CONFIG.GPS_TIMEOUT,
            maximumAge: options.maximumAge || CONFIG.GPS_MAX_AGE,
            retries: options.retries !== undefined ? options.retries : CONFIG.MAX_RETRIES,
            useCache: options.useCache !== false,
            fallbackToNetwork: options.fallbackToNetwork !== false && CONFIG.NETWORK_FALLBACK_ENABLED,
            fallbackToLastKnown: options.fallbackToLastKnown !== false,
            showUI: options.showUI !== false
        };

        // Prevent concurrent requests
        if (LocationService.isRequesting) {
            return new Promise((resolve) => {
                LocationService.listeners.push(resolve);
            });
        }

        LocationService.isRequesting = true;

        try {
            // Check if geolocation is supported
            if (!navigator.geolocation) {
                logError('GEOLOCATION_NOT_SUPPORTED', 'Browser does not support geolocation');
                return handleGeolocationUnsupported(opts);
            }

            // Try to use cached location if available and fresh
            if (opts.useCache && LocationService.currentLocation) {
                const age = Date.now() - LocationService.currentLocation.timestamp;
                if (age < opts.maximumAge) {
                    logSuccess('CACHED', LocationService.currentLocation);
                    LocationService.isRequesting = false;
                    notifyListeners(LocationService.currentLocation);
                    return LocationService.currentLocation;
                }
            }

            // Try GPS location with retries
            const gpsResult = await getGPSLocationWithRetry(opts);
            
            if (gpsResult.success) {
                LocationService.currentLocation = gpsResult;
                LocationService.lastKnownLocation = gpsResult;
                LocationService.isRequesting = false;
                notifyListeners(gpsResult);
                return gpsResult;
            }

            // GPS failed, try network fallback
            if (opts.fallbackToNetwork) {
                logInfo('FALLBACK_NETWORK', 'GPS failed, trying network-based location');
                const networkResult = await getNetworkLocation(opts);
                
                if (networkResult.success) {
                    LocationService.currentLocation = networkResult;
                    LocationService.lastKnownLocation = networkResult;
                    LocationService.isRequesting = false;
                    notifyListeners(networkResult);
                    return networkResult;
                }
            }

            // All methods failed, try last known location
            if (opts.fallbackToLastKnown && LocationService.lastKnownLocation) {
                logInfo('FALLBACK_LAST_KNOWN', 'Using last known location');
                LocationService.isRequesting = false;
                notifyListeners({
                    ...LocationService.lastKnownLocation,
                    source: 'last_known',
                    accuracy: 'low'
                });
                return {
                    ...LocationService.lastKnownLocation,
                    source: 'last_known',
                    accuracy: 'low'
                };
            }

            // All fallbacks failed
            const errorResult = {
                success: false,
                error: gpsResult.error || 'LOCATION_UNAVAILABLE',
                message: getErrorMessage(gpsResult.error || 'LOCATION_UNAVAILABLE'),
                source: 'none'
            };

            LocationService.isRequesting = false;
            notifyListeners(errorResult);
            return errorResult;

        } catch (error) {
            logError('UNEXPECTED_ERROR', error.message, error);
            LocationService.isRequesting = false;
            const errorResult = {
                success: false,
                error: 'UNEXPECTED_ERROR',
                message: 'An unexpected error occurred while getting your location.',
                source: 'none'
            };
            notifyListeners(errorResult);
            return errorResult;
        }
    };

    /**
     * Get GPS location with retry mechanism
     */
    async function getGPSLocationWithRetry(options) {
        LocationService.retryCount = 0;
        
        while (LocationService.retryCount <= options.retries) {
            try {
                const result = await getGPSLocation(options);
                
                if (result.success) {
                    LocationService.retryCount = 0;
                    return result;
                }

                // If permission denied, don't retry
                if (result.error === 'PERMISSION_DENIED') {
                    return result;
                }

                LocationService.retryCount++;
                
                if (LocationService.retryCount <= options.retries) {
                    logInfo('RETRY', `Retrying GPS location (attempt ${LocationService.retryCount}/${options.retries})`);
                    await delay(CONFIG.RETRY_DELAY * LocationService.retryCount);
                }
            } catch (error) {
                LocationService.retryCount++;
                if (LocationService.retryCount <= options.retries) {
                    await delay(CONFIG.RETRY_DELAY * LocationService.retryCount);
                } else {
                    return {
                        success: false,
                        error: 'GPS_ERROR',
                        message: 'Failed to get GPS location after retries',
                        source: 'gps'
                    };
                }
            }
        }

        return {
            success: false,
            error: 'GPS_TIMEOUT',
            message: 'GPS location request timed out after multiple retries',
            source: 'gps'
        };
    }

    /**
     * Get GPS location (single attempt)
     */
    function getGPSLocation(options) {
        return new Promise((resolve) => {
            const geolocationOptions = {
                enableHighAccuracy: options.enableHighAccuracy,
                timeout: options.timeout,
                maximumAge: options.maximumAge
            };

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const result = {
                        success: true,
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy,
                        altitude: position.coords.altitude,
                        altitudeAccuracy: position.coords.altitudeAccuracy,
                        heading: position.coords.heading,
                        speed: position.coords.speed,
                        timestamp: position.timestamp || Date.now(),
                        source: 'gps',
                        accuracyLevel: getAccuracyLevel(position.coords.accuracy)
                    };
                    logSuccess('GPS', result);
                    resolve(result);
                },
                (error) => {
                    const errorCode = getErrorCode(error);
                    const result = {
                        success: false,
                        error: errorCode,
                        message: getErrorMessage(errorCode),
                        source: 'gps',
                        errorDetails: {
                            code: error.code,
                            message: error.message
                        }
                    };
                    logError(errorCode, error.message, error);
                    resolve(result);
                },
                geolocationOptions
            );
        });
    }

    /**
     * Get network-based location (IP geolocation fallback)
     */
    async function getNetworkLocation(options) {
        try {
            // Try multiple IP geolocation services
            const services = [
                'https://ipapi.co/json/',
                'https://ip-api.com/json/',
                'https://geolocation-db.com/json/'
            ];

            for (const serviceUrl of services) {
                try {
                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 5000);
                    
                    const response = await fetch(serviceUrl, {
                        signal: controller.signal
                    });
                    
                    clearTimeout(timeoutId);
                    
                    if (!response.ok) continue;
                    
                    const data = await response.json();
                    
                    // Parse response based on service
                    let lat, lng;
                    if (data.latitude && data.longitude) {
                        lat = parseFloat(data.latitude);
                        lng = parseFloat(data.longitude);
                    } else if (data.lat && data.lng) {
                        lat = parseFloat(data.lat);
                        lng = parseFloat(data.lng);
                    } else {
                        continue;
                    }

                    if (isNaN(lat) || isNaN(lng)) continue;

                    const result = {
                        success: true,
                        latitude: lat,
                        longitude: lng,
                        accuracy: 10000, // Network location is less accurate
                        timestamp: Date.now(),
                        source: 'network',
                        accuracyLevel: 'low',
                        city: data.city || data.city_name,
                        region: data.region || data.region_name || data.state,
                        country: data.country || data.country_name,
                        countryCode: data.country_code || data.countryCode
                    };

                    logSuccess('NETWORK', result);
                    return result;
                } catch (err) {
                    logError('NETWORK_SERVICE_ERROR', `Failed to fetch from ${serviceUrl}`, err);
                    continue;
                }
            }

            return {
                success: false,
                error: 'NETWORK_LOCATION_UNAVAILABLE',
                message: 'Network-based location is not available',
                source: 'network'
            };
        } catch (error) {
            return {
                success: false,
                error: 'NETWORK_ERROR',
                message: 'Failed to get network-based location',
                source: 'network'
            };
        }
    }

    /**
     * Handle geolocation unsupported
     */
    function handleGeolocationUnsupported(options) {
        if (options.fallbackToNetwork) {
            return getNetworkLocation(options);
        }
        
        return {
            success: false,
            error: 'GEOLOCATION_NOT_SUPPORTED',
            message: 'Your browser does not support location services. Please enter your location manually.',
            source: 'none'
        };
    }

    /**
     * Get error code from geolocation error
     */
    function getErrorCode(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                return 'PERMISSION_DENIED';
            case error.POSITION_UNAVAILABLE:
                return 'POSITION_UNAVAILABLE';
            case error.TIMEOUT:
                return 'TIMEOUT';
            default:
                return 'UNKNOWN_ERROR';
        }
    }

    /**
     * Get user-friendly error message
     */
    function getErrorMessage(errorCode) {
        const messages = {
            'PERMISSION_DENIED': 'We need access to your location to provide delivery tracking. Please allow location access in settings.',
            'POSITION_UNAVAILABLE': 'Your location could not be determined accurately. Please move to an open area or select your location manually.',
            'TIMEOUT': 'Location request timed out. Please try again or select your location manually.',
            'GPS_TIMEOUT': 'GPS location request timed out. Please try again or select your location manually.',
            'GEOLOCATION_NOT_SUPPORTED': 'Your browser does not support location services. Please enter your location manually.',
            'NETWORK_LOCATION_UNAVAILABLE': 'We couldn\'t get your location due to network issues. Try again or select your location manually.',
            'LOCATION_UNAVAILABLE': 'We couldn\'t get your location. Please try again or select your location manually.',
            'UNKNOWN_ERROR': 'An error occurred while getting your location. Please try again.',
            'UNEXPECTED_ERROR': 'An unexpected error occurred. Please try again or select your location manually.'
        };

        return messages[errorCode] || messages['UNKNOWN_ERROR'];
    }

    /**
     * Get accuracy level based on accuracy value
     */
    function getAccuracyLevel(accuracy) {
        if (accuracy <= 20) return 'high';
        if (accuracy <= 100) return 'medium';
        return 'low';
    }

    /**
     * Delay helper
     */
    function delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Log success
     */
    function logSuccess(method, result) {
        if (!CONFIG.LOGGING_ENABLED) return;
        
        const logEntry = {
            type: 'success',
            method: method,
            timestamp: Date.now(),
            result: {
                source: result.source,
                accuracy: result.accuracy,
                accuracyLevel: result.accuracyLevel
            }
        };
        
        LocationService.errorLog.push(logEntry);
        
        // Keep only last 100 entries
        if (LocationService.errorLog.length > 100) {
            LocationService.errorLog.shift();
        }
        
        if (window.console && console.log) {
            console.log('[LocationService] Success:', logEntry);
        }
    }

    /**
     * Log error
     */
    function logError(errorCode, message, error = null) {
        if (!CONFIG.LOGGING_ENABLED) return;
        
        const logEntry = {
            type: 'error',
            errorCode: errorCode,
            message: message,
            timestamp: Date.now(),
            error: error ? {
                code: error.code,
                message: error.message
            } : null
        };
        
        LocationService.errorLog.push(logEntry);
        
        // Keep only last 100 entries
        if (LocationService.errorLog.length > 100) {
            LocationService.errorLog.shift();
        }
        
        if (window.console && console.error) {
            console.error('[LocationService] Error:', logEntry);
        }

        // Send to analytics if available
        if (window.analytics && typeof window.analytics.track === 'function') {
            window.analytics.track('Location Error', {
                errorCode: errorCode,
                message: message,
                timestamp: Date.now()
            });
        }
    }

    /**
     * Log info
     */
    function logInfo(type, message) {
        if (!CONFIG.LOGGING_ENABLED) return;
        
        const logEntry = {
            type: 'info',
            infoType: type,
            message: message,
            timestamp: Date.now()
        };
        
        LocationService.errorLog.push(logEntry);
        
        if (window.console && console.info) {
            console.info('[LocationService] Info:', logEntry);
        }
    }

    /**
     * Notify listeners
     */
    function notifyListeners(result) {
        LocationService.listeners.forEach(listener => {
            try {
                listener(result);
            } catch (error) {
                console.error('[LocationService] Listener error:', error);
            }
        });
        LocationService.listeners = [];
    }

    /**
     * Watch position (continuous updates)
     */
    LocationService.watchPosition = function(callback, options = {}) {
        if (!navigator.geolocation) {
            callback({
                success: false,
                error: 'GEOLOCATION_NOT_SUPPORTED',
                message: getErrorMessage('GEOLOCATION_NOT_SUPPORTED')
            });
            return null;
        }

        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const result = {
                    success: true,
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    timestamp: position.timestamp || Date.now(),
                    source: 'gps',
                    accuracyLevel: getAccuracyLevel(position.coords.accuracy)
                };
                
                LocationService.currentLocation = result;
                LocationService.lastKnownLocation = result;
                callback(result);
            },
            (error) => {
                const errorCode = getErrorCode(error);
                callback({
                    success: false,
                    error: errorCode,
                    message: getErrorMessage(errorCode)
                });
            },
            {
                enableHighAccuracy: options.enableHighAccuracy !== false ? CONFIG.HIGH_ACCURACY : false,
                timeout: options.timeout || CONFIG.GPS_TIMEOUT,
                maximumAge: options.maximumAge || CONFIG.GPS_MAX_AGE
            }
        );

        return watchId;
    };

    /**
     * Clear watch
     */
    LocationService.clearWatch = function(watchId) {
        if (watchId && navigator.geolocation) {
            navigator.geolocation.clearWatch(watchId);
        }
    };

    /**
     * Get error log
     */
    LocationService.getErrorLog = function() {
        return LocationService.errorLog;
    };

    /**
     * Clear error log
     */
    LocationService.clearErrorLog = function() {
        LocationService.errorLog = [];
    };

    /**
     * Get last known location
     */
    LocationService.getLastKnownLocation = function() {
        return LocationService.lastKnownLocation;
    };

    /**
     * Set location manually
     */
    LocationService.setLocation = function(latitude, longitude, addressData = {}) {
        const result = {
            success: true,
            latitude: parseFloat(latitude),
            longitude: parseFloat(longitude),
            accuracy: 0, // Manual entry is considered accurate
            timestamp: Date.now(),
            source: 'manual',
            accuracyLevel: 'high',
            ...addressData
        };

        LocationService.currentLocation = result;
        LocationService.lastKnownLocation = result;
        
        logSuccess('MANUAL', result);
        return result;
    };

    /**
     * Check if location permission is granted
     */
    LocationService.checkPermission = async function() {
        if (!navigator.permissions) {
            return 'unknown';
        }

        try {
            const result = await navigator.permissions.query({ name: 'geolocation' });
            return result.state; // 'granted', 'denied', or 'prompt'
        } catch (error) {
            return 'unknown';
        }
    };

    // Export to window
    window.LocationService = LocationService;

})(window);
