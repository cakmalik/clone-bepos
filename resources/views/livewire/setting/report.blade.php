@section('page-title')
    Pengaturan Laporan
@endsection
@section('action-header')
@endsection
<div class="py-3">
    <div class="form-group mb-3">
        <label for="daily_task" class="mb-2">Proses Generate Laporan Harian</label>
        <select name="time" id="time" class="form-control" wire:model='process_daily_task_time'>
            @for ($hour = 0; $hour < 24; $hour++)
                @for ($minute = 0; $minute < 60; $minute += 30)
                    @php
                        $time = sprintf('%02d:%02d', $hour, $minute);
                    @endphp
                    <option value="{{ $time }}">{{ $time }}</option>
                @endfor
            @endfor
        </select>
    </div>

    <button class="btn btn-primary mt-6" wire:click="save">Simpan</button>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('showAlert', event => {
                alert(event.detail.message);
            });
        });
    </script>

</div>
