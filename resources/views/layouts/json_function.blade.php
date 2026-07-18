<script>
    $(document).ready(function() {
        // عند تغيير الدولة
        $('select[name="id_country"]').on('change', function() {
            var id_country = $(this).val();

            // تعطيل القوائم
            $('select[name="id_governmentes"], select[name="id_city"], select[name="id_area"]').prop('disabled', true);

            if (id_country) {
                $.ajax({
                    url: "{{ URL::to('get_governmentes') }}/" + id_country,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        var govSelect = $('select[name="id_governmentes"]');
                        var citySelect = $('select[name="id_city"]');
                        var areaSelect = $('select[name="id_area"]');

                        // تفريغ القوائم
                        govSelect.empty();
                        citySelect.empty();
                        areaSelect.empty();

                        // تعبئة الخيارات الافتراضية
                        govSelect.append('<option value="" selected disabled>{{ trans('patient_trans.select_option') }}</option>');
                        citySelect.append('<option value="" selected disabled>{{ trans('patient_trans.select_option') }}</option>');
                        areaSelect.append('<option value="" selected disabled>{{ trans('patient_trans.select_option') }}</option>');

                        $.each(data, function(key, value) {
                            govSelect.append('<option value="' + key + '">' + value + '</option>');
                        });

                        // تفعيل القائمة بعد التحميل
                        govSelect.prop('disabled', false);
                    },
                    error: function() {
                        console.log('Failed to load governmentes');
                    }
                });
            }
        });
    });

    // عند اختيار المحافظة
    function get_city(id_governmentes) {
        var citySelect = $('select[name="id_city"]');
        var areaSelect = $('select[name="id_area"]');

        citySelect.prop('disabled', true);
        areaSelect.prop('disabled', true);

        if (id_governmentes) {
            $.ajax({
                url: "{{ URL::to('get_city') }}/" + id_governmentes,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    citySelect.empty();
                    areaSelect.empty();

                    citySelect.append('<option value="" selected disabled>{{ trans('patient_trans.select_option') }}</option>');
                    areaSelect.append('<option value="" selected disabled>{{ trans('patient_trans.select_option') }}</option>');

                    $.each(data, function(key, value) {
                        citySelect.append('<option value="' + key + '">' + value + '</option>');
                    });

                    citySelect.prop('disabled', false);
                },
                error: function() {
                    console.log('Failed to load cities');
                }
            });
        }
    }

    // عند اختيار المدينة
    function get_area(id_city) {
        var areaSelect = $('select[name="id_area"]');

        areaSelect.prop('disabled', true);

        if (id_city) {
            $.ajax({
                url: "{{ URL::to('get_area') }}/" + id_city,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    areaSelect.empty();
                    areaSelect.append('<option value="" selected disabled>{{ trans('patient_trans.select_option') }}</option>');

                    $.each(data, function(key, value) {
                        areaSelect.append('<option value="' + key + '">' + value + '</option>');
                    });

                    areaSelect.prop('disabled', false);
                },
                error: function() {
                    console.log('Failed to load areas');
                }
            });
        }
    }
</script>
