<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Laporan</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <table style="font-weight: 600">
                <table>
                    <tr>
                        <td>Periode</td>
                        <td style="text-align: right; padding-right: 10px;">:</td>
                        <td>{{ $date }}</td>
                    </tr>
                    <tr>
                        <td>Penjualan</td>
                        <td style="text-align: right; padding-right: 10px;">:</td>
                        <td>Rp{{ $summary }}</td>
                    </tr>
                    <tr>
                        <td>Retur</td>
                        <td style="text-align: right; padding-right: 10px;">:</td>
                        <td>Rp{{ $return }}</td>
                    </tr>
                    <tr>
                        <td>Pendapatan</td>
                        <td style="text-align: right; padding-right: 10px;">:</td>
                        <td>Rp{{ $revenue }}</td>
                    </tr>
                </table>
                
            </table>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Tanggal</th>
                        <th>Outlet</th>
                        <th>Pelanggan</th>
                        <th>Status</th>
                        <th>Total transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $i)
                        @php    
                            if ($i->ref_code != null && $i->status == 'success'){
                                $status = 'Retur';
                            } else if ($i->status == 'success') {
                                $status = 'Sukses';
                            } else if ($i->status == 'draft') {
                                $status = 'Draft';
                            }
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $i->sale_code }}</td>
                            <td>{{ Carbon\Carbon::parse($i->sale_date)->format('d/m/y') }}</td>
                            <td>{{ $i->outlet?->name }}</td>
                            <td>{{ $i->customer?->name == 'Walk-in-customer' ? '-' : $i->customer?->name }}</td>
                            <td>{{ $status }}</td>
                            <td>Rp{{ number_format($i->final_amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
    </script>
</body>

</html>
