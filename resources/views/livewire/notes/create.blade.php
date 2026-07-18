{{-- resources/views/livewire/notes/create.blade.php --}}

<div>
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="save">

        <div class="mb-3">
            <label class="form-label">الملحوظة</label>
            <textarea wire:model.defer="note" class="form-control" rows="4"></textarea>
            @error('note') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>درجة الأهمية</label>
                <select wire:model="importance_level" class="form-select">
                    <option value="low">منخفضة</option>
                    <option value="medium">متوسطة</option>
                    <option value="high">مرتفعة</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>هل تتطلب تنفيذ؟</label>
                <input type="checkbox" wire:model="action_required">
            </div>

            <div class="col-md-4 mb-3">
                <label>تثبيت الملحوظة</label>
                <input type="checkbox" wire:model="is_pinned">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>المريض</label>
                <input type="number" wire:model="patient_id" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>الحجز</label>
                <input type="number" wire:model="reservation_id" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>الزيارة</label>
                <input type="number" wire:model="visit_id" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>الجهة المستهدفة (type)</label>
                <select wire:model="target_type" class="form-select">
                    <option value="">اختر</option>
                    <option value="App\Models\User">مستخدم</option>
                    <option value="App\Models\Doctor">طبيب</option>
                    <option value="App\Models\Clinic">عيادة</option>
                    <option value="system">النظام</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>معرف الجهة</label>
                <input type="number" wire:model="target_id" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>السياق (context_type)</label>
                <input type="text" wire:model="context_type" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>معرف السياق</label>
                <input type="number" wire:model="context_id" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>الموعد النهائي</label>
                <input type="date" wire:model="due_date" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>الحالة</label>
                <select wire:model="status" class="form-select">
                    <option value="new">جديدة</option>
                    <option value="done">تم التنفيذ</option>
                    <option value="ignored">تم التجاهل</option>
                    <option value="rejected">مرفوضة</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">💾 حفظ</button>
    </form>
</div>
