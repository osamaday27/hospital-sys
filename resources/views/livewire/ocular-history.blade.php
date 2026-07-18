<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <i class="mdi mdi-eye-outline me-2"></i> Ocular History
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 position-relative">
                <label class="form-label">Disease:</label>
                <input type="text" class="form-control" wire:model.debounce.500ms="ocular_disease"
                    wire:keydown.escape="suggestions_disease = []" wire:keydown.tab="suggestions_disease = []"
                    placeholder="Enter disease">

                @if (!empty($suggestions_disease))
                    <ul class="list-group position-absolute w-100 shadow" style="z-index: 1050;">
                        @foreach ($suggestions_disease as $suggestion)
                            <li class="list-group-item list-group-item-action"
                                wire:click="selectDisease('{{ $suggestion }}')">
                                {{ $suggestion }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                @error('ocular_disease')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label">Relationship:</label>
                <input type="text" class="form-control" wire:model="ocular_relationship"
                    placeholder="Enter relationship">
            </div>
        </div>

        <div class="col-md-12 mt-2">
            <label class="form-label">Remarks:</label>
            <textarea class="form-control" wire:model="ocular_remark" rows="3" placeholder="Enter remarks"></textarea>
        </div>

        <button class="btn btn-success mt-3" wire:click="save">Save</button>
        <hr>

        <h5>History</h5>
        <input type="text" wire:model="search" class="form-control mb-2" placeholder="Search by Disease">

        <table class="table">
            <thead>
                <tr>
                    <th wire:click="sortBy('ocular_disease')">Disease</th>
                    <th wire:click="sortBy('ocular_relationship')">Relationship</th>
                    <th wire:click="sortBy('ocular_remark')">Remark</th>
                    <th wire:click="sortBy('created_at')">Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ocular_records as $record)
                    <tr>
                        <td>{{ $record->ocular_disease }}</td>
                        <td>{{ $record->ocular_relationship }}</td>
                        <td>{{ $record->ocular_remark }}</td>
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

        {{ $ocular_records->links() }}
    </div>
</div>
