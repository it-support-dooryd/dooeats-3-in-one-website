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
  function handleCurrentLocationClick() {
    hideError();
    showLoading(true);

    if (!navigator.geolocation) {
      showError(window.locationStrings?.notSupported || 'Geolocation is not supported by your browser');
      showLoading(false);
      return;
    }

    navigator.geolocation.getCurrentPosition(
      handleGeolocationSuccess,
      handleGeolocationError,
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
      }
    );
  }

  /**
   * Handle geolocation success
   */
  function handleGeolocationSuccess(position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;

    // Use existing getCurrentLocation function
    if (typeof getCurrentLocation === 'function') {
      getCurrentLocation('reload');
    } else {
      // Fallback: manually set location
      setLocationCoordinates(lat, lng);
    }
  }

  /**
   * Handle geolocation error
   */
  function handleGeolocationError(error) {
    showLoading(false);

    let errorMsg = window.locationStrings?.fetchError || 'Unable to fetch your location';

    switch (error.code) {
      case error.PERMISSION_DENIED:
        errorMsg = window.locationStrings?.permissionDenied || 'Location permission denied';
        break;
      case error.POSITION_UNAVAILABLE:
        errorMsg = 'Location information is unavailable';
        break;
      case error.TIMEOUT:
        errorMsg = 'Location request timed out';
        break;
    }

    showError(errorMsg);
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

  // Expose globally
  window.saveToRecentLocations = saveToRecentLocations;

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
