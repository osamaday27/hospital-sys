<style>
    canvas {
        border: 2px solid #000;
    }

    #tools {
        margin-top: 10px;
    }
</style>


<form id="form_ophthalmology_assessment" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <input type="text" id="patient_id" name="patient_id" hidden value="{{ $patient->id }}">
                <input id="type_visit" name="type_visit" value="{{ $patient_visits->type }}" type="hidden" />
                <input id="id_visit" name="id_visit" value="{{ $patient_visits->id }}" type="hidden" />
                <input type="text" id="id_reservation" name="id_reservation" hidden value="{{ $reservations->id }}">
                <!-- Form Inputs for Patient Data -->
                <div class="col-md-6 mb-3">
                    <label for="uava_bcva" class="form-label">UAVA/BCVA</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="UAVA/BCVA Right "
                                id="uava_bcva_right" name="uava_bcva_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="UAVA/BCVA left"
                                id="uava_bcva_left" name="uava_bcva_left">
                        </div>
                    </div>


                </div>
                <div class="col-md-6 mb-3">
                    <label for="glasses" class="form-label">Glasses</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="Glasses Right"
                                id="glasses_right" name="glasses_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="Glasses left"
                                id="glasses_left" name="glasses_left">
                        </div>
                    </div>

                </div>
                <div class="col-md-6 mb-3">
                    <label for="cycloplegic_refraction" class="form-label">Cycloplegic Refraction</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Cycloplegic Refraction Right" id="cycloplegic_refraction_right"
                                name="cycloplegic_refraction_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Cycloplegic Refraction left" id="cycloplegic_refraction_left"
                                name="cycloplegic_refraction_left">
                        </div>
                    </div>

                </div>
                <div class="col-md-6 mb-3">
                    <label for="iop" class="form-label">IOP</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="IOP Right"
                                id="iop_right" name="iop_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="IOP left"
                                id="iop_left" name="iop_left">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="pupil" class="form-label">Pupil</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="Pupil Right"
                                id="pupil_right" name="pupil_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="Pupil left"
                                id="pupil_left" name="pupil_left">
                        </div>
                    </div>

                </div>
                <div class="col-md-6 mb-3">
                    <label for="ocular_motility" class="form-label">Ocular Motility</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Ocular Motility Right" id="ocular_motility_right"
                                name="ocular_motility_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Ocular Motility left" id="ocular_motility_left" name="ocular_motility_left">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="eyelid_and_lacrimal" class="form-label">Eyelid and Lacrimal</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Eyelid and Lacrimal Right" id="eyelid_and_lacrimal_right"
                                name="eyelid_and_lacrimal_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Eyelid and Lacrimal left" id="eyelid_and_lacrimal_left"
                                name="eyelid_and_lacrimal_left">
                        </div>
                    </div>

                </div>
                <div class="col-md-6 mb-3">
                    <label for="conjunctive_and_sclera" class="form-label">Conjunctive and Sclera</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Conjunctive and Sclera Right" id="conjunctive_and_sclera_right"
                                name="conjunctive_and_sclera_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Conjunctive and Sclera left" id="conjunctive_and_sclera_left"
                                name="conjunctive_and_sclera_left">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="cornea" class="form-label">Cornea</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="cornea Right"
                                id="cornea_right" name="cornea_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="cornea left"
                                id="cornea_left" name="cornea_left">
                        </div>
                    </div>

                </div>
                <div class="col-md-6 mb-3">
                    <label for="ac_and_iris" class="form-label">AC and Iris</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="AC and Iris Right" id="ac_and_iris_right" name="ac_and_iris_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="AC and Iris left"
                                id="ac_and_iris_left" name="ac_and_iris_left">
                        </div>
                    </div>

                </div>
                <div class="col-md-6 mb-3">
                    <label for="lens" class="form-label">Lens</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="Lens Right"
                                id="lens_right" name="lens_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="Lens left"
                                id="lens_left" name="lens_left">
                        </div>
                    </div>

                </div>
                <div class="col-md-6 mb-3">
                    <label for="anterior_vitreous" class="form-label">Anterior Vitreous</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Anterior Vitreous Right" id="anterior_vitreous_right"
                                name="anterior_vitreous_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Anterior Vitreous left" id="anterior_vitreous_left"
                                name="anterior_vitreous_left">
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="col-md-12">
            <h3>Fundus</h3>
            <canvas id="eyeCanvas" width="550" height="400"></canvas>
            <div id="tools" class="mt-3">
                <div class="row">
                    <div class="col-4">
                        <div class="d-flex align-items-center mb-2">
                            <label for="color" class="me-2">{{ trans('doctor_trans.color') }}:</label>
                            <input type="color" id="color" value="#000000">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex align-items-center mb-2">
                            <label for="size" class="me-2">{{ trans('doctor_trans.brush_size') }}:</label>
                            <input type="range" id="size" min="1" max="10" value="5">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-2">
                            <button id="newDrawing" type="button"
                                class="btn btn-sm btn-outline-secondary">{{ trans('doctor_trans.new') }}</button>
                            <button id="undo" type="button"
                                class="btn btn-sm btn-outline-danger">{{ trans('doctor_trans.back') }}</button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-2">
                            <button id="rotate" type="button"
                                class="btn btn-sm btn-outline-secondary">{{ trans('doctor_trans.change_the_image_orientation') }}</button>
                            <button id="saveImage" type="button"
                                class="btn btn-sm btn-outline-primary">{{ trans('doctor_trans.save the image') }}</button>
                            <button id="savedata" type="button"
                                class="btn btn-sm btn-outline-success">{{ trans('doctor_trans.save_data_btn') }}</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    const canvas = document.getElementById('eyeCanvas');
    const ctx = canvas.getContext('2d');
    const colorPicker = document.getElementById('color');
    const sizePicker = document.getElementById('size');
    const clearBtn = document.getElementById('clear');
    const newDrawingBtn = document.getElementById('newDrawing');
    const undoBtn = document.getElementById('undo');
    const saveImageBtn = document.getElementById('saveImage');
    const shapeSelect = document.getElementById('shape');
    const rotateBtn = document.getElementById('rotate');
    const savedatabtn = document.getElementById('savedata');

    let drawing = false;
    let color = '#000000';
    let size = 5;
    let shapes = []; // لتخزين الأشكال المرسومة
    let currentPath = []; // لتخزين المسار الحالي لرسم
    let img = new Image(); // الصورة
    let angle = 0; // زاوية التدوير

    // تحميل صورة من المصدر المحلي
    img.src = "{{ URL::asset('assets/images/medical_sheet/sheet1.png') }}"; // Use double quotes for the URL
    img.onload = function() {
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
    };
    img.onerror = function() {
        alert('حدث خطأ أثناء تحميل الصورة. تحقق من اسم الصورة والمسار.');
    };

    // بدء الرسم
    canvas.addEventListener('mousedown', (event) => {
        drawing = true;
        currentPath = []; // إعادة تعيين المسار الحالي عند بدء الرسم
        ctx.lineWidth = size;
        ctx.lineCap = 'round'; // يجعل نهاية الخط مستديرة
        ctx.lineJoin = 'round'; // يجعل زوايا الخط ناعمة
        ctx.strokeStyle = color;

        currentPath.push({
            x: event.offsetX,
            y: event.offsetY
        });
    });

    canvas.addEventListener('mouseup', () => {
        drawing = false;
        ctx.beginPath(); // إنهاء المسار الحالي
        shapes.push(currentPath); // حفظ المسار في الأشكال
    });

    canvas.addEventListener('mousemove', draw);

    function draw(event) {
        if (!drawing) return;
        const x = event.offsetX;
        const y = event.offsetY;

        // استخدم lineTo لتحسين استمرارية الرسم
        ctx.lineTo(x, y);
        ctx.stroke();
        currentPath.push({
            x: x,
            y: y
        });
        ctx.beginPath();
        ctx.moveTo(x, y); // نقل المؤشر إلى النقطة الحالية للرسم السلس
    }

    // تحديث اللون وحجم الفرشاة
    colorPicker.addEventListener('input', (e) => {
        color = e.target.value;
    });

    sizePicker.addEventListener('input', (e) => {
        size = e.target.value;
    });



    // زر "جديد"
    newDrawingBtn.addEventListener('click', () => {
        if (confirm("هل أنت متأكد أنك تريد بدء رسم جديد؟")) {
            clearCanvas(); // مسح اللوحة
            clearForm(); // مسح النموذج
        }
    });
    // دالة لمسح النموذج
    function clearForm() {
        const form = document.getElementById('form_ophthalmology_assessment'); // استبدل 'myForm' بـ ID النموذج الخاص بك
        if (form) {
            form.reset(); // يقوم بإعادة تعيين جميع الحقول إلى قيمها الافتراضية
        }
    }
    // زر "رجوع"
    undoBtn.addEventListener('click', () => {
        if (shapes.length > 0) {
            shapes.pop(); // إزالة آخر شكل
            redrawCanvas(); // إعادة رسم اللوحة
        }
    });

    function redrawCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height); // إعادة تحميل الصورة
        shapes.forEach(shape => {
            ctx.beginPath();
            shape.forEach(point => {
                ctx.lineTo(point.x, point.y);
            });
            ctx.strokeStyle = color; // اللون الافتراضي للتحديد
            ctx.lineWidth = size;
            ctx.stroke();
        });
    }

    // مسح اللوحة
    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        shapes = []; // مسح الأشكال
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height); // إعادة تحميل الصورة
    }

    // تغيير اتجاه الصورة
    rotateBtn.addEventListener('click', () => {
        angle += 90; // زيادة الزاوية بـ 90 درجة
        ctx.clearRect(0, 0, canvas.width, canvas.height); // مسح اللوحة
        ctx.save(); // حفظ الحالة الحالية
        ctx.translate(canvas.width / 2, canvas.height / 2); // الانتقال إلى مركز اللوحة
        ctx.rotate(angle * Math.PI / 180); // تدوير الصورة
        ctx.drawImage(img, -img.width / 2, -img.height / 2, img.width, img.height); // رسم الصورة
        ctx.restore(); // استعادة الحالة المحفوظة
    });
    // إضافة التعليق بالنقر بزر الفأرة الأيمن
    // إضافة التعليق بالنقر بزر الفأرة الأيمن
    canvas.addEventListener('contextmenu', (event) => {
        event.preventDefault(); // منع القائمة الافتراضية
        const comment = prompt("أدخل التعليق: ");
        if (comment) {
            // استخدم إحداثيات الماوس مباشرة
            const x = event.offsetX; // إحداثيات x
            const y = event.offsetY; // إحداثيات y

            ctx.font = '20px Arial';
            ctx.fillStyle = color; // استخدم اللون المحدد

            // رسم التعليق
            ctx.fillText(comment, x, y);
        }
    });


    // حفظ الصورة المعدلة
    saveImageBtn.addEventListener('click', () => {
        canvas.toBlob((blob) => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'modified-eye-image.png';
            document.body.appendChild(link); // إضافة الرابط إلى DOM
            link.click(); // محاكاة النقر على الرابط
            document.body.removeChild(link); // إزالة الرابط بعد النقر
        }, 'image/png');
    });


    // حفظ الصورة المعدلة
    savedatabtn.addEventListener('click', () => {

        event.preventDefault();

        var formData = new FormData($('#form_ophthalmology_assessment')[0]);

        // إضافة الصورة من canvas إلى FormData
        canvas.toBlob((blob) => {
            formData.append('image_path', blob, 'eye_image.png');

            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/save_ophthalmology_assessment',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                beforeSend: function() {
                    $('#savedata').html('....Please wait');
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            position: "top-end",
                            icon: "success",
                            title: "تم حفظ البيانات بنجاح",
                            showConfirmButton: false,
                            timer: 1000
                        });
                    } else {
                        alert('فشل الحفظ: ' + response.message);
                    }
                },
                error: function() {
                    alert('حدث خطأ أثناء حفظ البيانات.');
                },
                complete: function(response) {
                    $('#savedata').html('Save Data');
                }
            });
        }, 'image/png');

    });
</script>
