<div class="document-view" style="padding: 0 2cm;">
    <div class="row">
        <div class="col-2">
            @if ($profileCompany)
                <img src="{{ asset('storage/images/' . $profileCompany->image) }}" alt="" width="70" height="70">
            @else
                <img src="{{ asset('img/default_img.jpg') }}" alt="" width="70" height="70">
            @endif

        </div>
        <div class="col-10 text-center d-flex justify-content-center align-items-center">
            <div>
                <h1 class="text-uppercase">{{ profileCompany()->name }}</h1>
                <p class="text-uppercase">{{ profileCompany()->address }} HOTLINE {{ profileCompany()->telp }}</p>
            </div>
        </div>
    </div>

    <div class="row p-2 mb-2">
        <div class="col-12 p-0 border-top font-weight-bold pt-1" style="border-color: #000 !important;">
            <h3 class="report-title">Neraca</h3>
            <h3 class="report-title">Periode Berjalan hingga {{ $end_date }}</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div style="display:flex; flex-direction: row; flex-wrap: wrap; border: 1px solid black; padding: 4px;">
                @forelse ($data as $type => $items)
                    <div style="display:flex; flex-direction: column; min-width: 50%; max-width: 50%;">
                        <div style="font-weight: 800; padding-left: 20px;">
                            <u>{{ $type }}</u>
                        </div>
                        <table>
                            <tbody>
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($items as $item)
                                    <tr>
                                        <td>{{ $item->journal_account_code }} {{ $item->journal_account_name }}</td>
                                        <td style="white-space: nowrap; text-align: right;">
                                            {{ rupiah($item->nominal_summary) }}</td>
                                    </tr>
                                    @php
                                        $total += $item->nominal_summary;
                                    @endphp
                                @endforeach
                                <tr>
                                    <td style="text-align: right;">Total</td>
                                    <td style="text-align: right; font-weight: 800;">
                                        {{ rupiah($item->nominal_summary) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @empty
                    <td class="text-center">Tidak Ada data</td>
                @endforelse


            </div>
        </div>
    </div>
</div>
