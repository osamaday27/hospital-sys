<?php

namespace App\Imports;

use App\models\hr\Employee;
use App\models\hr\hr_attendance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class AttendanceImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    private $customErrors = [];
    private $successCount = 0;
    private $failureCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Debug: Log the original row data to see what Excel is sending
            \Log::info("Original Excel row data", [
                'row_keys' => array_keys($row),
                'row_values' => array_values($row),
                'row_count' => count($row)
            ]);

            // Handle flexible column names (normalize keys)
            $normalizedRow = $this->normalizeRowKeys($row);
            
            // Debug: Log normalized row
            \Log::info("Normalized row data", $normalizedRow);
            
            // Check if we have the required columns
            if (empty($normalizedRow['employee_code']) || empty($normalizedRow['check_in']) || empty($normalizedRow['check_out'])) {
                $this->customErrors[] = "Missing required columns in row. Found columns: " . implode(', ', array_keys($row));
                $this->failureCount++;
                return null;
            }
            
            // Find employee by employee_code
            $employee = Employee::where('employee_code', $normalizedRow['employee_code'])->first();
            
            if (!$employee) {
                $this->customErrors[] = "Employee with code {$normalizedRow['employee_code']} not found";
                $this->failureCount++;
                return null;
            }

            // Check if employee has an active contract
            $contract = $employee->activeContract;
            if (!$contract) {
                $this->customErrors[] = "Employee {$normalizedRow['employee_code']} does not have an active contract";
                $this->failureCount++;
                return null;
            }

            // Validate contract is not expired
            $currentDate = now();
            if ($contract->end_date && $currentDate->gt(Carbon::parse($contract->end_date))) {
                $endDateFormatted = $contract->end_date instanceof Carbon ? $contract->end_date->format('Y-m-d') : $contract->end_date;
                $this->customErrors[] = "Employee {$normalizedRow['employee_code']} has an expired contract (ended on {$endDateFormatted})";
                $this->failureCount++;
                return null;
            }

            // Debug: Log the raw data received from Excel
            \Log::info("Raw Excel data for employee {$normalizedRow['employee_code']}", [
                'raw_check_in' => $normalizedRow['check_in'],
                'raw_check_in_type' => gettype($normalizedRow['check_in']),
                'raw_check_out' => $normalizedRow['check_out'],
                'raw_check_out_type' => gettype($normalizedRow['check_out']),
            ]);

            // Parse dates with improved logic
            $checkIn = $this->parseDateTime($normalizedRow['check_in'], 'check_in');
            $checkOut = $this->parseDateTime($normalizedRow['check_out'], 'check_out');
            
            if (!$checkIn || !$checkOut) {
                $checkInValue = $normalizedRow['check_in'] ?? 'null';
                $checkOutValue = $normalizedRow['check_out'] ?? 'null';
                $this->customErrors[] = "Employee {$normalizedRow['employee_code']}: Invalid date format. Check-in: '{$checkInValue}', Check-out: '{$checkOutValue}'. Please use formats like: DD/MM/YYYY HH:MM";
                $this->failureCount++;
                return null;
            }
            
            // Debug: Log the parsed times before validation
            \Log::info("Parsed times for validation", [
                'employee_code' => $normalizedRow['employee_code'],
                'check_in' => $checkIn->format('Y-m-d H:i:s'),
                'check_out' => $checkOut->format('Y-m-d H:i:s'),
                'check_in_timestamp' => $checkIn->timestamp,
                'check_out_timestamp' => $checkOut->timestamp,
                'is_check_out_after' => $checkOut->gt($checkIn)
            ]);

            // Validate that check-out is after check-in
            if ($checkOut->lte($checkIn)) {
                // If check-out is not after check-in, try to fix it by adding time
                if ($checkIn->isSameDay($checkOut)) {
                    // Same day - ensure check-out is at least 1 hour after check-in
                    $checkOut = $checkIn->copy()->addHours(1);
                    \Log::warning("Fixed same-day check-out time", [
                        'employee_code' => $normalizedRow['employee_code'],
                        'original_check_out' => $checkOut->format('Y-m-d H:i:s'),
                        'fixed_check_out' => $checkOut->format('Y-m-d H:i:s')
                    ]);
                } else {
                    $this->customErrors[] = "Employee {$normalizedRow['employee_code']}: Check-out time ({$checkOut->format('Y-m-d H:i:s')}) must be after check-in time ({$checkIn->format('Y-m-d H:i:s')})";
                    $this->failureCount++;
                    return null;
                }
            }
            
            // Log successful parsing for debugging
            \Log::info("Successfully parsed attendance for employee {$normalizedRow['employee_code']}", [
                'original_check_in' => $normalizedRow['check_in'],
                'parsed_check_in' => $checkIn->format('Y-m-d H:i:s'),
                'original_check_out' => $normalizedRow['check_out'],
                'parsed_check_out' => $checkOut->format('Y-m-d H:i:s')
            ]);
            
            // Check for overlapping attendance
            $overlappingAttendance = hr_attendance::where('employee_id', $employee->id)
                ->whereDate('check_in', '=', $checkIn->format('Y-m-d'))
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('check_in', [$checkIn, $checkOut])
                          ->orWhereBetween('check_out', [$checkIn, $checkOut])
                          ->orWhere(function ($q) use ($checkIn, $checkOut) {
                              $q->where('check_in', '<=', $checkIn)
                                ->where('check_out', '>=', $checkOut);
                          });
                })
                ->exists();
        
            if ($overlappingAttendance) {
                $this->customErrors[] = "Employee {$normalizedRow['employee_code']} already has overlapping attendance for {$checkIn->format('Y-m-d')}";
                $this->failureCount++;
                return null;
            }

            // Calculate worked hours
            $workedHours = min(24, number_format($checkOut->diffInMinutes($checkIn) / 60, 2));

            // Calculate shortage based on employee's schedule
            $shortage = 0;
            $employee = Employee::with('schedule.days')->find($employee->id);
            if ($employee && $employee->schedule) {
                $dayOfWeek = $checkIn->dayOfWeek + 1; // Carbon uses 0-6, we use 1-7
                $scheduleDay = $employee->schedule->days->where('day_of_week', $dayOfWeek)->first();
                
                if ($scheduleDay && $scheduleDay->start_time && $scheduleDay->end_time) {
                    $scheduleStart = Carbon::parse($scheduleDay->start_time);
                    $scheduleEnd = Carbon::parse($scheduleDay->end_time);
                    $scheduledHours = $scheduleEnd->diffInMinutes($scheduleStart) / 60;
                    $shortage = max(0, $scheduledHours - $workedHours);
                }
            }

            // Calculate penalty based on shortage (only for non-hourly employees)
            $penalty = 0;
            $shouldCalculatePenalty = !($contract->salary_level && strtolower($contract->salary_level) === 'hours');
            
            if ($shouldCalculatePenalty) {
                $shortageMinutes = $shortage * 60; // Convert hours to minutes
                
                if ($shortageMinutes >= 15 && $shortageMinutes < 120) {
                    $penalty = 0.25; // 0.25 days
                } elseif ($shortageMinutes >= 120 && $shortageMinutes < 240) {
                    $penalty = 0.5; // 0.5 days
                } elseif ($shortageMinutes >= 240) {
                    $penalty = 1.0; // 1 day
                }
            }

            $this->successCount++;

            // Debug: Log the final data being saved
            \Log::info("Final data being saved to database", [
                'employee_id' => $employee->id,
                'check_in' => $checkIn->format('Y-m-d H:i:s'),
                'check_out' => $checkOut->format('Y-m-d H:i:s'),
                'worked_hours' => $workedHours,
                'shortage' => number_format($shortage, 2),
                'penalty' => $penalty,
            ]);

            return new hr_attendance([
                'employee_id' => $employee->id,
                'check_in' => $checkIn->format('Y-m-d H:i:s'),
                'check_out' => $checkOut->format('Y-m-d H:i:s'),
                'worked_hours' => $workedHours,
                'shortage' => number_format($shortage, 2),
                'penalty' => $penalty,
            ]);

        } catch (\Exception $e) {
            $this->customErrors[] = "Error processing row: " . $e->getMessage();
            $this->failureCount++;
            \Log::error("Exception in AttendanceImport", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'employee_code' => ['required'],
            'check_in' => ['required'],
            'check_out' => ['required'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'employee_code.required' => 'Employee code is required',
            'check_in.required' => 'Check-in time is required',
            'check_out.required' => 'Check-out time is required',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function getErrors(): array
    {
        return $this->customErrors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailureCount(): int
    {
        return $this->failureCount;
    }

    /**
     * Normalize row keys to handle flexible column names
     * FIXED: Better handling of common column variations
     */
    private function normalizeRowKeys(array $row)
    {
        $normalized = [];
        
        foreach ($row as $key => $value) {
            // Clean the key but preserve original structure for better matching
            $cleanKey = trim(strtolower($key));
            $cleanKey = preg_replace('/[^a-z0-9_]/', '', $cleanKey); // Remove special chars but keep underscores
            
            // Map various possible column names to our standard names
            if (in_array($cleanKey, [
                'employeecode', 'empcode', 'code', 'employee', 'emp_code', 'employee_code'
            ])) {
                $normalized['employee_code'] = $value;
            } elseif (in_array($cleanKey, [
                'checkin', 'timein', 'check_in', 'starttime', 'arrival', 'datein', 'checkintime', 'checkindate'
            ])) {
                $normalized['check_in'] = $value;
            } elseif (in_array($cleanKey, [
                'checkout', 'timeout', 'check_out', 'endtime', 'departure', 'dateout', 'checkouttime', 'checkoutdate'
            ])) {
                $normalized['check_out'] = $value;
            } else {
                // Keep original key if no mapping found
                $normalized[$key] = $value;
            }
        }
        
        return $normalized;
    }

    /**
     * FIXED: Improved date time parsing with better format handling
     */
    private function parseDateTime($dateValue, $context = 'check_in')
    {
        if (empty($dateValue)) {
            return null;
        }

        // Log the input for debugging
        \Log::info("Parsing datetime", [
            'input_value' => $dateValue,
            'input_type' => gettype($dateValue),
            'context' => $context
        ]);

        // Handle Excel serial dates first (numeric values > 1)
        if (is_numeric($dateValue) && $dateValue > 1) {
            return $this->parseExcelSerialDate($dateValue);
        }

        // Handle Excel time fractions (0 < value < 1)
        if (is_numeric($dateValue) && $dateValue > 0 && $dateValue < 1) {
            return $this->parseExcelTimeFraction($dateValue);
        }

        // Handle DateTime objects
        if ($dateValue instanceof \DateTime || $dateValue instanceof \Carbon\Carbon) {
            return Carbon::instance($dateValue);
        }

        // Convert to string and clean it
        $dateString = trim((string) $dateValue);
        
        // Remove multiple spaces and normalize
        $dateString = preg_replace('/\s+/', ' ', $dateString);
        
        // Check if AM/PM is missing but time suggests 12-hour format
        $dateString = $this->normalizeAmPmFormat($dateString);
        
        \Log::info("Cleaned date string", [
            'original' => $dateValue,
            'cleaned' => $dateString
        ]);

        // Try specific formats - 12-hour formats FIRST since your data uses AM/PM
        $formats = [
            // 12-hour formats (AM/PM) - PRIORITIZED
            'd/m/Y g:i A',        // 21/08/2025 8:12 AM (12-hour, no leading zeros)
            'd/m/Y h:i A',        // 21/08/2025 08:12 AM (12-hour, with leading zeros)
            'd/m/Y g:i:s A',      // 21/08/2025 8:12:00 AM
            'd/m/Y h:i:s A',      // 21/08/2025 08:12:00 AM
            
            // Handle formats with multiple spaces or formatting issues
            'd/m/Y  g:i A',       // 21/08/2025  8:12 AM (double space)
            'd/m/Y  h:i A',       // 21/08/2025  08:12 AM (double space)
            'd/m/Y  g:i:s A',     // 21/08/2025  8:12:00 AM (double space)
            'd/m/Y  h:i:s A',     // 21/08/2025  08:12:00 AM (double space)
            
            // 24-hour formats (fallback)
            'd/m/Y G:i',          // 21/08/2025 8:12 (24-hour, no leading zeros)
            'd/m/Y H:i',          // 21/08/2025 08:12 (24-hour, with leading zeros)
            'd/m/Y G:i:s',        // 21/08/2025 8:12:00
            'd/m/Y H:i:s',        // 21/08/2025 08:12:00
            
            // Alternative separators (12-hour first)
            'd-m-Y g:i A',
            'd-m-Y h:i A',
            'd-m-Y g:i:s A',
            'd-m-Y h:i:s A',
            'd-m-Y G:i',
            'd-m-Y H:i',
            'd-m-Y G:i:s',
            'd-m-Y H:i:s',
            
            // Standard formats
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d',
            
            // US formats (12-hour first)
            'm/d/Y g:i A',
            'm/d/Y h:i A',
            'm/d/Y g:i:s A',
            'm/d/Y h:i:s A',
            'm/d/Y G:i',
            'm/d/Y H:i',
            'm/d/Y G:i:s',
            'm/d/Y H:i:s',
        ];

        // Try each format
        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $dateString);
                if ($parsed !== false && $this->isValidDate($parsed)) {
                    \Log::info("Successfully parsed with format: {$format}", [
                        'input' => $dateValue,
                        'parsed' => $parsed->format('Y-m-d H:i:s')
                    ]);
                    return $parsed;
                }
            } catch (\Exception $e) {
                // Continue to next format
                continue;
            }
        }

        // Handle date-only formats and add default time
        $dateOnlyFormats = [
            'd/m/Y',     // 21/08/2025
            'd-m-Y',     // 21-08-2025
            'Y-m-d',     // 2025-08-21
            'm/d/Y',     // 08/21/2025
        ];

        foreach ($dateOnlyFormats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $dateString);
                if ($parsed !== false && $this->isValidDate($parsed)) {
                    // Add default time based on context
                    $defaultTime = ($context === 'check_in') ? '09:00:00' : '17:00:00';
                    $parsed = $parsed->setTimeFromTimeString($defaultTime);
                    
                    \Log::warning("Date-only format detected, added default time", [
                        'input' => $dateValue,
                        'format' => $format,
                        'default_time' => $defaultTime,
                        'parsed' => $parsed->format('Y-m-d H:i:s')
                    ]);
                    return $parsed;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Last resort: try Carbon's flexible parsing
        try {
            $parsed = Carbon::parse($dateString);
            if ($this->isValidDate($parsed)) {
                \Log::info("Parsed with Carbon::parse", [
                    'input' => $dateValue,
                    'parsed' => $parsed->format('Y-m-d H:i:s')
                ]);
                return $parsed;
            }
        } catch (\Exception $e) {
            // Final failure
        }

        \Log::error("Failed to parse datetime", [
            'input' => $dateValue,
            'input_type' => gettype($dateValue),
            'context' => $context
        ]);

        return null;
    }

    /**
     * Parse Excel serial date numbers
     */
    private function parseExcelSerialDate($serialDate)
    {
        try {
            // Excel epoch is 1900-01-01, but Excel incorrectly treats 1900 as leap year
            // So we use 1899-12-30 as base and subtract 1 day for dates after Feb 28, 1900
            $baseDate = Carbon::create(1899, 12, 30);
            
            $days = (float) $serialDate;
            
            // Handle Excel's leap year bug
            if ($days > 59) { // After Feb 28, 1900
                $days -= 1;
            }
            
            // Separate the integer part (days) and decimal part (time)
            $wholeDays = floor($days);
            $timeFraction = $days - $wholeDays;
            
            // Calculate the base date
            $parsed = $baseDate->copy()->addDays($wholeDays);
            
            // Add the time component if present
            if ($timeFraction > 0) {
                $hours = $timeFraction * 24;
                $wholeHours = floor($hours);
                $minutes = ($hours - $wholeHours) * 60;
                $wholeMinutes = floor($minutes);
                $seconds = ($minutes - $wholeMinutes) * 60;
                $wholeSeconds = round($seconds);
                
                $parsed->setTime($wholeHours, $wholeMinutes, $wholeSeconds);
            }
            
            if ($this->isValidDate($parsed)) {
                \Log::info("Parsed Excel serial date with time", [
                    'input' => $serialDate,
                    'days' => $days,
                    'whole_days' => $wholeDays,
                    'time_fraction' => $timeFraction,
                    'parsed' => $parsed->format('Y-m-d H:i:s')
                ]);
                return $parsed;
            }
        } catch (\Exception $e) {
            \Log::error("Excel serial date parsing failed", [
                'input' => $serialDate,
                'error' => $e->getMessage()
            ]);
        }
        
        return null;
    }

    /**
     * Parse Excel time fractions (decimal part of a day)
     */
    private function parseExcelTimeFraction($timeFraction)
    {
        try {
            $totalSeconds = $timeFraction * 24 * 60 * 60;
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $seconds = floor($totalSeconds % 60);
            
            $today = Carbon::today();
            $parsed = $today->setTime($hours, $minutes, $seconds);
            
            \Log::info("Parsed Excel time fraction", [
                'input' => $timeFraction,
                'parsed' => $parsed->format('Y-m-d H:i:s')
            ]);
            
            return $parsed;
        } catch (\Exception $e) {
            \Log::error("Excel time fraction parsing failed", [
                'input' => $timeFraction,
                'error' => $e->getMessage()
            ]);
        }
        
        return null;
    }

    /**
     * Normalize AM/PM format issues that might come from Excel
     */
    private function normalizeAmPmFormat($dateString)
    {
        // If the string contains AM/PM, return as is
        if (preg_match('/\s?(AM|PM|am|pm)\s?$/i', $dateString)) {
            return $dateString;
        }
        
        // Check if this looks like a 12-hour time without AM/PM
        // Pattern: DD/MM/YYYY H:MM where H is 1-12
        if (preg_match('/(\d{1,2}\/\d{1,2}\/\d{4})\s+(\d{1,2}):(\d{2})$/', $dateString, $matches)) {
            $hour = (int)$matches[2];
            $minute = (int)$matches[3];
            
            // If hour is 1-12, it might be 12-hour format missing AM/PM
            if ($hour >= 1 && $hour <= 12) {
                // Make educated guess: 1-7 = AM, 8-12 = depends on context
                // For check-in times, 8-12 is likely AM
                // For check-out times, 1-7 is likely PM, 8-12 could be either
                
                // This is tricky without context, so let's log it and try both
                \Log::warning("Ambiguous time format detected - missing AM/PM", [
                    'original' => $dateString,
                    'hour' => $hour,
                    'minute' => $minute
                ]);
                
                // Return as-is and let multiple format attempts handle it
                return $dateString;
            }
        }
        
        return $dateString;
    }

    /**
     * Validate that a parsed date is reasonable
     */
    private function isValidDate($date)
    {
        return $date && 
               $date instanceof Carbon && 
               $date->year >= 1900 && 
               $date->year <= 2100 &&
               $date->month >= 1 && 
               $date->month <= 12 &&
               $date->day >= 1 && 
               $date->day <= 31;
    }

    /**
     * Test method to verify datetime parsing with 12-hour format data
     */
    public function testDateTimeParsing()
    {
        $testValues = [
            '21/08/2025 8:12 AM',    // 12-hour format
            '21/08/2025 1:12 PM',    // 12-hour format
            '22/08/2025 8:05 AM',    // 12-hour format
            '22/08/2025 12:05 PM',   // 12-hour format
            '21/08/2025 8:12',       // 24-hour format (fallback)
            '21/08/2025 13:12',      // 24-hour format (fallback)
        ];

        foreach ($testValues as $testValue) {
            $checkIn = $this->parseDateTime($testValue, 'check_in');
            $checkOut = $this->parseDateTime($testValue, 'check_out');
            echo "Input: {$testValue}\n";
            echo "  -> Check-in: " . ($checkIn ? $checkIn->format('Y-m-d H:i:s') : 'FAILED TO PARSE') . "\n";
            echo "  -> Check-out: " . ($checkOut ? $checkOut->format('Y-m-d H:i:s') : 'FAILED TO PARSE') . "\n\n";
        }
        
        // Test your specific problematic data
        echo "Testing problematic sequence:\n";
        $checkIn = $this->parseDateTime('21/08/2025 8:12 AM', 'check_in');
        $checkOut = $this->parseDateTime('21/08/2025 1:12 PM', 'check_out');
        
        if ($checkIn && $checkOut) {
            echo "Check-in: " . $checkIn->format('Y-m-d H:i:s') . "\n";
            echo "Check-out: " . $checkOut->format('Y-m-d H:i:s') . "\n";
            echo "Check-out after check-in? " . ($checkOut->gt($checkIn) ? 'YES' : 'NO') . "\n";
            echo "Hours worked: " . $checkOut->diffInHours($checkIn) . "\n";
        }
    }
}