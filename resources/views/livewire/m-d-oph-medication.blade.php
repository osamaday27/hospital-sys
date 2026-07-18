<div class="card mb-4 border-warning">
    <div class="card-header bg-warning text-white">
        <i class="mdi mdi-pill me-2"></i> Medication
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Anticoag/AntiPlat:</label>
                <select class="form-control" wire:model="anticoag_antiplat">
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Details:</label>
                <input type="text" class="form-control" wire:model="medication_details" placeholder="Enter medication details">
            </div>
        </div>

        <button class="btn btn-success mt-3" wire:click="save">Save</button>
        <hr>

        <h5>History</h5>
        <input type="text" wire:model="search" class="form-control mb-2" placeholder="Search by Medication Details">

        <table class="table">
            <thead>
                <tr>
                    <th wire:click="sortBy('anticoag_antiplat')">Anticoag/AntiPlat</th>
                    <th wire:click="sortBy('medication_details')">Details</th>
                    <th wire:click="sortBy('created_at')">Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medication_records as $record)
                    <tr>
                        <td>{{ $record->anticoag_antiplat }}</td>
                        <td>{{ $record->medication_details }}</td>
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

        {{ $medication_records->links() }}
    </div>
</div>
