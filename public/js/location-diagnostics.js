/**
 * Location Diagnostics and Analytics
 * Tracks location attempts, errors, and user actions for analytics
 */

(function(window) {
    'use strict';

    const LocationDiagnostics = {
        analytics: {
            totalAttempts: 0,
            successfulAttempts: 0,
            failedAttempts: 0,
            errorTypes: {},
            sourceDistribution: {},
            accuracyDistribution: {},
            userActions: {},
            averageAccuracy: 0,
            averageResponseTime: 0,
            responseTimes: []
        },

        /**
         * Track location attempt
         */
        trackAttempt: function(method) {
            this.analytics.totalAttempts++;
            
            if (window.analytics && typeof window.analytics.track === 'function') {
                window.analytics.track('Location Attempt', {
                    method: method,
                    timestamp: Date.now()
                });
            }
        },

        /**
         * Track successful location
         */
        trackSuccess: function(result) {
            this.analytics.successfulAttempts++;
            
            // Track source distribution
            const source = result.source || 'unknown';
            this.analytics.sourceDistribution[source] = (this.analytics.sourceDistribution[source] || 0) + 1;
            
            // Track accuracy distribution
            const accuracyLevel = result.accuracyLevel || 'unknown';
            this.analytics.accuracyDistribution[accuracyLevel] = (this.analytics.accuracyDistribution[accuracyLevel] || 0) + 1;
            
            // Track accuracy value
            if (result.accuracy) {
                this.analytics.responseTimes.push(Date.now());
                this.updateAverageAccuracy(result.accuracy);
            }

            if (window.analytics && typeof window.analytics.track === 'function') {
                window.analytics.track('Location Success', {
                    source: source,
                    accuracy: result.accuracy,
                    accuracyLevel: accuracyLevel,
                    timestamp: Date.now()
                });
            }
        },

        /**
         * Track failed location
         */
        trackFailure: function(errorCode, errorDetails = {}) {
            this.analytics.failedAttempts++;
            
            // Track error types
            this.analytics.errorTypes[errorCode] = (this.analytics.errorTypes[errorCode] || 0) + 1;

            if (window.analytics && typeof window.analytics.track === 'function') {
                window.analytics.track('Location Failure', {
                    errorCode: errorCode,
                    errorDetails: errorDetails,
                    timestamp: Date.now()
                });
            }
        },

        /**
         * Track user action
         */
        trackUserAction: function(action, details = {}) {
            this.analytics.userActions[action] = (this.analytics.userActions[action] || 0) + 1;

            if (window.analytics && typeof window.analytics.track === 'function') {
                window.analytics.track('Location User Action', {
                    action: action,
                    details: details,
                    timestamp: Date.now()
                });
            }
        },

        /**
         * Update average accuracy
         */
        updateAverageAccuracy: function(accuracy) {
            const total = this.analytics.successfulAttempts;
            const currentAvg = this.analytics.averageAccuracy;
            this.analytics.averageAccuracy = ((currentAvg * (total - 1)) + accuracy) / total;
        },

        /**
         * Get diagnostics report
         */
        getReport: function() {
            const successRate = this.analytics.totalAttempts > 0 
                ? (this.analytics.successfulAttempts / this.analytics.totalAttempts * 100).toFixed(2)
                : 0;

            return {
                summary: {
                    totalAttempts: this.analytics.totalAttempts,
                    successfulAttempts: this.analytics.successfulAttempts,
                    failedAttempts: this.analytics.failedAttempts,
                    successRate: successRate + '%',
                    averageAccuracy: this.analytics.averageAccuracy.toFixed(2) + 'm'
                },
                errorTypes: this.analytics.errorTypes,
                sourceDistribution: this.analytics.sourceDistribution,
                accuracyDistribution: this.analytics.accuracyDistribution,
                userActions: this.analytics.userActions,
                locationServiceLog: window.LocationService ? window.LocationService.getErrorLog() : []
            };
        },

        /**
         * Export diagnostics data
         */
        exportData: function() {
            const report = this.getReport();
            const dataStr = JSON.stringify(report, null, 2);
            const dataBlob = new Blob([dataStr], { type: 'application/json' });
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `location-diagnostics-${Date.now()}.json`;
            link.click();
            URL.revokeObjectURL(url);
        },

        /**
         * Clear diagnostics data
         */
        clear: function() {
            this.analytics = {
                totalAttempts: 0,
                successfulAttempts: 0,
                failedAttempts: 0,
                errorTypes: {},
                sourceDistribution: {},
                accuracyDistribution: {},
                userActions: {},
                averageAccuracy: 0,
                averageResponseTime: 0,
                responseTimes: []
            };
        },

        /**
         * Initialize diagnostics
         */
        init: function() {
            // Listen to LocationService events
            if (window.LocationService) {
                // Track location attempts
                const originalGetLocation = LocationService.getCurrentLocation;
                LocationService.getCurrentLocation = async function(...args) {
                    LocationDiagnostics.trackAttempt('getCurrentLocation');
                    const startTime = Date.now();
                    const result = await originalGetLocation.apply(this, args);
                    const responseTime = Date.now() - startTime;
                    
                    if (result.success) {
                        LocationDiagnostics.trackSuccess(result);
                    } else {
                        LocationDiagnostics.trackFailure(result.error, result.errorDetails);
                    }
                    
                    return result;
                };
            }

            // Track user actions on error handlers
            if (window.LocationErrorHandler) {
                const originalShowError = LocationErrorHandler.showError;
                LocationErrorHandler.showError = function(errorResult, options) {
                    LocationDiagnostics.trackUserAction('error_shown', { error: errorResult.error });
                    return originalShowError.apply(this, arguments);
                };
            }

            // Track manual location selection
            $(document).on('location:manual', function(e, locationData) {
                LocationDiagnostics.trackUserAction('manual_selection');
            });

            // Track retry attempts
            $(document).on('location:retry', function() {
                LocationDiagnostics.trackUserAction('retry');
            });

            // Track fallback usage
            $(document).on('location:fallback', function(e, fallbackType) {
                LocationDiagnostics.trackUserAction('fallback_used', { type: fallbackType });
            });
        }
    };

    // Initialize on DOM ready
    $(document).ready(function() {
        LocationDiagnostics.init();
    });

    // Export to window
    window.LocationDiagnostics = LocationDiagnostics;

})(window);
