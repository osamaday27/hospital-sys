<div class="card-body">
    <div class="row mb-3">
        <div class="col-md-8 position-relative">
            <label class="form-label">Diagnosis</label>
            <input type="text" class="form-control" wire:model.debounce.500ms="diagnosis"
                wire:keydown.escape="suggestions = []" wire:keydown.tab="suggestions = []"
                placeholder="Enter diagnosis">

            @if (!empty($suggestions))
                <ul class="list-group position-absolute w-100 shadow" style="z-index: 1050;">
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

        <div class="col-md-6">
            <label class="form-label" style="padding-top: 18px;">Eye</label>
            <div class="btn-group w-100">
                <button type="button" class="btn btn-outline-primary {{ $eye == 'OD' ? 'active' : '' }}"
                    wire:click="$set('eye', 'OD')">OD</button>
                <button type="button" class="btn btn-outline-primary {{ $eye == 'OU' ? 'active' : '' }}"
                    wire:click="$set('eye', 'OU')">OU</button>
                <button type="button" class="btn btn-outline-primary {{ $eye == 'OS' ? 'active' : '' }}"
                    wire:click="$set('eye', 'OS')">OS</button>
            </div>
        </div>

        {{-- ✅ Flag: initial / final --}}
        <div class="col-md-6 mt-3">
                        <label class="form-label">initial / final</label>

            <div class="btn-group w-100">
                <button type="button" class="btn btn-outline-dark {{ $diag_flag == 'initial' ? 'active' : '' }}"
                    wire:click="$set('diag_flag', 'initial')">Initial</button>
                <button type="button" class="btn btn-outline-dark {{ $diag_flag == 'final' ? 'active' : '' }}"
                    wire:click="$set('diag_flag', 'final')">Final</button>
            </div>
            @error('diag_flag')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-12">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" wire:model="remarks" rows="3"></textarea>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-success" wire:click="save">{{ $editingId ? 'Update' : 'Add' }} Diagnosis</button>
        <button class="btn btn-danger" wire:click="resetInput">Clear</button>
    </div>

    <hr>

    <!-- 🔍 البحث داخل الجدول -->
    <div class="mb-3">
        <input type="text" class="form-control" wire:model="search" placeholder="🔍 Search diagnosis...">
    </div>

    <h5>Diagnosis Records</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th wire:click="sortBy('diagnosis')" style="cursor: pointer;">Diagnosis 🔽</th>
                    <th>Eye</th>
                    {{-- ✅ عمود الـ Flag مع ترتيب --}}
                    <th wire:click="sortBy('diag_flag')" style="cursor: pointer;">Flag</th>
                    <th>Remarks</th>
                    <th wire:click="sortBy('created_at')" style="cursor: pointer;">Created At 📅</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($diagnoses as $item)
                    <tr ondblclick="Livewire.emit('edit', {{ $item->id }})">
                        <td>{{ $item->diagnosis }}</td>
                        <td>{{ $item->eye }}</td>
                        {{-- ✅ عرض قيمة الـ Flag (Initial/Final) --}}
                        <td>{{ ucfirst($item->diag_flag ?? 'initial') }}</td>
                        <td>{{ $item->remarks }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" wire:click="edit({{ $item->id }})">Edit</button>
                            <button class="btn btn-danger btn-sm" wire:click="delete({{ $item->id }})">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $diagnoses->links() }}
</div>
