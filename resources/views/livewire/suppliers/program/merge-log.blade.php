<div dir="rtl">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="text-white"><i class="mdi mdi-history"></i> {{ __('suppliers.merge_log') }}</h5>
        </div>
        <div class="card-body">
            @if(session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-striped text-sm">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('suppliers.main_supplier') }}</th>
                        <th>{{ __('suppliers.merged_supplier') }}</th>
                        <th>{{ __('suppliers.user') }}</th>
                        <th>{{ __('suppliers.affected_operations') }}</th>
                        <th>{{ __('suppliers.status') }}</th>
                        <th>{{ __('suppliers.options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($merges as $merge)
                        <tr>
                            <td>{{ $merge->id }}</td>
                            <td>{{ $merge->mainSupplier->name_ar ?? '-' }}</td>
                            <td>{{ $merge->mergedSupplier->name_ar ?? '-' }}</td>
                            <td>{{ $merge->user->name ?? '-' }}</td>
                            <td>
                                @foreach($merge->affected_tables ?? [] as $table => $count)
                                    <span class="badge bg-info">{{ $table }}: {{ $count }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($merge->is_reverted)
                                    <span class="badge bg-secondary">{{ __('suppliers.reverted') }}</span>
                                @else
                                    <span class="badge bg-success">{{ __('suppliers.active') }}</span>
                                @endif
                            </td>
                            <td>
                                @if(!$merge->is_reverted)
                                    <button wire:click="revert({{ $merge->id }})" class="btn btn-sm btn-danger">
                                        <i class="mdi mdi-undo"></i> {{ __('suppliers.revert') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
