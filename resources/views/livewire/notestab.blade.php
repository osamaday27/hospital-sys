<div class="card">
    <div class="card-body">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ __('doctor_trans.notes') }}</h4>
            </div>

            <div class="card-body p-0">
                <div id="users-chat">
                    <div class="chat-conversation p-3" style="height: 400px; overflow-y: auto;">
                        <ul class="list-unstyled chat-conversation-list chat-sm">
                            @forelse($notes as $index => $note)
                                <li class="chat-item mb-3 p-3 rounded shadow-sm @if(Auth::id() == $note['user_id']) bg-light @else bg-white @endif">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <strong class="text-primary">{{ $note['user'] }}</strong>
                                            @if($editIndex === $index)
                                                <div class="mt-2">
                                                    <textarea wire:model.defer="editNote" class="form-control mb-2"></textarea>
                                                    <button class="btn btn-sm btn-success" wire:click="updateNote">حفظ</button>
                                                    <button class="btn btn-sm btn-secondary" wire:click="$set('editIndex', null)">إلغاء</button>
                                                </div>
                                            @else
                                                <p class="mb-2 text-wrap lh-lg">{{ $note['notes'] }}</p>
                                                <small class="text-muted">{{ $note['date'] }}</small>
                                            @endif
                                        </div>
                                        @if(Auth::id() == $note['user_id'])
                                            <div class="dropdown ms-2">
                                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                    <i class="mdi mdi-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" wire:click="editNote({{ $index }})">تعديل</a></li>
                                                    <li><a class="dropdown-item text-danger" wire:click="deleteNote({{ $index }})">حذف</a></li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="text-center text-muted">لا توجد ملاحظات حتى الآن</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="border-top border-top-dashed">
                    <div class="row g-2 mx-3 mt-2 mb-3">
                        <div class="col">
                            <div class="position-relative">
                                <input type="text" wire:model.defer="newNote" class="form-control border-secondary bg-light" placeholder="اكتب الملاحظة...">
                                @error('newNote')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-auto">
                            <button wire:click="addNote" class="btn btn-success">
                                <span class="d-none d-sm-inline-block me-2">{{ __('doctor_trans.save') }}</span>
                                <i class="mdi mdi-send float-end"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div><!-- end cardbody -->
        </div><!-- end inner card -->
    </div><!-- end outer card body -->
</div><!-- end outer card -->
