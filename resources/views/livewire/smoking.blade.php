<div class="card mb-4 border-info">
    <div class="card-header bg-info text-white">
        <i class="mdi mdi-smoking me-2"></i> Smoking
    </div>
    <div class="card-body">
        <!-- إدخال بيانات التدخين -->
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Smoking Status:</label>
                <select class="form-control" wire:model="smoking_status">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                    <option value="Occasional">Occasional</option>
                </select>
                @error('smoking_status') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Type:</label>
                <select class="form-control" wire:model="smoking_type">
                    <option value="Cigarettes">Cigarettes</option>
                    <option value="Shisha">Shisha</option>
                </select>
                @error('smoking_type') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Packs:</label>
                <input type="number" class="form-control" wire:model="smoking_packs" placeholder="Enter packs">
                @error('smoking_packs') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label class="form-label">Duration:</label>
                <input type="number" min="0" class="form-control" wire:model="smoking_duration" placeholder="Enter duration">
                @error('smoking_duration') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Duration Unit:</label>
                <select class="form-control" wire:model="smoking_duration_unit">
                    <option value="New">New</option>
                    <option value="M">Months</option>
                    <option value="Y">Years</option>
                </select>
                @error('smoking_duration_unit') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success" wire:click="save">
                <i class="mdi mdi-content-save me-2"></i> {{ $editingId ? 'Update' : 'Save' }}
            </button>
            <button class="btn btn-danger" wire:click="resetInput">
                <i class="mdi mdi-delete me-2"></i> Clear Form
            </button>
        </div>

        <hr>

        <!-- البحث داخل الجدول -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" class="form-control" wire:model="search" placeholder="Search by Smoking Status">
            </div>
        </div>

        <!-- عرض السجل -->
        <h5>History</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th wire:click="sortBy('smoking_status')">Status <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('smoking_type')">Type <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('smoking_packs')">Packs <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('smoking_duration')">Duration <span class="mdi mdi-arrow-up-down"></span></th>
                        <th wire:click="sortBy('created_at')">Created At <span class="mdi mdi-arrow-up-down"></span></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($smoking_records as $record)
                        <tr>
                            <td>{{ $record->smoking_status }}</td>
                            <td>{{ $record->smoking_type }}</td>
                            <td>{{ $record->smoking_packs }}</td>
                            <td>{{ $record->smoking_duration }} {{ $record->smoking_duration_unit }}</td>
                            <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" wire:click="edit({{ $record->id }})">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" wire:click="delete({{ $record->id }})">
                                    <i class="mdi mdi-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $smoking_records->links() }}
    </div>
</div>
