<div class="document-view" style="padding: 0 2cm;">
    <div class="row">
        <div class="col-2">
            <img src="{{ asset('storage/images/' . $profileCompany->image) }}" height="100px" />
        </div>
        <div class="col-10 text-center d-flex justify-content-center align-items-center">
            <div>
                <h1 class="text-uppercase">{{ $profileCompany->name }}</h1>
                <p class="text-uppercase">{{ $profileCompany->address }} HOTLINE {{ $profileCompany->telp }}</p>
            </div>
        </div>
    </div>

    <div class="row p-2 mb-2">
        <div class="col-12 p-0 border-top font-weight-bold pt-1" style="border-color: #000 !important;">
            BUKU BESAR
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <table class="table-bordered" style="width: 100%;">
                <tbody>
                    @foreach ($journalAccounts as $journalAccount)
                        <tr>
                            <td colspan="6" class="font-weight-bold">
                                {{ $journalAccount['name'] }}
                            </td>
                        </tr>
                        <tr class="font-weight-bold text-center">
                            <td>NO</td>
                            <td>TANGGAL</td>
                            <td>KETERANGAN</td>
                            <td>DEBIT</td>
                            <td>KREDIT</td>
                            <td>SALDO</td>
                        </tr>
                        <tr>
                            <td colspan="5">Saldo</td>
                            <td class="text-right">{{ rupiah($journalAccount['total_balance']) }}</td>
                        </tr>
                        @foreach ($journalAccount['journal_transactions'] as $key => $journalTransaction)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>{{ $journalTransaction['journal_number']['date'] }}</td>
                                <td>{{ $journalTransaction['description'] }}</td>
                                @if ($journalTransaction['type'] == 'credit')
                                    <td class="text-right">{{ rupiah(0) }}</td>
                                    <td class="text-right">{{ rupiah($journalTransaction['nominal']) }}</td>
                                @else
                                    <td class="text-right">{{ rupiah($journalTransaction['nominal']) }}</td>
                                    <td class="text-right">{{ rupiah(0) }}</td>
                                @endif
                                <td class="text-right">{{ rupiah($journalTransaction['balance']) }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-weight-bold text-right">
                            <td colspan="3">TOTAL</td>
                            <td>{{ rupiah($journalAccount['total_debit']) }}</td>
                            <td>{{ rupiah($journalAccount['total_credit']) }}</td>
                            <td>{{ rupiah($journalAccount['total_balance']) }}</td>
                        </tr>
                        <tr class="trailing-tr">
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
