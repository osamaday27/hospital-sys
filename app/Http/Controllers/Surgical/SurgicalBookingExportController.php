<?php
namespace App\Http\Controllers\surgical;

use App\Http\Controllers\Controller;
use App\Exports\SurgicalBookingsExport;
use Maatwebsite\Excel\Facades\Excel;

class SurgicalBookingExportController extends Controller
{
    public function exportExcel()
    {
        return Excel::download(new SurgicalBookingsExport, 'surgical_bookings.xlsx');
    }
}