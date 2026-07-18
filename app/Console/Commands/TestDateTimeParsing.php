<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\AttendanceImport;
use Carbon\Carbon;

class TestDateTimeParsing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:datetime-parsing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test datetime parsing for Excel import';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Testing DateTime Parsing and Database Storage');
        $this->line('');

        // Test 1: DateTime Parsing
        $this->info('=== Testing DateTime Parsing ===');
        $testValues = [
            '15/09/2025  4:00:00 PM',  // Your exact format with double space
            '15/09/2025 4:00:00 PM',   // Single space version
            '21/08/2025 1:12:00 PM',
            '21/08/2025 13:12:00',
            '2025-08-21 13:12:00',
            '08/21/2025 1:12:00 PM',
        ];

        $import = new AttendanceImport();

        foreach ($testValues as $testValue) {
            $this->line("Testing: '{$testValue}'");
            
            // Use reflection to access the private parseDateTime method
            $reflection = new \ReflectionClass($import);
            $method = $reflection->getMethod('parseDateTime');
            $method->setAccessible(true);
            
            $result = $method->invoke($import, $testValue);
            
            if ($result) {
                $this->info("✓ SUCCESS: " . $result->format('Y-m-d H:i:s'));
            } else {
                $this->error("✗ FAILED: Could not parse");
            }
            
            $this->line('');
        }

        // Test 2: Database Storage
        $this->info('=== Testing Database Storage ===');
        
        try {
            // Create a test attendance record
            $checkIn = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s A', '15/09/2025 4:00:00 PM');
            $checkOut = \Carbon\Carbon::createFromFormat('d/m/Y H:i:s A', '15/09/2025 8:00:00 PM');
            
            $this->info("Parsed check_in: " . $checkIn->format('Y-m-d H:i:s'));
            $this->info("Parsed check_out: " . $checkOut->format('Y-m-d H:i:s'));

            // Get first employee for testing
            $employee = \App\models\hr\Employee::first();
            if (!$employee) {
                $this->error("No employees found in database");
                return 1;
            }

            $this->info("Using employee: {$employee->id} - {$employee->full_name}");

            $attendance = \App\models\hr\hr_attendance::create([
                'employee_id' => $employee->id,
                'check_in' => $checkIn->format('Y-m-d H:i:s'),
                'check_out' => $checkOut->format('Y-m-d H:i:s'),
                'worked_hours' => 4.0,
                'shortage' => 0,
                'penalty' => 0,
            ]);

            $this->info("✓ Attendance record created with ID: {$attendance->id}");

            // Retrieve the saved record to see what was actually stored
            $saved = \App\models\hr\hr_attendance::find($attendance->id);
            $raw = \DB::table('hr_attendance')->where('id', $attendance->id)->first();

            $this->info("Model check_in: " . $saved->check_in);
            $this->info("Model check_out: " . $saved->check_out);
            $this->info("Raw DB check_in: " . $raw->check_in);
            $this->info("Raw DB check_out: " . $raw->check_out);

            // Clean up test record
            $attendance->delete();
            $this->info("✓ Test record cleaned up");

        } catch (\Exception $e) {
            $this->error("Database test failed: " . $e->getMessage());
            return 1;
        }

        $this->info('All Tests Complete!');
        
        return 0;
    }
}
