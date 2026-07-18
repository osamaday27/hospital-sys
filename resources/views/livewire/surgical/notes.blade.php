<div class="container-fluid px-4 py-3">
    <!-- Success Message (Floating Toast) -->
    @if (session()->has('message'))
        <div class="toast show align-items-center text-white bg-success border-0 position-fixed top-4 end-4" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 1000; border-radius: 15px;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="mdi mdi-check-circle-outline me-2 fs-5"></i>
                    {{ session('message') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
        <div class="card-header d-flex justify-content-between align-items-center bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 text-white p-3">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="mdi mdi-message-text-outline me-2"></i>
                {{ trans("surgical_trans.operation_notes_chat") }}
            </h5>
            <span class="badge bg-light text-dark py-2 px-3 rounded-pill">
                <i class="mdi mdi-counter me-1"></i>
                {{ count($notes) }} {{ trans("surgical_trans.notes") }}
            </span>
        </div>
        
        <!-- Conversation Area -->
        <div class="card-body p-0" style="height: 400px; overflow-y: auto; background-color: #f5f5f5;">
            <div class="p-3">
                @forelse ($notes as $item)
                    <div class="d-flex mb-3 @if($item->user_id == auth()->id()) justify-content-end @else justify-content-start @endif">
                        <div class="card @if($item->user_id == auth()->id()) bg-primary text-white @else bg-white @endif" style="max-width: 80%; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="@if($item->user_id == auth()->id()) text-white-50 @else text-muted @endif">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        {{ $item->created_at->format('h:i A - Y/m/d') }}
                                    </small>
                                    @if($item->user_id == auth()->id())
                                        <button class="btn btn-sm p-0 text-white-50" wire:click="delete({{ $item->id }})">
                                            <i class="mdi mdi-delete-outline" style="font-size: 1.2rem;"></i>
                                        </button>
                                    @endif
                                </div>
                                 <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="@if($item->user_id == auth()->id()) text-white-50 @else text-muted @endif">
                                        <i class="mdi mdi-account me-1"></i>
                                        {{ $item->user->name }}
                                    </small>
                                  
                                </div>
                                <p class="mb-0" style="line-height: 1.6;">{{ $item->note }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="mdi mdi-message-off-outline display-4 text-muted"></i>
                        <p class="mt-3">{{ trans("surgical_trans.no_notes_available") }}</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Message Send Area -->
        <div class="card-footer bg-light p-3">
            <div class="input-group">
                <textarea 
                    class="form-control border-0 shadow-sm" 
                    wire:model.defer="note" 
                    rows="2" 
                    placeholder="{{ trans('surgical_trans.write_your_note_here') }}..."
                    style="border-radius: 20px; resize: none;"></textarea>
                <button class="btn btn-primary ms-2" wire:click="save" style="border-radius: 20px;">
                    <i class="mdi mdi-send"></i> {{ trans('surgical_trans.send') }}
                </button>
            </div>
            @error('note') 
                <small class="text-danger d-block mt-2">
                    <i class="mdi mdi-alert-circle-outline me-1"></i> 
                    {{ $message }}
                </small> 
            @enderror
        </div>
    </div>
</div>
