<?php
namespace App\Http\Livewire\Setting;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Livewire\Component;

class Report extends Component
{
    public $process_daily_task_time = '';

    public function mount()
    {
        $this->process_daily_task_time = config('app.process_daily_task_time');
    }

    public function save()
    {
        $this->updateEnv('PROCESS_DAILY_TASK_TIME', $this->process_daily_task_time);

        // Refresh konfigurasi agar perubahan langsung diterapkan
        Artisan::call('config:clear');

        $this->dispatchBrowserEvent('showAlert', [
            'message' => 'Data Berhasil disimpan',
        ]);
    }

    public function updateEnv($key, $value)
    {
        $envFile = base_path('.env');

        // Membaca isi file .env
        $envContent = File::get($envFile);

        // Cek apakah variabel sudah ada di dalam .env
        if (preg_match("/^{$key}=.*/m", $envContent)) {
            // Jika ada, update nilai variabel
            $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        } else {
            // Jika tidak ada, tambahkan ke akhir file
            $envContent .= "\n{$key}={$value}\n";
        }

        // Simpan perubahan ke file .env
        File::put($envFile, $envContent);
    }

    public function render()
    {
        return view('livewire.setting.report');
    }
}
