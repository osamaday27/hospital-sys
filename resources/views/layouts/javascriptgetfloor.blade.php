       <script>
   $(document).ready(function() {
    $('#building_id').on('change', function() {
        let buildingId = $(this).val();
        $('#floor_id').empty().append(`<option value="">{{ trans('outpatient_trans.select_floor') }}</option>`);
        if (buildingId) {
            $.ajax({
                url: `/get-floors/${buildingId}`, // تأكد من وجود الشرطية الصحيحة هنا
                type: 'GET',
                success: function(data) {
                    let locale = '{{ app()->getLocale() }}';
                    $.each(data, function(key, floor) {
                        let floorName = locale === 'ar' ? floor.name_ar : floor.name_en;
                        $('#floor_id').append(`<option value="${floor.id}">${floorName}</option>`);
                    });
                },
                error: function() {
                    alert('{{ trans('outpatient_trans.error_loading_floors') }}');
                }
            });
        }
    });
});
</script>