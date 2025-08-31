<?php

namespace App\Exports;

use App\Models\Sales;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Database\Eloquent\Builder;


class SalesExport implements FromView
{

    private $query;
    private $date;
    private $summary;
    private $return;
    private $revenue;

    public function __construct(Builder $query, $date, $summary, $return, $revenue)
    {
        $this->query = $query;
        $this->date = $date;
        $this->summary = $summary;
        $this->return = $return;
        $this->revenue = $revenue;
    }

    public function view(): View
    {
        $data = $this->query->get();


        return view('export.sales', [
            'sales' => $data,
            'date' => $this->date,
            'summary' => $this->summary,
            'return' => $this->return,
            'revenue' => $this->revenue
        ]);
    }
}
