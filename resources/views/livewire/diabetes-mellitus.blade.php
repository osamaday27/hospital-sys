<div class="card mb-4 border-success">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0 text-white">
            <i class="mdi mdi-heart-pulse me-2"></i> Diabetes Mellitus (DM)
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Type:</label>
                <select class="form-control" wire:model="type">
                    <option value="I">Type I</option>
                    <option value="II">Type II</option>
                    <option value="Unknown">Unknown</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Controlled:</label>
                <select class="form-control" wire:model="controlled">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                    <option value="Unknown">Unknown</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Medication:</label>
                <select class="form-control" wire:model="medication">
                    <option value="Insulin">Insulin</option>
                    <option value="Oral">Oral</option>
                    <option value="Both">Both</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Hba1c:</label>
                <input type="number" step="0.1" min="0" max="20" class="form-control" wire:model="hba1c" placeholder="Enter Hba1c value">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Duration:</label>
                <input type="number" min="0" class="form-control" wire:model="duration" placeholder="Enter duration">
            </div>
            <div class="col-md-6">
                <label class="form-label">Duration Unit:</label>
                <select class="form-control" wire:model="duration_unit">
                    <option value="New">New</option>
                    <option value="M">Months</option>
                    <option value="Y">Years</option>
                </select>
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
                <input type="text" class="form-control" wire:model="search" placeholder="Search by Type, Medication, or Controlled">
            </div>
        </div>

        <!-- عرض السجل -->
        <h5>History</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th wire:click="sortBy('type')">Type <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('controlled')">Controlled <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('medication')">Medication <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('hba1c')">Hba1c <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('duration')">Duration <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('created_at')">Created At <span class="mdi mdi-arrow-up-down"></span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dm_records as $record)
                        <tr>
                            <td>{{ $record->type }}</td>
                            <td>{{ $record->controlled }}</td>
                            <td>{{ $record->medication }}</td>
                            <td>{{ $record->hba1c }}</td>
                            <td>{{ $record->duration }} {{ $record->duration_unit }}</td>
                            <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- التنقل بين الصفحات -->
        {{ $dm_records->links() }}
    </div>
</div>
