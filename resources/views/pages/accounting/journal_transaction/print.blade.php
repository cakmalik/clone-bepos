<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width">
    @include('pages.nota.style-normal')
</head>

<body>


    <div class="print">
        <table class="report-container">
            <thead class="report-header">
                <tr>
                    <th class="report-header-cell">
                        <div class="header-info">
                            <table style="width: 100%; margin-bottom: 5px; border: 0;">
                                <tr style="padding: 0;">
                                    <td style="width: 10%; border-bottom: 1px solid #000; padding: 5px;">
                                        @if ($company->image)
                                            <img src="{{ asset('storage/images/' . $company->image) }}" alt=""
                                                width="70" height="70">
                                        @else
                                            <img src="{{ asset('img/default_img.jpg') }}" alt="" width="70"
                                                height="70">
                                        @endif
                                    </td>
                                    <td style="width: 90%; border-bottom: 1px solid #000; padding: 5px;">
                                        <h1 style="text-align: center;">
                                            {{ $company->name }}
                                        </h1>
                                        <p style="padding: 0;text-align: center;">
                                            {{ $company->address }}
                                        </p>

                                        <p style="padding: 0;text-align: center;">
                                            {{ $company->about }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </th>


                </tr>
            </thead>
            <tbody class="report-content">
                <tr>
                    <td class="report-content-cell">
                        <div class="main">
                            <h4 class="text-center">Laporan Jurnal</h4>
                            <h4 class="text-center">Tanggal : {{ $start_date }} hingga {{ $end_date }}</h4>
                            <br>
                            <table id="nota_journal_table">
                                <thead>
                                    <tr class="border-cust">
                                        <th>Nomor Jurnal</th>
                                        <th>Tipe Jurnal</th>
                                        <th>Tanggal</th>
                                        <th>Akun</th>
                                        <th>Keterangan</th>
                                        <th>Debit</th>
                                        <th>Kredit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction as $value)
                                        <tr class="border-cust border-lr">
                                            <td class="center">{{ $value->code }}</td>
                                            <td>{{ $value->journalNumber->journalType->name }}</td>
                                            <td>{{ $value->journalNumber->date }}</td>
                                            <td>{{ $value->JournalAccount->name }}</td>
                                            @if ($value->journalNumber->inventory_id)
                                                <td><b>Gudang: {{ $value->journalNumber->inventory->name }}
                                                    </b><br>
                                                    <b> {{ $value->description }} </b>
                                                </td>
                                            @else
                                                <td><b>Outlet: {{ $value->journalNumber->outlet->name }}
                                                    </b><br>
                                                    <b>{{ $value->description }} </b>
                                                </td>
                                            @endif

                                            <td>{{ $value->type == 'debit' ? rupiah($value->nominal) : '' }}</td>
                                            <td>{{ $value->type == 'credit' ? rupiah($value->nominal) : '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>
        window.onload = function() {
            parent.iframeLoaded();
        }
    </script>
</body>

</html>
