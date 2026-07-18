<div class="card-body">
    <h5 class="card-title mb-4">Old Glasses</h5>

    <div class="row">
        <!-- OD Section -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">OD (Right Eye)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- OD Sph --}}
                        <div class="col-md-6">
                            <label class="form-label">Sph. (Scale)*</label>
                            <input type="text" class="form-control" list="sph_list" wire:model="od_sph" placeholder="Enter Sph.">
                            <datalist id="sph_list">
                                @foreach ($sph_options as $option)
                                    <option value="{{ $option }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        {{-- OD Cyl --}}
                        <div class="col-md-6">
                            <label class="form-label">Cyl (Scale)*</label>
                            <input type="text" class="form-control" list="cyl_list" wire:model="od_cyl" placeholder="Enter Cyl.">
                            <datalist id="cyl_list">
                                @foreach ($cyl_options as $option)
                                    <option value="{{ $option }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        {{-- OD Axis --}}
                        <div class="col-md-6">
                            <label class="form-label">Axis Cyl (180 scale)*</label>
                            <input type="text" class="form-control" list="axis_list" wire:model="od_axis" placeholder="Enter Axis">
                            <datalist id="axis_list">
                                @foreach ($axis_options as $option)
                                    <option value="{{ $option }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        {{-- OD Near Add --}}
                        <div class="col-md-6">
                            <label class="form-label">Near Add. (Scale)#</label>
                            <input type="text" class="form-control" list="near_add_list" wire:model="od_near_add" placeholder="Enter Near Add.">
                            <datalist id="near_add_list">
                                @foreach ($near_add_options as $option)
                                    <option value="{{ $option }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        {{-- OD BCVA --}}
                        <div class="col-md-12">
                            <label class="form-label">BCVA</label>
                            <select class="form-select" wire:model="od_bcva">
                                <option value="">Select BCVA</option>
                                @foreach ($bcva_options as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- OD Comment --}}
                        <div class="col-md-12">
                            <label class="form-label">OD Comment</label>
                            <textarea class="form-control" wire:model="od_comment" rows="2" placeholder="Enter OD Comment"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OS Section -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">OS (Left Eye)</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- OS Sph --}}
                        <div class="col-md-6">
                            <label class="form-label">Sph. (Scale)*</label>
                            <input type="text" class="form-control" list="sph_list" wire:model="os_sph" placeholder="Enter Sph.">
                        </div>

                        {{-- OS Cyl --}}
                        <div class="col-md-6">
                            <label class="form-label">Cyl (Scale)*</label>
                            <input type="text" class="form-control" list="cyl_list" wire:model="os_cyl" placeholder="Enter Cyl.">
                        </div>

                        {{-- OS Axis --}}
                        <div class="col-md-6">
                            <label class="form-label">Axis Cyl (180 scale)*</label>
                            <input type="text" class="form-control" list="axis_list" wire:model="os_axis" placeholder="Enter Axis">
                        </div>

                        {{-- OS Near Add --}}
                        <div class="col-md-6">
                            <label class="form-label">Near Add. (Scale)#</label>
                            <input type="text" class="form-control" list="near_add_list" wire:model="os_near_add" placeholder="Enter Near Add.">
                        </div>

                        {{-- OS BCVA --}}
                        <div class="col-md-12">
                            <label class="form-label">BCVA</label>
                            <select class="form-select" wire:model="os_bcva">
                                <option value="">Select BCVA</option>
                                @foreach ($bcva_options as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- OS Comment --}}
                        <div class="col-md-12">
                            <label class="form-label">OS Comment</label>
                            <textarea class="form-control" wire:model="os_comment" rows="2" placeholder="Enter OS Comment"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- أزرار الحفظ والتعديل والتنظيف -->
    <div class="d-flex justify-content-between">
        @if ($editingId)
            <button class="btn btn-warning" wire:click="save">Update</button>
        @else
            <button class="btn btn-success" wire:click="save">Save</button>
        @endif
        <button class="btn btn-danger" wire:click="resetInput">Clear</button>
    </div>

    <hr>

    <!-- جدول البيانات -->
    <h5>History</h5>
    <table class="table table-bordered">
        <thead class="bg-light">
            <tr>
                <th>OD Sph</th>
                <th>OD Cyl</th>
                <th>OD Axis</th>
                <th>OD Near Add</th>
                <th>OD BCVA</th>
                <th>OD Comment</th>
                <th>OS Sph</th>
                <th>OS Cyl</th>
                <th>OS Axis</th>
                <th>OS Near Add</th>
                <th>OS BCVA</th>
                <th>OS Comment</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($glasses_records as $record)
                <tr>
                    <td>{{ $record->od_sph }}</td>
                    <td>{{ $record->od_cyl }}</td>
                    <td>{{ $record->od_axis }}</td>
                    <td>{{ $record->od_near_add }}</td>
                    <td>{{ $record->od_bcva }}</td>
                    <td>{{ $record->od_comment }}</td>
                    <td>{{ $record->os_sph }}</td>
                    <td>{{ $record->os_cyl }}</td>
                    <td>{{ $record->os_axis }}</td>
                    <td>{{ $record->os_near_add }}</td>
                    <td>{{ $record->os_bcva }}</td>
                    <td>{{ $record->os_comment }}</td>
                    <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" wire:click="edit({{ $record->id }})">Edit</button>
                        <button class="btn btn-danger btn-sm" wire:click="delete({{ $record->id }})">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $glasses_records->links() }}
</div>
