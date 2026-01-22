@include('layouts.app')

@include('layouts.header')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white border-0 py-4 px-5">
                    <h2 class="font-weight-bold mb-0" style="color: #102A1C;">Terms of Use</h2>
                </div>
                <div class="card-body p-5">
                    <div id="data-table_processing" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="terms" id="terms" style="line-height: 1.8; color: #444;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script type="text/javascript">
    $(document).ready(function() {
        $("#data-table_processing").show();
        var termsAndConditionsRef = database.collection('settings').doc('termsAndConditions');
        termsAndConditionsRef.get().then(async function (termsAndConditionsSnapshots) {
            var termsAndConditionsData = termsAndConditionsSnapshots.data();
            if (termsAndConditionsData && termsAndConditionsData.termsAndConditions) {
                $('#terms').html(termsAndConditionsData.termsAndConditions);
            } else {
                $('#terms').html('<p class="text-center mt-4">Terms and conditions content not available.</p>');
            }
            $("#data-table_processing").hide();
        }).catch(function(error) {
            console.error("Error getting terms:", error);
            $('#terms').html('<p class="text-danger text-center mt-4">Failed to load terms and conditions.</p>');
            $("#data-table_processing").hide();
        });
    });
</script>