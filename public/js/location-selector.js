/**
 * Location Selection Modal JavaScript
 * Handles location popup, geolocation, search, and recent locations
 */

(function ($) {
  'use strict';

  // Configuration
  const MAX_RECENT_LOCATIONS = 5;
  const RECENT_LOCATIONS_KEY = 'recent_locations';

  // DOM Elements
  let $locationModal;
  let $useCurrentLocationBtn;
  let $locationSearchInput;
  let $autocompleteResults;
  let $recentLocationsList;
  let $errorAlert;
  let $errorMessage;

  /**
   * Initialize the location selector
   */
  function initLocationSelector() {
    // Cache DOM elements
    $locationModal = $('#locationModal');
    $useCurrentLocationBtn = $('#useCurrentLocationBtn');
    $locationSearchInput = $('#locationSearchInput');
    $autocompleteResults = $('#locationAutocompleteResults');
    $recentLocationsList = $('#recentLocationsList');
    $errorAlert = $('#locationErrorAlert');
    $errorMessage = $('#locationErrorMessage');

    // Bind events
    bindEvents();

    // Load recent locations
    loadRecentLocations();
  }

  /**
   * Bind event handlers
   */
  function bindEvents() {
    // Use current location button
    $useCurrentLocationBtn.on('click', handleCurrentLocationClick);

    // Search input
    $locationSearchInput.on('input', handleSearchInput);

    // Modal close - clear errors
    $locationModal.on('hidden.bs.modal', function () {
      hideError();
      resetCurrentLocationButton();
    });

    // Recent location click
    $recentLocationsList.on('click', '.recent-location-item', handleRecentLocationClick);
  }

  /**
   * Handle current location button click
   */
  async function handleCurrentLocationClick() {
    hideError();
    showLoading(true);
    LocationUI.setButtonLoading('#useCurrentLocationBtn', true, 'Getting location...');

    try {
      // Use comprehensive LocationService
      const result = await LocationService.getCurrentLocation({
        enableHighAccuracy: true,
        timeout: 15000,
        retries: 3,
        fallbackToNetwork: true,
        fallbackToLastKnown: true
      });

      if (result.success) {
        handleGeolocationSuccess(result);
      } else {
        handleGeolocationError(result);
      }
    } catch (error) {
      console.error('[LocationSelector] Unexpected error:', error);
      handleGeolocationError({
        success: false,
        error: 'UNEXPECTED_ERROR',
        message: 'An unexpected error occurred. Please try again.'
      });
    } finally {
      showLoading(false);
      LocationUI.setButtonLoading('#useCurrentLocationBtn', false);
    }
  }

  /**
   * Handle geolocation success
   */
  async function handleGeolocationSuccess(result) {
    const lat = result.latitude;
    const lng = result.longitude;

    // Show success message
    if (window.LocationUI) {
      LocationUI.showSuccess(result, '#locationSuccessContainer');
    }

    // Reverse geocode to get address
    try {
      const addressData = await reverseGeocode(lat, lng);
      
      // Sync location across all features
      if (window.LocationSync) {
        LocationSync.syncToAllFeatures({
          latitude: lat,
          longitude: lng,
          address_name: addressData.address_name || addressData.formatted_address || '',
          address_city: addressData.city || '',
          address_state: addressData.state || '',
          address_country: addressData.country || '',
          address_zip: addressData.zip || '',
          source: result.source
        });
      } else {
        // Fallback to old method
        setLocationCoordinates(lat, lng);
      }

      // Reload page after short delay to show success message
      setTimeout(() => {
        if (typeof getCurrentLocation === 'function') {
          getCurrentLocation('reload');
        } else {
          window.location.reload();
        }
      }, 1000);
    } catch (error) {
      console.error('[LocationSelector] Reverse geocode error:', error);
      // Still save coordinates even if reverse geocode fails
      if (window.LocationSync) {
        LocationSync.syncToAllFeatures({
          latitude: lat,
          longitude: lng,
          source: result.source
        });
      } else {
        setLocationCoordinates(lat, lng);
      }
      setTimeout(() => window.location.reload(), 1000);
    }
  }

  /**
   * Handle geolocation error
   */
  function handleGeolocationError(errorResult) {
    showLoading(false);

    // Use LocationErrorHandler for better error display
    if (window.LocationErrorHandler) {
      LocationErrorHandler.showError(errorResult, {
        container: '#locationErrorAlert',
        onRetry: () => {
          handleCurrentLocationClick();
        },
        onManual: () => {
          // Show manual input or map selector
          if (window.LocationUI) {
            LocationUI.showMapSelector({
              onSelect: (location) => {
                handleGeolocationSuccess({
                  success: true,
                  latitude: location.latitude,
                  longitude: location.longitude,
                  source: 'manual'
                });
              }
            });
          } else {
            // Fallback: focus on search input
            $locationSearchInput.focus();
          }
        },
        onSettings: () => {
          // Open browser settings (if possible)
          if (navigator.permissions) {
            navigator.permissions.query({ name: 'geolocation' }).then(result => {
              alert('Please enable location access in your browser settings and try again.');
            });
          } else {
            alert('Please enable location access in your browser settings and try again.');
          }
        },
        onLastKnown: () => {
          const lastKnown = LocationService.getLastKnownLocation();
          if (lastKnown) {
            handleGeolocationSuccess(lastKnown);
          } else {
            alert('No last known location available.');
          }
        }
      });
    } else {
      // Fallback to simple error message
      showError(errorResult.message || 'Unable to fetch your location');
    }
  }

  /**
   * Reverse geocode coordinates to address
   */
  async function reverseGeocode(lat, lng) {
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
              address_name: result.formatted_address
            };
            
            result.address_components.forEach(component => {
              if (component.types.includes('locality')) {
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
              address_name: data.display_name,
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

  /**
   * Set location from coordinates
   */
  function setLocationCoordinates(lat, lng) {
    if (typeof setCookie === 'function') {
      setCookie('address_lat', lat, 365);
      setCookie('address_lng', lng, 365);
      window.location.reload();
    }
  }

  /**
   * Handle search input
   */
  function handleSearchInput() {
    const query = $locationSearchInput.val().trim();

    if (query.length < 3) {
      $autocompleteResults.empty().hide();
      return;
    }

    // Use existing autocomplete if available
    // The autocomplete is already initialized in footer.blade.php
    // This is just for visual feedback
  }

  /**
   * Handle recent location click
   */
  function handleRecentLocationClick(e) {
    e.preventDefault();

    const $item = $(this);
    const locationData = $item.data('location');

    if (locationData) {
      applyLocation(locationData);
    }
  }

  /**
   * Apply selected location
   */
  function applyLocation(locationData) {
    if (typeof setCookie === 'function') {
      setCookie('address_name', locationData.name || '', 365);
      setCookie('address_lat', locationData.lat || '', 365);
      setCookie('address_lng', locationData.lng || '', 365);
      setCookie('address_city', locationData.city || '', 365);
      setCookie('address_state', locationData.state || '', 365);
      setCookie('address_country', locationData.country || '', 365);

      // Save to recent locations
      saveToRecentLocations(locationData);

      // Reload page
      window.location.reload();
    }
  }

  /**
   * Load recent locations from localStorage
   */
  function loadRecentLocations() {
    const recentLocations = getRecentLocations();

    $recentLocationsList.empty();

    if (recentLocations.length === 0) {
      $recentLocationsList.html(
        '<div class="text-muted small text-center py-3">' +
        (window.locationStrings?.noRecent || 'No recent locations') +
        '</div>'
      );
      return;
    }

    recentLocations.forEach(function (location) {
      const $item = $('<div class="recent-location-item"></div>')
        .data('location', location)
        .html(
          '<i class="feather-map-pin mr-2 text-primary"></i>' +
          '<div class="location-details">' +
          '<div class="location-name">' + escapeHtml(location.name) + '</div>' +
          '<div class="location-address text-muted">' +
          escapeHtml(location.city || location.country || '') +
          '</div>' +
          '</div>' +
          '<i class="feather-chevron-right ml-auto"></i>'
        );

      $recentLocationsList.append($item);
    });
  }

  /**
   * Get recent locations from localStorage
   */
  function getRecentLocations() {
    try {
      const stored = localStorage.getItem(RECENT_LOCATIONS_KEY);
      return stored ? JSON.parse(stored) : [];
    } catch (e) {
      console.error('Error reading recent locations:', e);
      return [];
    }
  }

  /**
   * Save location to recent locations
   */
  function saveToRecentLocations(locationData) {
    try {
      let recentLocations = getRecentLocations();

      // Remove if already exists
      recentLocations = recentLocations.filter(function (loc) {
        return !(loc.lat === locationData.lat && loc.lng === locationData.lng);
      });

      // Add to beginning
      recentLocations.unshift({
        name: locationData.name,
        lat: locationData.lat,
        lng: locationData.lng,
        city: locationData.city,
        state: locationData.state,
        country: locationData.country,
        timestamp: Date.now()
      });

      // Keep only MAX_RECENT_LOCATIONS
      recentLocations = recentLocations.slice(0, MAX_RECENT_LOCATIONS);

      localStorage.setItem(RECENT_LOCATIONS_KEY, JSON.stringify(recentLocations));
    } catch (e) {
      console.error('Error saving recent location:', e);
    }
  }

  /**
   * Show loading state on current location button
   */
  function showLoading(isLoading) {
    if (isLoading) {
      $useCurrentLocationBtn
        .prop('disabled', true)
        .find('.spinner-border').removeClass('d-none');
      $useCurrentLocationBtn.find('.btn-text').text('Fetching location...');
    } else {
      resetCurrentLocationButton();
    }
  }

  /**
   * Reset current location button
   */
  function resetCurrentLocationButton() {
    $useCurrentLocationBtn
      .prop('disabled', false)
      .find('.spinner-border').addClass('d-none');
    $useCurrentLocationBtn.find('.btn-text').text(
      window.locationStrings?.useCurrentLocation || 'Use Current Location'
    );
  }

  /**
   * Show error message
   */
  function showError(message) {
    $errorMessage.text(message);
    $errorAlert.removeClass('d-none');
  }

  /**
   * Hide error message
   */
  function hideError() {
    $errorAlert.addClass('d-none');
    $errorMessage.text('');
  }

  /**
   * Escape HTML to prevent XSS
   */
  function escapeHtml(text) {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text ? String(text).replace(/[&<>"']/g, function (m) { return map[m]; }) : '';
  }

  /**
   * Open location modal (expose globally)
   */
  window.openLocationModal = function () {
    if ($locationModal) {
      hideError();
      loadRecentLocations();
      $locationModal.modal('show');
    }
  };

  // Initialize on document ready
  $(document).ready(function () {
    initLocationSelector();
  });

})(jQuery);
