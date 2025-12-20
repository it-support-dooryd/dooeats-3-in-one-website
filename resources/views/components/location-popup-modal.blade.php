<!-- Location Selection Popup Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content location-modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title font-weight-bold" id="locationModalLabel">
                    <i class="feather-map-pin text-primary mr-2"></i>
                    {{ trans('lang.select_location') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4">
                <!-- Current Location Button -->
                <button type="button" class="btn btn-primary btn-block mb-3 use-current-location-btn" id="useCurrentLocationBtn">
                    <i class="feather-navigation mr-2"></i>
                    <span class="btn-text">{{ trans('lang.use_current_location') }}</span>
                    <span class="spinner-border spinner-border-sm ml-2 d-none" role="status" aria-hidden="true"></span>
                </button>

                <!-- Search Location -->
                <div class="location-search-wrapper mb-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0">
                                <i class="feather-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" 
                               class="form-control border-left-0 location-search-input" 
                               id="locationSearchInput" 
                               placeholder="{{ trans('lang.search_location_placeholder') }}"
                               autocomplete="off"
                               onkeyup="syncLocationInput(this.value)"
                               onfocus="syncLocationInputFocus()">
                    </div>
                    <!-- Autocomplete Results -->
                    <div class="location-autocomplete-results" id="locationAutocompleteResults"></div>
                </div>

                <!-- Divider -->
                <div class="location-divider mb-3">
                    <span class="divider-text">{{ trans('lang.or') }}</span>
                </div>

                <!-- Recent Locations -->
                <div class="recent-locations-wrapper">
                    <h6 class="text-muted small font-weight-bold mb-2">
                        <i class="feather-clock mr-1"></i>
                        {{ trans('lang.recent_locations') }}
                    </h6>
                    <div class="recent-locations-list" id="recentLocationsList">
                        <!-- Recent locations will be populated by JavaScript -->
                    </div>
                </div>



                <!-- Geolocation Info -->
                <div class="location-info-box mt-3 p-3 bg-light rounded">
                    <small class="text-muted">
                        <i class="feather-info mr-1"></i>
                        {{ trans('lang.location_permission_info') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
