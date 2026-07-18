<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white text-center">
        <h5 class="mb-0">Extraocular Movement (EOM) - Eye Examination</h5>
    </div>

    <div class="card-body">
        <div class="row g-4">

            <!-- ✅ Right Eye -->
            <div class="col-md-6 text-center">
                <h6 class="text-primary">Right Eye - EOM</h6>
                <div class="position-relative d-inline-block" id="rightEyeContainer" style="width: 350px;">
                    <img src="{{ asset('assets/images/OPTH/eom_rt.jpg') }}" class="img-fluid rounded shadow-sm"
                         alt="EOM Right Eye" style="height: 251px; width: 347px;">

                    <!-- ✅ Input Fields -->
                    <input type="text" class="eom-input" wire:model.defer="right_eom.SR" style="top: 10%; left: 36%;" list="eom_list">
                    <input type="text" class="eom-input" wire:model.defer="right_eom.IO" style="top: 11%; left: 64%;" list="eom_list">
                    <input type="text" class="eom-input" wire:model.defer="right_eom.IR" style="top: 93%; left: 35%;" list="eom_list">
                    <input type="text" class="eom-input" wire:model.defer="right_eom.SO" style="top: 93%; left: 65%;" list="eom_list">
                    <input type="text" class="eom-input" wire:model.defer="right_eom.LR" style="top: 51%; left: 9%;" list="eom_list">
                    <input type="text" class="eom-input" wire:model.defer="right_eom.MR" style="top: 52%; left: 92%;" list="eom_list">
                </div>

                <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
                    <button class="btn btn-secondary" onclick="copyToLeftEOM()">Copy to Left</button>
                    <button wire:click="clearEOM('right')" class="btn btn-danger">Clear Right</button>
                    <button class="btn btn-info" onclick="downloadImage('rightEyeContainer', 'EOM_Right.png')">Download</button>
                </div>
            </div>

            <!-- ✅ Left Eye -->
            <div class="col-md-6 text-center">
                <h6 class="text-primary">Left Eye - EOM</h6>
                <div class="position-relative d-inline-block" id="leftEyeContainer" style="width: 350px;">
                    <img src="{{ asset('assets/images/OPTH/eom_lt.jpg') }}" class="img-fluid rounded shadow-sm"
                         alt="EOM Left Eye" style="height: 251px; width: 347px;">

                    <!-- ✅ Input Fields -->
                    <input type="text" class="eom-input" wire:model.defer="left_eom.SR" style="top: 5%; right: 22%;" list="eom_list">
                    <input type="text" class="eom-input" wire:model.defer="left_eom.IO" style="top: 5%; left: 37%;" list="eom_list">
                    <input type="text" class="eom-input" wire:model.defer="left_eom.IR" style="bottom: -5%; right: 21%;" list="eom_list">
                    <input type="text" class="eom-input" wire:model.defer="left_eom.SO" style="bottom: -5%; left: 34%;" list="eom_list">
                    <input type="text" class="eom-input" wire:model.defer="left_eom.MR" style="top: 50%; left: 8%;" list="eom_list">
                    <input type="text" class="eom-input" wire:model.defer="left_eom.LR" style="top: 49%; right: -6%;" list="eom_list">
                </div>

                <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
                    <button class="btn btn-secondary" onclick="copyToRightEOM()">Copy to Right</button>
                    <button wire:click="clearEOM('left')" class="btn btn-danger">Clear Left</button>
                    <button class="btn btn-info" onclick="downloadImage('leftEyeContainer', 'EOM_Left.png')">Download</button>
                </div>
            </div>

            <!-- ✅ Uploaded Image Preview -->
            <div class="col-md-12 text-center mt-4">
                <label class="form-label fw-bold">Upload External EOM Image</label>
                <input type="file" wire:model="uploaded_eom_image" accept="image/*" class="form-control w-50 mx-auto mb-2">
                @if ($uploaded_eom_image)
                    <div class="mt-2">
                        <img src="{{ $uploaded_eom_image->temporaryUrl() }}" class="img-thumbnail shadow" style="max-height: 200px;">
                    </div>
                @endif
                @error('uploaded_eom_image') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <!-- ✅ Footer Buttons -->
            <div class="col-12 text-center mt-4">
                <button class="btn btn-info me-2" onclick="downloadBothImages()">Download </button>
                <button class="btn btn-success" onclick="captureBothEyesAndSave()">حفظ البيانات والصورتين</button>
            </div>
        </div>
    </div>
</div>

<datalist id="eom_list">
    @foreach ($eom_options as $option)
        <option value="{{ $option }}"></option>
    @endforeach
</datalist>

<style>
    .eom-input {
        position: absolute;
        width: 50px;
        height: 30px;
        font-size: 14px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transform: translate(-50%, -50%);
    }
</style>
