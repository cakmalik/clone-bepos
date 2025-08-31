<?php

namespace App\Http\Livewire\Developer;

use Livewire\Component;
use App\Models\ProfilCompany;

class Index extends Component
{
    public function mount()
    {
        $profilCompany = ProfilCompany::first();
        if (!$profilCompany) {
            return redirect()->route('developer.setup');
        }
    }
    public function render()
    {

        return view('livewire.developer.index')->layout('layouts.developer');
    }
}
