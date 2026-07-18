{{-- resources/views/components/note-button.blade.php --}}

@props([
    'targetType' => null,
    'targetId' => null,
    'contextType' => null,
    'contextId' => null,
    'patientId' => null,
    'reservationId' => null,
    'visitId' => null,
])

<!-- زر فتح النافذة -->
<button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#noteModal">
    📝 أضف ملحوظة
</button>

<!-- Modal -->
<div wire:ignore.self class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">📝 إضافة ملحوظة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body">
                @livewire('notes.create', [
                    'target_type'    => $targetType,
                    'target_id'      => $targetId,
                    'context_type'   => $contextType,
                    'context_id'     => $contextId,
                    'patient_id'     => $patientId,
                    'reservation_id' => $reservationId,
                    'visit_id'       => $visitId,
                ])
            </div>
        </div>
    </div>
</div>
