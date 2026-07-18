<div class="card mb-4 border-info">
    <div class="card-header bg-info text-white">
        <i class="mdi mdi-heart me-2"></i> Hypertension (HTN)
    </div>
    <div class="card-body">
        <!-- إدخال البيانات -->
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Controlled:</label>
                <select class="form-control" wire:model="controlled">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                    <option value="Unknown">Unknown</option>
                </select>
                @error('controlled') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Duration:</label>
                <input type="number" min="0" class="form-control" wire:model="duration" placeholder="Enter duration">
                @error('duration') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">Duration Unit:</label>
                <select class="form-control" wire:model="duration_unit">
                    <option value="New">New</option>
                    <option value="M">Months</option>
                    <option value="Y">Years</option>
                </select>
                @error('duration_unit') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success" wire:click="save">
                <i class="mdi mdi-plus me-2"></i> Save
            </button>
            <button class="btn btn-danger" wire:click="resetInput">
                <i class="mdi mdi-delete me-2"></i> Clear Form
            </button>
        </div>

        <hr>

        <!-- البحث داخل الجدول -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" wire:model="search" placeholder="Search by Controlled Status">
            </div>
        </div>

        <!-- عرض السجل -->
        <h5>History</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th wire:click="sortBy('controlled')">Controlled <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('duration')">Duration <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('created_at')">Created At <span class="mdi mdi-arrow-up-down"></span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($htn_records as $record)
                        <tr>
                            <td>{{ $record->controlled }}</td>
                            <td>{{ $record->duration }} {{ $record->duration_unit }}</td>
                            <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $htn_records->links() }}
    </div>
</div>
