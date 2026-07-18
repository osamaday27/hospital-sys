    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-6 position-relative">
                <label class="form-label">Complaints</label>
                <input type="text" class="form-control" wire:model.debounce.500ms="complaints"
                    wire:keydown.escape="suggestions = []" wire:keydown.tab="suggestions = []"
                    placeholder="Enter complaints">

                @if (!empty($suggestions))
                    <ul class="list-group position-absolute w-100 shadow" style="z-index: 1050;">
                        @foreach ($suggestions as $suggestion)
                            <li class="list-group-item list-group-item-action"
                                wire:click="selectComplaint('{{ $suggestion }}')">
                                {{ $suggestion }}
                            </li>
                        @endforeach
                    </ul>
                @endif
                @error('complaints')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="form-label">Eye</label>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-outline-primary {{ $eye == 'OD' ? 'active' : '' }}"
                        wire:click="$set('eye', 'OD')">OD</button>
                    <button type="button" class="btn btn-outline-primary {{ $eye == 'OU' ? 'active' : '' }}"
                        wire:click="$set('eye', 'OU')">OU</button>
                    <button type="button" class="btn btn-outline-primary {{ $eye == 'OS' ? 'active' : '' }}"
                        wire:click="$set('eye', 'OS')">OS</button>
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label">Duration</label>
                <input type="number" class="form-control" wire:model="for_number" min="1">
            </div>

            <div class="col-md-2">
                <label class="form-label">Period</label>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-outline-secondary {{ $for_period == 'D' ? 'active' : '' }}"
                        wire:click="$set('for_period', 'D')">D</button>
                    <button type="button" class="btn btn-outline-secondary {{ $for_period == 'W' ? 'active' : '' }}"
                        wire:click="$set('for_period', 'W')">W</button>
                    <button type="button" class="btn btn-outline-secondary {{ $for_period == 'M' ? 'active' : '' }}"
                        wire:click="$set('for_period', 'M')">M</button>
                    <button type="button" class="btn btn-outline-secondary {{ $for_period == 'Y' ? 'active' : '' }}"
                        wire:click="$set('for_period', 'Y')">Y</button>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Complain Date</label>
                <input type="date" class="form-control" wire:model="complan_date" />
            </div>
             <div class="col-md-4">
            <label class="form-label">Onset</label>
            <input type="text" class="form-control" wire:model="onset" list="onset-list">
            <datalist id="onset-list">
                @foreach ($onsetOptions as $option)
                    <option value="{{ $option }}"></option>
                @endforeach
            </datalist>
        </div>
        <div class="col-md-4">
            <label class="form-label">Course</label>
            <input type="text" class="form-control" wire:model="course" list="course-list">
            <datalist id="course-list">
                @foreach ($courseOptions as $option)
                    <option value="{{ $option }}"></option>
                @endforeach
            </datalist>
        </div>
        <div class="col-md-12">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" wire:model="remarks" rows="3"></textarea>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <button class="btn btn-success" wire:click="save">{{ $editingId ? 'Update' : 'Add' }} Complaint</button>
        <button class="btn btn-danger" wire:click="resetInput">Clear</button>
    </div>

    <hr>

    <!-- 🔍 البحث داخل الجدول -->
    <div class="mb-3">
        <input type="text" class="form-control" wire:model="search" placeholder="🔍 Search complaints...">
    </div>

    <h5>Complaint Records</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th wire:click="sortBy('complaints')" style="cursor: pointer;">Complaint 🔽</th>
                    <th>Eye</th>
                    <th>Duration</th>
                    <th>Complain Date</th>
                    <th>Onset</th>
                    <th>Course</th>
                    <th>Remarks</th>
                    <th wire:click="sortBy('created_at')" style="cursor: pointer;">Created At 📅</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($histories as $history)
                    <tr ondblclick="Livewire.emit('edit', {{ $history->id }})">
                        <td>{{ $history->complaints }}</td>
                        <td>{{ $history->eye }}</td>
                        <td>{{ $history->duration }} </td>
                        <td>{{ $history->complan_date }}</td>
                        <td>{{ $history->onset }}</td>
                        <td>{{ $history->course }}</td>
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
    </div>

    {{ $histories->links() }}
    </div>
