@extends('layouts.app')
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Hak Akses
                    </h2>
                </div>

                <div class="col-auto">
                    <a href="javascript:history.back()" class="btn btn-outline-primary btn-sm rounded-2">
                        <i class="fa-solid fa-arrow-left"></i>&nbsp; Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card py-3">
                <div class="card-body p-3">
                    {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                    @foreach ($errors->all() as $error)
                        <x-alert level="danger" message="{{ $error }}" />
                    @endforeach --}}
                    <form action="{{route('permission.update',$data['role_id'])}}"  method="POST" enctype="multipart/form-data" class="form-detect-unsaved">
                    @csrf
                    @method('PUT')
                    <div class="table-responsive mb-4">
                        <table class="card-table w-100 bg-grey">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th width="50%" class="py-2">Fitur Permission</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['access_map'] as $key=> $value)
                                    <tr>
                                        <td>{{ $value['menu_name'] }}</td>
                                        <td class="pt-3">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" name="menu_id[]" value="{{ $value['menu_id'] }}" type="checkbox" {{ $value['is_access'] ? 'checked' : '' }}>
                                                <span class="form-check-label"></span>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group">
                        <input class="form-check-input mb-3" type="checkbox" value="" id="giveAllAccess"> Beri Akses Semua
                    </div>
                    <div class="form-group text-end">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i>&nbsp;&nbsp; Simpan Perubahan</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
      $('body').on('click', '#giveAllAccess', function() {
        $('input:checkbox').not(this).prop('checked', this.checked);
    }) ;
</script>
@endpush
