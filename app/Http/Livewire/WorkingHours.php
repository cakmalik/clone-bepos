<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Cashflow;

class WorkingHours extends Component
{
    public $start_date;
    public $end_date;

    public $col = [];
    public $grandTotal = 0;

    public function mount()
    {
        $this->start_date = date('Y-m-d');
        $this->end_date = date('Y-m-d');
        $this->loadData();
    }

    public function updated($field)
    {
        if (in_array($field, ['start_date', 'end_date'])) {
            $this->loadData();
        }
    }

    public function loadData()
    {
        // query utama cashflows
        $query = Cashflow::query()
            ->where('type', 'modal')
            ->with('user:id,name'); // join user agar langsung tersedia

        // filter tanggal kalau ada
        if ($this->start_date && $this->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->start_date)->startOfDay(),
                Carbon::parse($this->end_date)->endOfDay(),
            ]);
        }

        // clone untuk total durasi
        $totals = (clone $query)
            ->selectRaw('user_id, SUM(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as total_seconds')
            ->groupBy('user_id')
            ->with('user:id,users_name')
            ->get()
            ->filter(fn($row) => $row->user !== null)
            ->map(fn($row) => [
                'user' => $row->user->users_name ?? 'Unknown',
                'total_hours' => round($row->total_seconds / 3600, 2),
            ]);

        $this->col = $totals;
        $this->grandTotal = $totals->sum('total_hours');
    }

    public function render()
    {
        return view('livewire.working-hours');
    }
}
