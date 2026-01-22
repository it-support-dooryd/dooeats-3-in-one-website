/**
 * Location Error Handler
 * Provides user-friendly error messages and UI actions
 */

(function(window) {
    'use strict';

    const LocationErrorHandler = {
        /**
         * Show error with appropriate UI actions
         * @param {Object} errorResult - Error result from LocationService
         * @param {Object} options - Display options
         */
        showError: function(errorResult, options = {}) {
            const opts = {
                container: options.container || '#locationErrorContainer',
                showActions: options.showActions !== false,
                onRetry: options.onRetry || null,
                onManual: options.onManual || null,
                onSettings: options.onSettings || null,
                onLastKnown: options.onLastKnown || null
            };

            const $container = $(opts.container);
            if (!$container.length) {
                console.warn('[LocationErrorHandler] Container not found:', opts.container);
                return;
            }

            const errorConfig = this.getErrorConfig(errorResult.error);
            const html = this.buildErrorHTML(errorResult, errorConfig, opts);
            
            $container.html(html).show();

            // Bind action handlers
            if (opts.onRetry) {
                $container.find('.location-error-retry').on('click', function(e) {
                    e.preventDefault();
                    opts.onRetry();
                });
            }

            if (opts.onManual) {
                $container.find('.location-error-manual').on('click', function(e) {
                    e.preventDefault();
                    opts.onManual();
                });
            }

            if (opts.onSettings) {
                $container.find('.location-error-settings').on('click', function(e) {
                    e.preventDefault();
                    opts.onSettings();
                });
            }

            if (opts.onLastKnown) {
                $container.find('.location-error-last-known').on('click', function(e) {
                    e.preventDefault();
                    opts.onLastKnown();
                });
            }
        },

        /**
         * Get error configuration
         */
        getErrorConfig: function(errorCode) {
            const configs = {
                'PERMISSION_DENIED': {
                    title: 'Location Permission Required',
                    message: 'We need access to your location to provide delivery tracking. Please allow location access in settings.',
                    icon: 'feather-lock',
                    showRetry: false,
                    showManual: true,
                    showSettings: true,
                    showLastKnown: true
                },
                'POSITION_UNAVAILABLE': {
                    title: 'Location Unavailable',
                    message: 'Your location could not be determined accurately. Please move to an open area or select your location manually.',
                    icon: 'feather-alert-circle',
                    showRetry: true,
                    showManual: true,
                    showSettings: false,
                    showLastKnown: true
                },
                'TIMEOUT': {
                    title: 'Location Request Timed Out',
                    message: 'Location request timed out. Please try again or select your location manually.',
                    icon: 'feather-clock',
                    showRetry: true,
                    showManual: true,
                    showSettings: false,
                    showLastKnown: true
                },
                'GPS_TIMEOUT': {
                    title: 'GPS Timeout',
                    message: 'GPS location request timed out after multiple retries. Please try again or select your location manually.',
                    icon: 'feather-clock',
                    showRetry: true,
                    showManual: true,
                    showSettings: false,
                    showLastKnown: true
                },
                'GEOLOCATION_NOT_SUPPORTED': {
                    title: 'Location Not Supported',
                    message: 'Your browser does not support location services. Please enter your location manually.',
                    icon: 'feather-x-circle',
                    showRetry: false,
                    showManual: true,
                    showSettings: false,
                    showLastKnown: true
                },
                'NETWORK_LOCATION_UNAVAILABLE': {
                    title: 'Network Location Unavailable',
                    message: 'We couldn\'t get your location due to network issues. Try again or select your location manually.',
                    icon: 'feather-wifi-off',
                    showRetry: true,
                    showManual: true,
                    showSettings: false,
                    showLastKnown: true
                },
                'LOCATION_UNAVAILABLE': {
                    title: 'Location Unavailable',
                    message: 'We couldn\'t get your location. Please try again or select your location manually.',
                    icon: 'feather-map-pin',
                    showRetry: true,
                    showManual: true,
                    showSettings: false,
                    showLastKnown: true
                },
                'UNKNOWN_ERROR': {
                    title: 'Location Error',
                    message: 'An error occurred while getting your location. Please try again.',
                    icon: 'feather-alert-triangle',
                    showRetry: true,
                    showManual: true,
                    showSettings: false,
                    showLastKnown: true
                }
            };

            return configs[errorCode] || configs['UNKNOWN_ERROR'];
        },

        /**
         * Build error HTML
         */
        buildErrorHTML: function(errorResult, errorConfig, options) {
            let actionsHTML = '';

            if (options.showActions) {
                const actions = [];
                
                if (errorConfig.showRetry && options.onRetry) {
                    actions.push(`
                        <button class="btn btn-primary btn-sm location-error-retry">
                            <i class="feather-refresh-cw mr-1"></i>
                            Retry
                        </button>
                    `);
                }

                if (errorConfig.showManual && options.onManual) {
                    actions.push(`
                        <button class="btn btn-outline-primary btn-sm location-error-manual">
                            <i class="feather-map mr-1"></i>
                            Enter Manually
                        </button>
                    `);
                }

                if (errorConfig.showSettings && options.onSettings) {
                    actions.push(`
                        <button class="btn btn-outline-secondary btn-sm location-error-settings">
                            <i class="feather-settings mr-1"></i>
                            Open Settings
                        </button>
                    `);
                }

                if (errorConfig.showLastKnown && options.onLastKnown) {
                    actions.push(`
                        <button class="btn btn-outline-info btn-sm location-error-last-known">
                            <i class="feather-clock mr-1"></i>
                            Use Last Location
                        </button>
                    `);
                }

                if (actions.length > 0) {
                    actionsHTML = `
                        <div class="location-error-actions mt-3 d-flex flex-wrap gap-2">
                            ${actions.join('')}
                        </div>
                    `;
                }
            }

            return `
                <div class="location-error-alert alert alert-warning border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-start">
                        <div class="location-error-icon mr-3">
                            <i class="${errorConfig.icon} text-warning" style="font-size: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-2 font-weight-bold">${errorConfig.title}</h6>
                            <p class="mb-0 text-muted">${errorConfig.message}</p>
                            ${actionsHTML}
                        </div>
                    </div>
                </div>
            `;
        },

        /**
         * Hide error
         */
        hideError: function(container = '#locationErrorContainer') {
            $(container).hide().empty();
        },

        /**
         * Show permission request prompt
         */
        showPermissionPrompt: function(options = {}) {
            const opts = {
                container: options.container || '#locationPermissionPrompt',
                onAllow: options.onAllow || null,
                onDeny: options.onDeny || null,
                message: options.message || 'We need your location to track your orders in real-time and provide accurate delivery estimates.'
            };

            const $container = $(opts.container);
            if (!$container.length) {
                console.warn('[LocationErrorHandler] Permission prompt container not found:', opts.container);
                return;
            }

            const html = `
                <div class="location-permission-prompt card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="location-permission-icon mr-3">
                                <i class="feather-map-pin text-primary" style="font-size: 32px;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-2 font-weight-bold">Enable Location Access</h5>
                                <p class="card-text text-muted mb-0">${opts.message}</p>
                            </div>
                        </div>
                        <div class="location-permission-actions d-flex gap-2">
                            ${opts.onAllow ? `
                                <button class="btn btn-primary btn-block location-permission-allow">
                                    <i class="feather-check mr-2"></i>
                                    Allow Location Access
                                </button>
                            ` : ''}
                            ${opts.onDeny ? `
                                <button class="btn btn-outline-secondary btn-block location-permission-deny">
                                    <i class="feather-x mr-2"></i>
                                    Not Now
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;

            $container.html(html).show();

            if (opts.onAllow) {
                $container.find('.location-permission-allow').on('click', function(e) {
                    e.preventDefault();
                    opts.onAllow();
                });
            }

            if (opts.onDeny) {
                $container.find('.location-permission-deny').on('click', function(e) {
                    e.preventDefault();
                    opts.onDeny();
                });
            }
        },

        /**
         * Hide permission prompt
         */
        hidePermissionPrompt: function(container = '#locationPermissionPrompt') {
            $(container).hide().empty();
        }
    };

    // Export to window
    window.LocationErrorHandler = LocationErrorHandler;

})(window);
