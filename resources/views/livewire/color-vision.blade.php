<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title mb-4 text-primary">
            <i class="mdi mdi-eye-outline me-2"></i> Color Vision
        </h5>

        <div class="row">
            <!-- ✅ OD (Right Eye) -->
            <div class="col-md-6">
                <h6 class="text-primary">OD (Right Eye)</h6>
                <div class="btn-group w-100 mb-2">
                    <button type="button"
                        class="btn btn-outline-danger {{ in_array('washed-red', $od_colors) ? 'active' : '' }}"
                        wire:click="toggleColor('od_colors', 'washed-red')">Washed Red</button>
                    <button type="button"
                        class="btn btn-outline-dark {{ in_array('dark-red', $od_colors) ? 'active' : '' }}"
                        wire:click="toggleColor('od_colors', 'dark-red')">Dark Red</button>
                    <button type="button"
                        class="btn btn-outline-warning {{ in_array('blue-yellow', $od_colors) ? 'active' : '' }}"
                        wire:click="toggleColor('od_colors', 'blue-yellow')">Blue-Yellow</button>
                    <button type="button"
                        class="btn btn-outline-success {{ in_array('red-green', $od_colors) ? 'active' : '' }}"
                        wire:click="toggleColor('od_colors', 'red-green')">Red-Green</button>
                </div>
                <label class="form-label">Comment</label>
                <textarea class="form-control mb-2" wire:model="od_comment" rows="2" placeholder="OD Comment"></textarea>
            </div>

            <!-- ✅ OS (Left Eye) -->
            <div class="col-md-6">
                <h6 class="text-danger">OS (Left Eye)</h6>
                <div class="btn-group w-100 mb-2">
                    <button type="button"
                        class="btn btn-outline-danger {{ in_array('washed-red', $os_colors) ? 'active' : '' }}"
                        wire:click="toggleColor('os_colors', 'washed-red')">Washed Red</button>
                    <button type="button"
                        class="btn btn-outline-dark {{ in_array('dark-red', $os_colors) ? 'active' : '' }}"
                        wire:click="toggleColor('os_colors', 'dark-red')">Dark Red</button>
                    <button type="button"
                        class="btn btn-outline-warning {{ in_array('blue-yellow', $os_colors) ? 'active' : '' }}"
                        wire:click="toggleColor('os_colors', 'blue-yellow')">Blue-Yellow</button>
                    <button type="button"
                        class="btn btn-outline-success {{ in_array('red-green', $os_colors) ? 'active' : '' }}"
                        wire:click="toggleColor('os_colors', 'red-green')">Red-Green</button>
                </div>
                <label class="form-label">Comment</label>
                <textarea class="form-control mb-2" wire:model="os_comment" rows="2" placeholder="OS Comment"></textarea>
            </div>
        </div>

        <!-- ✅ الأزرار -->
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success" wire:click="save"><i class="mdi mdi-content-save"></i> Save</button>
            <button class="btn btn-danger" wire:click="resetInput"><i class="mdi mdi-delete"></i> Clear</button>
        </div>

        <hr>

        <!-- ✅ جدول عرض البيانات -->
        <h5 class="mt-4">Color Vision History</h5>
        <input type="text" wire:model="search" class="form-control mb-2" placeholder="Search">

        <table class="table table-bordered table-hover">
            <thead class="bg-light">
                <tr>
                    <th>OD Colors</th>
                    <th>OS Colors</th>
                    <th>OD Comment</th>
                    <th>OS Comment</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>
                            @foreach (json_decode($record->od_colors, true) as $color)
                                <span class="badge"
                                    style="background-color: {{ $getColor($color) }}">{{ ucfirst($color) }}</span>
                            @endforeach
                        </td>
                        <td>
                            @foreach (json_decode($record->os_colors, true) as $color)
                                <span class="badge"
                                    style="background-color: {{ $getColor($color) }}">{{ ucfirst($color) }}</span>
                            @endforeach
                        </td>

                        <td>{{ $record->od_comment }}</td>
                        <td>{{ $record->os_comment }}</td>
                        <td>{{ $record->user->name ?? 'Unknown' }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" wire:click="edit({{ $record->id }})">
                                <i class="mdi mdi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $record->id }})">
                                <i class="mdi mdi-delete"></i> Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
