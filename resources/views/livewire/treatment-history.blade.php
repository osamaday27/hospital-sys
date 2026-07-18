<div class="card mb-3 border-info">
    <div class="card-header bg-info text-white">
        <i class="mdi mdi-taps me-2"></i> Treatment
    </div>
    <div class="card-body">
        <!-- إدخال بيانات العلاج -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Drug</label>
                <input type="text" class="form-control" wire:model="drug" placeholder="Enter Drug">
                @error('drug') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-2">
                <label class="form-label">Eye</label>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-outline-primary {{ $eye == 'OD' ? 'active' : '' }}" wire:click="$set('eye', 'OD')">OD</button>
                    <button type="button" class="btn btn-outline-primary {{ $eye == 'OU' ? 'active' : '' }}" wire:click="$set('eye', 'OU')">OU</button>
                    <button type="button" class="btn btn-outline-primary {{ $eye == 'OS' ? 'active' : '' }}" wire:click="$set('eye', 'OS')">OS</button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">For</label>
                <input type="number" class="form-control" wire:model="forNumber" placeholder="Enter Number">
                @error('forNumber') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-2">
                <label class="form-label">Period</label>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-outline-secondary {{ $period == 'D' ? 'active' : '' }}" wire:click="$set('period', 'D')">D</button>
                    <button type="button" class="btn btn-outline-secondary {{ $period == 'W' ? 'active' : '' }}" wire:click="$set('period', 'W')">W</button>
                    <button type="button" class="btn btn-outline-secondary {{ $period == 'M' ? 'active' : '' }}" wire:click="$set('period', 'M')">M</button>
                    <button type="button" class="btn btn-outline-secondary {{ $period == 'Y' ? 'active' : '' }}" wire:click="$set('period', 'Y')">Y</button>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Dose</label>
                <input type="text" class="form-control" wire:model="dose" placeholder="Enter Dose">
                @error('dose') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Remarks</label>
                <textarea class="form-control" wire:model="remarks" rows="2" placeholder="Enter Remarks"></textarea>
                @error('remarks') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-success" wire:click="save">
                <i class="mdi mdi-plus me-2"></i> Add Treatment
            </button>
            <button type="button" class="btn btn-danger" wire:click="resetInput">
                <i class="mdi mdi-delete me-2"></i> Clear Form
            </button>
        </div>

        <hr>

        <!-- البحث داخل الجدول -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" wire:model="search" placeholder="Search Treatment">
            </div>
        </div>

        <!-- عرض السجل العلاجي -->
        <h5>History</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th wire:click="sortBy('drug')">Drug <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('eye')">Eye <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('forNumber')">Duration <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('dose')">Dose <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('remarks')">Remarks <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('created_at')">Created At <span class="mdi mdi-arrow-up-down"></span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($treatments as $treatment)
                        <tr>
                            <td>{{ $treatment->drug }}</td>
                            <td>{{ $treatment->eye }}</td>
                            <td>{{ $treatment->forNumber }} {{ $treatment->period }}</td>
                            <td>{{ $treatment->dose }}</td>
                            <td>{{ $treatment->remarks }}</td>
                            <td>{{ $treatment->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- تنقل بين الصفحات -->
        {{ $treatments->links() }}
    </div>
</div>
