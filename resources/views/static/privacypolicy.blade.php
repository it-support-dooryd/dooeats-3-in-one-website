@include('layouts.app')

@include('layouts.header')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white border-0 py-4 px-5">
                    <h2 class="font-weight-bold mb-0" style="color: #102A1C;">Privacy Policy</h2>
                </div>
                <div class="card-body p-5">
                    <div id="data-table_processing" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="privacy_policy" id="privacy_policy" style="line-height: 1.8; color: #444;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script type="text/javascript">
    $(document).ready(function() {
        $("#data-table_processing").show();
        var privacyPolicyRef = database.collection('settings').doc('privacyPolicy');
        privacyPolicyRef.get().then(async function (privacyPolicySnapshots) {
            var privacyPolicyData = privacyPolicySnapshots.data();
            if (privacyPolicyData && privacyPolicyData.privacy_policy) {
                $('#privacy_policy').html(privacyPolicyData.privacy_policy);
            } else {
                $('#privacy_policy').html('<p class="text-center mt-4">Privacy policy content not available.</p>');
            }
            $("#data-table_processing").hide();
        }).catch(function(error) {
            console.error("Error getting privacy policy:", error);
            $('#privacy_policy').html('<p class="text-danger text-center mt-4">Failed to load privacy policy.</p>');
            $("#data-table_processing").hide();
        });
    });
</script>