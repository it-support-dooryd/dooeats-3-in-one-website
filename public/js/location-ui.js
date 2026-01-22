/**
 * Location UI Components
 * Provides loading states, fallback screens, and interactive location selection
 */

(function(window) {
    'use strict';

    const LocationUI = {
        /**
         * Show loading state
         */
        showLoading: function(container = '#locationLoadingContainer', message = 'Getting your location...') {
            const $container = $(container);
            if (!$container.length) {
                console.warn('[LocationUI] Loading container not found:', container);
                return;
            }

            const html = `
                <div class="location-loading d-flex flex-column align-items-center justify-content-center p-4">
                    <div class="location-loading-spinner spinner-border text-primary mb-3" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="text-muted mb-0 text-center">${message}</p>
                    <small class="text-muted mt-2">This may take a few seconds...</small>
                </div>
            `;

            $container.html(html).show();
        },

        /**
         * Hide loading state
         */
        hideLoading: function(container = '#locationLoadingContainer') {
            $(container).hide().empty();
        },

        /**
         * Show fallback screen with options
         */
        showFallbackScreen: function(options = {}) {
            const opts = {
                container: options.container || '#locationFallbackContainer',
                title: options.title || 'Location Not Available',
                message: options.message || 'We couldn\'t get your current location. Please choose an option below.',
                showRetry: options.showRetry !== false,
                showManual: options.showManual !== false,
                showLastKnown: options.showLastKnown !== false,
                showMap: options.showMap !== false,
                onRetry: options.onRetry || null,
                onManual: options.onManual || null,
                onLastKnown: options.onLastKnown || null,
                onMap: options.onMap || null
            };

            const $container = $(opts.container);
            if (!$container.length) {
                console.warn('[LocationUI] Fallback container not found:', opts.container);
                return;
            }

            let actionsHTML = '<div class="location-fallback-actions d-grid gap-2 mt-4">';

            if (opts.showRetry && opts.onRetry) {
                actionsHTML += `
                    <button class="btn btn-primary btn-lg location-fallback-retry">
                        <i class="feather-refresh-cw mr-2"></i>
                        Try Again
                    </button>
                `;
            }

            if (opts.showMap && opts.onMap) {
                actionsHTML += `
                    <button class="btn btn-outline-primary btn-lg location-fallback-map">
                        <i class="feather-map mr-2"></i>
                        Select on Map
                    </button>
                `;
            }

            if (opts.showManual && opts.onManual) {
                actionsHTML += `
                    <button class="btn btn-outline-secondary btn-lg location-fallback-manual">
                        <i class="feather-edit-3 mr-2"></i>
                        Enter Address Manually
                    </button>
                `;
            }

            if (opts.showLastKnown && opts.onLastKnown) {
                actionsHTML += `
                    <button class="btn btn-outline-info btn-lg location-fallback-last-known">
                        <i class="feather-clock mr-2"></i>
                        Use Last Known Location
                    </button>
                `;
            }

            actionsHTML += '</div>';

            const html = `
                <div class="location-fallback-screen card border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <div class="location-fallback-icon mb-4">
                            <i class="feather-map-pin text-muted" style="font-size: 64px; opacity: 0.5;"></i>
                        </div>
                        <h4 class="card-title mb-3 font-weight-bold">${opts.title}</h4>
                        <p class="card-text text-muted mb-0">${opts.message}</p>
                        ${actionsHTML}
                    </div>
                </div>
            `;

            $container.html(html).show();

            // Bind handlers
            if (opts.onRetry) {
                $container.find('.location-fallback-retry').on('click', function(e) {
                    e.preventDefault();
                    opts.onRetry();
                });
            }

            if (opts.onMap) {
                $container.find('.location-fallback-map').on('click', function(e) {
                    e.preventDefault();
                    opts.onMap();
                });
            }

            if (opts.onManual) {
                $container.find('.location-fallback-manual').on('click', function(e) {
                    e.preventDefault();
                    opts.onManual();
                });
            }

            if (opts.onLastKnown) {
                $container.find('.location-fallback-last-known').on('click', function(e) {
                    e.preventDefault();
                    opts.onLastKnown();
                });
            }
        },

        /**
         * Hide fallback screen
         */
        hideFallbackScreen: function(container = '#locationFallbackContainer') {
            $(container).hide().empty();
        },

        /**
         * Show location success state
         */
        showSuccess: function(locationResult, container = '#locationSuccessContainer') {
            const $container = $(container);
            if (!$container.length) {
                return;
            }

            const accuracyBadge = locationResult.accuracyLevel === 'high' 
                ? '<span class="badge badge-success">High Accuracy</span>'
                : locationResult.accuracyLevel === 'medium'
                ? '<span class="badge badge-warning">Medium Accuracy</span>'
                : '<span class="badge badge-info">Approximate</span>';

            const sourceLabel = {
                'gps': 'GPS',
                'network': 'Network',
                'manual': 'Manual',
                'last_known': 'Last Known'
            }[locationResult.source] || 'Unknown';

            const html = `
                <div class="location-success alert alert-success border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="location-success-icon mr-3">
                            <i class="feather-check-circle text-success" style="font-size: 24px;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1 font-weight-bold">Location Found</h6>
                            <p class="mb-0 text-muted">
                                Source: ${sourceLabel} ${accuracyBadge}
                            </p>
                        </div>
                    </div>
                </div>
            `;

            $container.html(html).show();

            // Auto-hide after 3 seconds
            setTimeout(() => {
                this.hideSuccess(container);
            }, 3000);
        },

        /**
         * Hide success state
         */
        hideSuccess: function(container = '#locationSuccessContainer') {
            $(container).hide().empty();
        },

        /**
         * Update button loading state
         */
        setButtonLoading: function(buttonSelector, isLoading, loadingText = 'Getting location...') {
            const $button = $(buttonSelector);
            if (!$button.length) return;

            if (isLoading) {
                $button.data('original-text', $button.html());
                $button.prop('disabled', true);
                $button.html(`
                    <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                    ${loadingText}
                `);
            } else {
                const originalText = $button.data('original-text') || 'Use Current Location';
                $button.prop('disabled', false);
                $button.html(originalText);
            }
        },

        /**
         * Show map selector modal
         */
        showMapSelector: function(options = {}) {
            const opts = {
                container: options.container || '#locationMapSelector',
                initialLat: options.initialLat || null,
                initialLng: options.initialLng || null,
                onSelect: options.onSelect || null,
                onCancel: options.onCancel || null
            };

            // This would integrate with Google Maps or OpenStreetMap
            // For now, we'll create a placeholder that can be enhanced
            const $container = $(opts.container);
            if (!$container.length) {
                console.warn('[LocationUI] Map selector container not found:', opts.container);
                return;
            }

            const html = `
                <div class="modal fade" id="locationMapSelectorModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="feather-map mr-2"></i>
                                    Select Your Location
                                </h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0">
                                <div id="locationMapCanvas" style="height: 400px; width: 100%;"></div>
                                <div class="p-3 border-top">
                                    <p class="text-muted small mb-2">
                                        <i class="feather-info mr-1"></i>
                                        Drag the marker to your exact location
                                    </p>
                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">Selected Address:</label>
                                        <input type="text" class="form-control" id="locationMapAddress" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary location-map-confirm">
                                    <i class="feather-check mr-2"></i>
                                    Confirm Location
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $container.html(html);
            $('#locationMapSelectorModal').modal('show');

            // Initialize map (this would need Google Maps API or similar)
            if (opts.onSelect) {
                $('#locationMapSelectorModal').on('click', '.location-map-confirm', function() {
                    // Get selected coordinates from map
                    // This is a placeholder - actual implementation would get from map
                    const lat = opts.initialLat || 0;
                    const lng = opts.initialLng || 0;
                    opts.onSelect({ latitude: lat, longitude: lng });
                    $('#locationMapSelectorModal').modal('hide');
                });
            }

            if (opts.onCancel) {
                $('#locationMapSelectorModal').on('hidden.bs.modal', function() {
                    opts.onCancel();
                });
            }
        }
    };

    // Export to window
    window.LocationUI = LocationUI;

})(window);
