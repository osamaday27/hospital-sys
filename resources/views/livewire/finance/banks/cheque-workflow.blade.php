<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="mdi mdi-book-open-variant me-2"></i> {{ __('bank_trans.cheque_workflow') }}</h5>
            <input type="text" wire:model.debounce.500ms="search" class="form-control w-25"
                   placeholder="{{ __('bank_trans.search_cheques') }}">
        </div>

        <div class="card-body">
            {{-- رسائل --}}
            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="ri-alert-line me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- جدول الشيكات --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('bank_trans.cheque_number') }}</th>
                            <th>{{ __('bank_trans.payee') }}</th>
                            <th>{{ __('bank_trans.amount') }}</th>
                            <th>{{ __('bank_trans.status') }}</th>
                            <th>{{ __('bank_trans.workflow_steps') }}</th>
                            <th>{{ __('bank_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cheques as $chq)
                            <tr>
                                <td>{{ $chq->id }}</td>
                                <td>{{ $chq->cheque_number }}</td>
                                <td>{{ $chq->payee }}</td>
                                <td>{{ number_format($chq->amount,2) }}</td>
                                <td>
                                    <span class="badge 
                                        @if($chq->status == 'issued') bg-secondary
                                        @elseif($chq->status == 'approved') bg-primary
                                        @elseif($chq->status == 'cleared') bg-success
                                        @elseif($chq->status == 'reconciled') bg-dark
                                        @elseif($chq->status == 'bounced') bg-warning
                                        @elseif($chq->status == 'cancelled') bg-danger
                                        @endif">
                                        {{ __('bank_trans.'.$chq->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{-- شريط حالات Workflow --}}
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        @foreach(['issued','approved','cleared','reconciled'] as $step)
                                            <span class="badge rounded-pill 
                                                {{ $chq->status == $step ? 'bg-success' : 'bg-light text-dark' }}">
                                                {{ __('bank_trans.'.$step) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    {{-- Actions --}}
                                    @if($chq->status == 'issued' && auth()->user()->hasBankPermission('can_approve_cheque'))
                                        <button wire:click="changeStatus({{ $chq->id }}, 'approved')" class="btn btn-sm btn-primary" title="{{ __('bank_trans.approval') }}">
                                            <i class="mdi mdi-check-decagram"></i>
                                        </button>
                                    @endif
                                    @if($chq->status == 'approved' && auth()->user()->hasBankPermission('can_clear_cheque'))
                                        <button wire:click="changeStatus({{ $chq->id }}, 'cleared')" class="btn btn-sm btn-success" title="{{ __('bank_trans.clearing') }}">
                                            <i class="mdi mdi-cash-check"></i>
                                        </button>
                                    @endif
                                    @if($chq->status == 'cleared' && auth()->user()->hasBankPermission('can_reconcile_cheque'))
                                        <button wire:click="changeStatus({{ $chq->id }}, 'reconciled')" class="btn btn-sm btn-dark" title="{{ __('bank_trans.reconciliation') }}">
                                            <i class="mdi mdi-scale-balance"></i>
                                        </button>
                                    @endif
                                    @if(in_array($chq->status, ['issued','approved','cleared']) && auth()->user()->hasBankPermission('can_cancel_cheque'))
                                        <button wire:click="changeStatus({{ $chq->id }}, 'cancelled')" class="btn btn-sm btn-danger" title="{{ __('bank_trans.cancel') }}">
                                            <i class="mdi mdi-close-octagon"></i>
                                        </button>
                                    @endif
                                    @if($chq->status == 'cleared' && auth()->user()->hasBankPermission('can_bounce_cheque'))
                                        <button wire:click="changeStatus({{ $chq->id }}, 'bounced')" class="btn btn-sm btn-warning" title="{{ __('bank_trans.bounced') }}">
                                            <i class="mdi mdi-alert-circle"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">{{ __('bank_trans.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $cheques->links() }}
        </div>
    </div>
</div>
