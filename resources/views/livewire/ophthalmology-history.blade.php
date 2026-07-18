<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="mb-3">Ophthalmology History</h5>

        <div class="row mb-3">
            <div class="col-md-6 position-relative">
                <label class="form-label">Diagnosis</label>
                <input type="text" class="form-control" wire:model.debounce.500ms="diagnosis"
                    wire:keydown.escape="suggestions = []" wire:keydown.tab="suggestions = []"
                    placeholder="Enter ICD code or diagnosis name">

                @if (!empty($suggestions))
                    <ul class="list-group position-absolute w-100 shadow"
                        style="z-index: 1050; max-height: 200px; overflow-y: auto;">
                        @foreach ($suggestions as $suggestion)
                            <li class="list-group-item list-group-item-action"
                                wire:click="selectDiagnosis('{{ $suggestion }}')">
                                {{ $suggestion }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                @error('diagnosis')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="col-md-2">
                <label class="form-label">Eye</label>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-outline-primary {{ $eye === 'OD' ? 'active' : '' }}"
                        wire:click="$set('eye', 'OD')">OD</button>
                    <button type="button" class="btn btn-outline-primary {{ $eye === 'OU' ? 'active' : '' }}"
                        wire:click="$set('eye', 'OU')">OU</button>
                    <button type="button" class="btn btn-outline-primary {{ $eye === 'OS' ? 'active' : '' }}"
                        wire:click="$set('eye', 'OS')">OS</button>
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label">Duration</label>
                <input type="number" class="form-control" wire:model.defer="for_number" placeholder="Enter Duration">
            </div>

            <div class="col-md-2">
                <label class="form-label">Period</label>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-outline-secondary {{ $for_period === 'D' ? 'active' : '' }}"
                        wire:click="$set('for_period', 'D')">D</button>
                    <button type="button" class="btn btn-outline-secondary {{ $for_period === 'W' ? 'active' : '' }}"
                        wire:click="$set('for_period', 'W')">W</button>
                    <button type="button" class="btn btn-outline-secondary {{ $for_period === 'M' ? 'active' : '' }}"
                        wire:click="$set('for_period', 'M')">M</button>
                    <button type="button" class="btn btn-outline-secondary {{ $for_period === 'Y' ? 'active' : '' }}"
                        wire:click="$set('for_period', 'Y')">Y</button>
                </div>
            </div>
            <div class="col-md-12">
                <label class="form-label">Remarks</label>
                <textarea class="form-control" wire:model="remarks" rows="3"></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button class="btn btn-success" wire:click="save">{{ $editingId ? 'Update' : 'Add' }} History</button>
            <button class="btn btn-danger" wire:click="resetInput">Clear</button>
        </div>

        <hr>
        <h5>History Records</h5>
        <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>Diagnosis</th>
                    <th>Eye</th>
                    <th>Duration</th>
                    <th>Remarks</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($histories as $history)
                    <tr>
                        <td>{{ $history->diseases }}</td>
                        <td>{{ $history->eye }}</td>
                        <td>{{ $history->for_number }} {{ $history->for_period }}</td>
                        <td>{{ $history->remarks }}</td>
                        <td>{{ $history->created_at }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" wire:click="edit({{ $history->id }})">Edit</button>
                            <button class="btn btn-danger btn-sm"
                                wire:click="delete({{ $history->id }})">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $histories->links() }}
    </div>
</div>
