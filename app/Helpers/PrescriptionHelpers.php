<?php

namespace App\Helpers;

use App\Models\Medical\{Prescription, PrescriptionItem, Medication};
use Illuminate\Support\Facades\Auth;

class PrescriptionHelpers
{
    /**
     * Get most prescribed medications by current doctor
     */
    public static function getMostPrescribedMedications($limit = 10, $days = 30)
    {
        return Medication::whereHas('prescriptionItems', function($q) use ($days) {
            $q->whereHas('prescription', function($q2) use ($days) {
                $q2->where('doctor_id', Auth::id())
                   ->where('created_at', '>=', now()->subDays($days));
            });
        })
        ->withCount(['prescriptionItems' => function($q) use ($days) {
            $q->whereHas('prescription', function($q2) use ($days) {
                $q2->where('doctor_id', Auth::id())
                   ->where('created_at', '>=', now()->subDays($days));
            });
        }])
        ->orderBy('prescription_items_count', 'desc')
        ->limit($limit)
        ->get();
    }

    /**
     * Get common medication combinations
     */
    public static function getCommonCombinations($medicationId, $limit = 5)
    {
        $prescriptionIds = PrescriptionItem::where('medication_id', $medicationId)
            ->pluck('prescription_id')
            ->toArray();

        return Medication::whereHas('prescriptionItems', function($q) use ($prescriptionIds, $medicationId) {
            $q->whereIn('prescription_id', $prescriptionIds)
              ->where('medication_id', '!=', $medicationId);
        })
        ->withCount(['prescriptionItems' => function($q) use ($prescriptionIds) {
            $q->whereIn('prescription_id', $prescriptionIds);
        }])
        ->orderBy('prescription_items_count', 'desc')
        ->limit($limit)
        ->get();
    }

    /**
     * Format frequency text
     */
    public static function formatFrequency($timer)
    {
        $frequencies = [
            '1' => __('doctor_trans.once_daily'),
            '2' => __('doctor_trans.twice_daily'),
            '3' => __('doctor_trans.three_times_daily'),
            '4' => __('doctor_trans.four_times_daily'),
            'qd' => __('doctor_trans.once_daily'),
            'bid' => __('doctor_trans.twice_daily'),
            'tid' => __('doctor_trans.three_times_daily'),
            'qid' => __('doctor_trans.four_times_daily'),
            'prn' => __('doctor_trans.as_needed'),
        ];

        return $frequencies[strtolower($timer)] ?? $timer;
    }

    /**
     * Check for drug interactions (placeholder for future implementation)
     */
    public static function checkInteractions(array $medicationIds)
    {
        // This should connect to a drug interaction database
        // For now, return empty array
        return [];
    }

    /**
     * Get prescription statistics for doctor
     */
    public static function getDoctorStatistics($doctorId, $days = 30)
    {
        $prescriptions = Prescription::where('doctor_id', $doctorId)
            ->where('created_at', '>=', now()->subDays($days))
            ->with('items')
            ->get();

        return [
            'total_prescriptions' => $prescriptions->count(),
            'total_medications' => $prescriptions->sum(fn($p) => $p->items->count()),
            'avg_medications_per_prescription' => $prescriptions->count() > 0 
                ? round($prescriptions->sum(fn($p) => $p->items->count()) / $prescriptions->count(), 2)
                : 0,
            'most_prescribed' => self::getMostPrescribedMedications(5, $days),
        ];
    }

    /**
     * Generate prescription report
     */
    public static function generateReport(Prescription $prescription)
    {
        return [
            'prescription_id' => $prescription->id,
            'patient_name' => $prescription->patient->name ?? 'N/A',
            'doctor_name' => $prescription->doctor->name ?? 'N/A',
            'date' => $prescription->prescription_date->format('Y-m-d H:i'),
            'medications' => $prescription->items->map(function($item) {
                return [
                    'medication' => $item->medication_name,
                    'dose' => $item->quantity . ' ' . $item->unit_name,
                    'frequency' => self::formatFrequency($item->timer),
                    'duration' => $item->duration_name,
                    'route' => $item->taken_name,
                    'eye' => $item->eye_display,
                    'notes' => $item->notes,
                ];
            })->toArray(),
            'total_items' => $prescription->items->count(),
        ];
    }

