<style>
    .canvas-follow {
        border: 2px solid #000;
    }

    #tools-follow {
        margin-top: 10px;
    }
</style>

<form id="form-follow-up-ophthalmology-assessment" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <input type="text" id="patient-id" name="patient_id" hidden value="{{ $patient->id }}">
                <input id="visit-type" name="type_visit" value="{{ $patient_visits->type }}" type="hidden" />
                <input id="visit-id" name="id_visit" value="{{ $patient_visits->id }}" type="hidden" />
                <input type="text" id="reservation-id" name="id_reservation" hidden value="{{ $reservations->id }}">
                <!-- Form Inputs for Patient Data -->
                <div class="col-md-12 mb-3">
                    <div class="row">
                        <label for="uava_bcva" class="form-label">UAVA/BCVA</label>

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

                <div class="col-md-12 mb-3">
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
                <div class="col-md-12 mb-3">
                    <label for="ant-segment" class="form-label">Ant.Segment</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="Ant.Segment Right"
                                id="ant_segment_right" name="ant_segment_right">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" placeholder="Ant.Segment left"
                                id="ant_segment_left" name="ant_segment_left">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-12">
            <h3>Fundus</h3>
            <canvas id="eye-canvas-follow" width="550" height="400"></canvas>
            <div id="tools-follow" class="mt-3">
                <div class="row">
                    <div class="col-4">
                        <div class="d-flex align-items-center mb-2">
                            <label for="color-picker" class="me-2">{{ trans('doctor_trans.color') }}:</label>
                            <input type="color" id="color-picker" value="#000000">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-flex align-items-center mb-2">
                            <label for="brush-size" class="me-2">{{ trans('doctor_trans.brush_size') }}:</label>
                            <input type="range" id="brush-size" min="1" max="10" value="5">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-2">
                            <button id="new-drawing" type="button"
                                class="btn btn-sm btn-outline-secondary">{{ trans('doctor_trans.new') }}</button>
                            <button id="undo-drawing" type="button"
                                class="btn btn-sm btn-outline-danger">{{ trans('doctor_trans.back') }}</button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-2">
                            <button id="rotate-image" type="button"
                                class="btn btn-sm btn-outline-secondary">{{ trans('doctor_trans.change_the_image_orientation') }}</button>
                            <button id="save-image" type="button"
                                class="btn btn-sm btn-outline-primary">{{ trans('doctor_trans.save the image') }}</button>
                            <button id="save-data" type="button"
                                class="btn btn-sm btn-outline-success">{{ trans('doctor_trans.save_data_btn') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // دالة لإنشاء لوحة رسم
    function createCanvas(canvasId, colorPickerId, sizePickerId, newDrawingBtnId, undoBtnId, rotateBtnId,
        saveImageBtnId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        const colorPicker = document.getElementById(colorPickerId);
        const sizePicker = document.getElementById(sizePickerId);
        const newDrawingBtn = document.getElementById(newDrawingBtnId);
        const undoBtn = document.getElementById(undoBtnId);
        const rotateBtn = document.getElementById(rotateBtnId);
        const saveImageBtn = document.getElementById(saveImageBtnId);

        let drawing = false;
        let color = '#000000';
        let size = 5;
        let shapes = [];
        let currentPath = [];
        let img = new Image();
        let angle = 0;

        // تحميل صورة من المصدر المحلي
        img.src = "{{ URL::asset('assets/images/medical_sheet/sheet1.png') }}";
        img.onload = function() {
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        };
        img.onerror = function() {
            alert('حدث خطأ أثناء تحميل الصورة. تحقق من اسم الصورة والمسار.');
        };

        // بدء الرسم
        canvas.addEventListener('mousedown', (event) => {
            drawing = true;
            currentPath = [];
            ctx.lineWidth = size;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.strokeStyle = color;

            currentPath.push({
                x: event.offsetX,
                y: event.offsetY
            });
        });

        canvas.addEventListener('mouseup', () => {
            drawing = false;
            ctx.beginPath();
            shapes.push(currentPath);
        });

        canvas.addEventListener('mousemove', draw);

        function draw(event) {
            if (!drawing) return;
            const x = event.offsetX;
            const y = event.offsetY;

            ctx.lineTo(x, y);
            ctx.stroke();
            currentPath.push({
                x: x,
                y: y
            });
            ctx.beginPath();
            ctx.moveTo(x, y);
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
                clearCanvas();
            }
        });

        // زر "رجوع"
        undoBtn.addEventListener('click', () => {
            if (shapes.length > 0) {
                shapes.pop();
                redrawCanvas();
            }
        });

        function redrawCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            shapes.forEach(shape => {
                ctx.beginPath();
                shape.forEach(point => {
                    ctx.lineTo(point.x, point.y);
                });
                ctx.strokeStyle = color;
                ctx.lineWidth = size;
                ctx.stroke();
            });
        }

        // مسح اللوحة
        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            shapes = [];
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        }

        // تغيير اتجاه الصورة
        rotateBtn.addEventListener('click', () => {
            angle += 90;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.save();
            ctx.translate(canvas.width / 2, canvas.height / 2);
            ctx.rotate(angle * Math.PI / 180);
            ctx.drawImage(img, -img.width / 2, -img.height / 2, img.width, img.height);
            ctx.restore();
        });

        // إضافة التعليق بالنقر بزر الفأرة الأيمن
        canvas.addEventListener('contextmenu', (event) => {
            event.preventDefault();
            const comment = prompt("أدخل التعليق: ");
            if (comment) {
                const x = event.offsetX;
                const y = event.offsetY;

                ctx.font = '20px Arial';
                ctx.fillStyle = color;

                ctx.fillText(comment, x, y);
            }
        });

        // حفظ الصورة المعدلة
        saveImageBtn.addEventListener('click', () => {
            canvas.toBlob((blob) => {
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'modified-eye-image.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 'image/png');
        });
    }

    // إنشاء اللوحة الأولى
    createCanvas('eyeCanvas', 'color', 'size', 'newDrawing', 'undo', 'rotate', 'saveImage');

    // إنشاء اللوحة الثانية
    createCanvas('eye-canvas-follow', 'color-picker', 'brush-size', 'new-drawing', 'undo-drawing', 'rotate-image',
        'save-image');

    // حفظ البيانات مع الصورة
    document.getElementById('save-data').addEventListener('click', (event) => {
        event.preventDefault();

        var formData = new FormData(document.getElementById('form-follow-up-ophthalmology-assessment'));

        // إضافة الصورة إلى FormData
        const canvasFollow = document.getElementById('eye-canvas-follow');
        canvasFollow.toBlob((blob) => {
            formData.append('image_path', blob,
            'eye_image.png'); // تأكد من أن 'image_path' هو المفتاح الصحيح

            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/save_follow_ophthalmology_assessment',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
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
                    alert('حدث خطأ أثناء الحفظ.');
                }
            });
        }, 'image/png');
    });
</script>
