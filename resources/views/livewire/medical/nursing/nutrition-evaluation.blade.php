{{-- resources/views/livewire/medical/nursing/nutritional‐screening.blade.php --}}

<div>
    {{-- رسالة النجاح --}}
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form @if($selectedRecord) wire:submit.prevent="update" @else wire:submit.prevent="save" @endif>
        {{-- قسم نوع الغذاء بالمنزل --}}
        <div class="card card-section mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-utensils mr-2"></i>نوع الغذاء بالمنزل</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered text-center nutrition-table mb-0">
                    <thead>
                        <tr>
                            <th>طبيعة أكل خاصة</th>
                            <th colspan="3">السكر</th>
                            <th colspan="3">الملح</th>
                            <th colspan="3">البروتين</th>
                            <th colspan="3">الدهون</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>عالي</th><th>قليل</th><th>طبيعي</th>
                            <th>عالي</th><th>قليل</th><th>طبيعي</th>
                            <th>عالي</th><th>قليل</th><th>طبيعي</th>
                            <th>عالي</th><th>قليل</th><th>طبيعي</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" wire:model.defer="special_diet" class="form-control form-control-custom">
                                @error('special_diet') <span class="text-danger">{{ $message }}</span> @enderror
                            </td>
                            <td><input type="checkbox" wire:model.defer="sugar_high" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="sugar_low" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="sugar_normal" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="salt_high" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="salt_low" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="salt_normal" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="protein_high" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="protein_low" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="protein_normal" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="fat_high" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="fat_low" class="form-check-input"></td>
                            <td><input type="checkbox" wire:model.defer="fat_normal" class="form-check-input"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- قسم تقييمات متعددة --}}
        <div class="card card-section mb-4">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-utensils mr-2"></i>التقييمات التغذوية والفموية والسنّية</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>تقييم التغذية:</label><br>
                        <label class="me-3"><input type="checkbox" wire:model.defer="nutrition_normal"> طبيعي</label>
                        <label class="me-3"><input type="checkbox" wire:model.defer="nutrition_nausea"> غثيان</label>
                        <label class="me-3"><input type="checkbox" wire:model.defer="nutrition_vomiting"> قيء</label>
                        <label class="me-3"><input type="checkbox" wire:model.defer="nutrition_weight_loss"> فقدان الوزن واضح</label>
                        <label><input type="checkbox" wire:model.defer="nutrition_dysphagia"> صعوبة بالبلع</label>
                    </div>
                    <div class="col-md-6">
                        <label>تقييم الفم:</label><br>
                        <label class="me-3"><input type="checkbox" wire:model.defer="mouth_normal"> طبيعي</label>
                        <label class="me-3"><input type="checkbox" wire:model.defer="mouth_dry"> جاف</label>
                        <label><input type="checkbox" wire:model.defer="mouth_inflammation"> يوجد التهاب</label>
                    </div>
                    <div class="col-md-6">
                        <label>الأسنان:</label><br>
                        <label class="me-3"><input type="checkbox" wire:model.defer="teeth_normal"> طبيعي</label>
                        <label class="me-3"><input type="checkbox" wire:model.defer="teeth_loss"> فقد الأسنان</label>
                        <label><input type="checkbox" wire:model.defer="teeth_denture"> تركيبات متحركة</label>
                    </div>
                    <div class="col-md-6">
                        <label>السيدات:</label><br>
                        <label class="me-3"><input type="checkbox" wire:model.defer="women_na"> NA</label>
                        <label class="me-3"><input type="checkbox" wire:model.defer="pregnant"> حامل</label>
                        <label><input type="checkbox" wire:model.defer="breastfeeding"> ترضع</label>
                    </div>
                    <div class="col-md-6">
                        <label>كبار السن:</label><br>
                        <label class="me-3"><input type="checkbox" wire:model.defer="elderly_na"> NA</label>
                        <label><input type="checkbox" wire:model.defer="elderly_over70"> أكبر من 70 سنة</label>
                    </div>
                    <div class="col-md-6">
                        <label>دليل التقييم:</label><br>
                        <label class="me-3"><input type="checkbox" wire:model.defer="risk_low"> عديم الخطورة (0–2 مخاطر)</label>
                        <label><input type="checkbox" wire:model.defer="risk_high"> عالي الخطورة (>2 مخاطر)</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- قسم التنبيه --}}
        <div class="card card-section mb-4">
            <div class="card-body">
                <label>يجب تنبيه الطبيب عند اختيار علامة <span class="text-danger">*</span></label><br>
                <label class="me-3"><input type="checkbox" wire:model.defer="notify_doctor_yes"> نعم</label>
                <label><input type="checkbox" wire:model.defer="notify_doctor_no"> لا</label>
            </div>
        </div>

        <div class="text-center mb-5">
            <button type="submit" class="btn btn-primary px-4">{{ $selectedRecord ? 'تحديث' : 'حفظ' }}</button>
            @if($selectedRecord)
                <button type="button" wire:click.prevent="cancelEdit" class="btn btn-secondary px-4">إلغاء</button>
            @endif
        </div>
    </form>

    {{-- سجل السجلات السابقة --}}
    <div class="card mt-5">
        <div class="card-header bg-secondary text-white">سجل الفحص التغذوي</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>التاريخ</th><th>المستخدم</th><th>طبيعة الأكل</th><th>تغذية</th><th>فم</th><th>أسنان</th>
                        <th>سيدات</th><th>كبار سن</th><th>مخاطر</th><th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $r)
                        <tr>
                            <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($r->user)->name }}</td>
                            <td>{{ $r->special_diet }}</td>
                            <td>
                                {{ $r->nutrition_normal?'طبيعي':'' }}
                                {{ $r->nutrition_nausea?' غثيان':'' }}
                                {{ $r->nutrition_vomiting?' قيء':'' }}
                                {{ $r->nutrition_weight_loss?' فقدان وزن':'' }}
                                {{ $r->nutrition_dysphagia?' صعوبة بلع':'' }}
                            </td>
                            <td>
                                {{ $r->mouth_normal?'طبيعي':'' }}
                                {{ $r->mouth_dry?' جاف':'' }}
                                {{ $r->mouth_inflammation?' التهاب':'' }}
                            </td>
                            <td>
                                {{ $r->teeth_normal?'طبيعي':'' }}
                                {{ $r->teeth_loss?' فقد أسنان':'' }}
                                {{ $r->teeth_denture?' تركيبات':'' }}
                            </td>
                            <td>
                                {{ $r->women_na?'NA':'' }}
                                {{ $r->pregnant?' حامل':'' }}
                                {{ $r->breastfeeding?' ترضع':'' }}
                            </td>
                            <td>
                                {{ $r->elderly_na?'NA':'' }}
                                {{ $r->elderly_over70?' >70':'' }}
                            </td>
                            <td>
                                {{ $r->risk_low?'منخفض':'' }}
                                {{ $r->risk_high?' عالي':'' }}
                            </td>
                            <td class="text-center">
                                <button wire:click="viewRecord({{ $r->id }})" class="btn btn-sm btn-info">عرض</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center">لا توجد سجلات</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-end mt-3 pe-3">
                {{ $records->links('livewire::bootstrap') }}
            </div>
        </div>
    </div>
</div>