    /**
     * Validate prescription data
     */
    public static function validatePrescription(array $data)
    {
        $errors = [];

        if (empty($data['medications'])) {
            $errors[] = 'يجب إضافة دواء واحد على الأقل';
        }

        foreach ($data['medications'] as $index => $med) {
            if (empty($med['name'])) {
                $errors[] = "السطر " . ($index + 1) . ": اسم الدواء مطلوب";
            }
            if (empty($med['quantity']) || $med['quantity'] <= 0) {
                $errors[] = "السطر " . ($index + 1) . ": الجرعة يجب أن تكون أكبر من صفر";
            }
            if (empty($med['frequency'])) {
                $errors[] = "السطر " . ($index + 1) . ": التكرار مطلوب";
            }
        }

        return $errors;
    }

    /**
     * Get quick templates for common conditions
     */
    public static function getTemplates()
    {
        return [
            'eye_infection' => [
                'name' => __('Eye Infection Template'),
                'medications' => [
                    ['type' => 'antibiotic_eye_drop', 'frequency' => '4', 'duration' => '7 days'],
                    ['type' => 'lubricant_eye_drop', 'frequency' => 'prn', 'duration' => '14 days'],
                ]
            ],
            'dry_eye' => [
                'name' => __('Dry Eye Template'),
                'medications' => [
                    ['type' => 'artificial_tears', 'frequency' => '4-6', 'duration' => '1 month'],
                    ['type' => 'omega3', 'frequency' => '1', 'duration' => '3 months'],
                ]
            ],
            'glaucoma' => [
                'name' => __('Glaucoma Template'),
                'medications' => [
                    ['type' => 'pressure_lowering_drop', 'frequency' => '1-2', 'duration' => 'continuous'],
                ]
            ],
        ];
    }

    /**
     * Export prescription to PDF (placeholder)
     */
    public static function exportToPDF(Prescription $prescription)
    {
        // This will be implemented with a PDF library like DOMPDF or TCPDF
        $data = self::generateReport($prescription);
        
        // Return PDF content or file path
        return $data;
    }

    /**
     * Send prescription via email (placeholder)
     */
    public static function sendViaEmail(Prescription $prescription, $email)
    {
        // This will be implemented with Laravel Mail
        // Mail::to($email)->send(new PrescriptionMail($prescription));
        
        return true;
    }

    /**
     * Get medication alternatives
     */
    public static function getAlternatives($medicationId)
    {
        $medication = Medication::find($medicationId);
        
        if (!$medication) {
            return collect();
        }

        // Find medications in same category with similar properties
        return Medication::where('category', $medication->category)
            ->where('id', '!=', $medicationId)
            ->active()
            ->limit(5)
            ->get();
    }

    /**
     * Calculate total treatment cost (if prices are available)
     */
    public static function calculateCost(Prescription $prescription)
    {
        $total = 0;

        foreach ($prescription->items as $item) {
            if ($item->medication && isset($item->medication->price)) {
                $total += $item->medication->price * $item->quantity;
            }
        }

        return $total;
    }

    /**
     * Get prescription adherence tips
     */
    public static function getAdherenceTips($prescriptionItem)
    {
        $tips = [];

        if ($prescriptionItem->timer >= 3) {
            $tips[] = __('Set reminders on your phone for each dose');
        }

        if ($prescriptionItem->duration_id && $prescriptionItem->duration->days > 7) {
            $tips[] = __('Use a pill organizer for better tracking');
        }

        if ($prescriptionItem->taken_name == 'Eye Drop') {
            $tips[] = __('Wash hands before application');
            $tips[] = __('Wait 5 minutes between different eye drops');
        }

        return $tips;
    }
}