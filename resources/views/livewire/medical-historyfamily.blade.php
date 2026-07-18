<div class="card mb-4 border-success">
    <div class="card-header bg-success text-white">
        <i class="mdi mdi-heart-pulse me-2"></i> medical History
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Disease:</label>
                <input type="text" class="form-control" wire:model="medical_disease" placeholder="Enter disease">
            </div>
            <div class="col-md-6">
                <label class="form-label">Relationship:</label>
                <input type="text" class="form-control" wire:model="medical_relationship"
                    placeholder="Enter relationship">
            </div>
        </div>

        <div class="col-md-12 mt-2">
            <label class="form-label">Remarks:</label>
            <textarea class="form-control" wire:model="medical_remark" rows="3" placeholder="Enter remarks"></textarea>
        </div>

        <button class="btn btn-success mt-3" wire:click="save">Save</button>
        <hr>

        <h5>History</h5>
        <input type="text" wire:model="search" class="form-control mb-2" placeholder="Search by Disease">

        <table class="table">
            <thead>
                <tr>
                    <th wire:click="sortBy('medical_disease')">Disease</th>
                    <th wire:click="sortBy('medical_relationship')">Relationship</th>
                    <th wire:click="sortBy('medical_remark')">Remark</th>
                    <th wire:click="sortBy('created_at')">Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medical_records as $record)
                    <tr>
                        <td>{{ $record->medical_disease }}</td>
                        <td>{{ $record->medical_relationship }}</td>
                        <td>{{ $record->medical_remark }}</td>
                        <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" wire:click="edit({{ $record->id }})">Edit</button>
                            <button class="btn btn-danger btn-sm"
                                wire:click="delete({{ $record->id }})">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $medical_records->links() }}
    </div>
</div>
