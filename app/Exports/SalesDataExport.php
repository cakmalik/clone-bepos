<?php

namespace App\Exports;

use App\Models\SalesDetail;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalesDataExport implements  FromView
{
    protected $data;
    protected $reportType;

    public function __construct($data, $reportType)
    {
        $this->reportType = $reportType;

        $this->data = $data;
        Log::info('SalesDataExport constructor - Data:', ['data' => $data]);
    }

    public function view(): View
    {
        return view('export.laporansales', [
            'data' => $this->data,
            'reportType' => $this->reportType,
        ]);
    }
}
