<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Customer Report</title>
    <style>
        body {
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            /* This makes borders collapse into one another */
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            /* This sets a solid black border around cells */
            padding: 8px;
            text-align: left;
        }
        
    </style>
</head>

<body>
    <h2>Data Customer  -  <span>{{ date('d/mm/Y') }}</span></h2> 

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Telp</th>
                <th>Kota</th>
                <th>Kecamatan</th>
                <th>Desa</th>
                <th>Dusun</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $key => $value)
            <tr>
                <td>{{ ++$key }}</td>
                <td>{{ $value->code }}</td>
                <td>{{ $value->name }}</td>
                <td>{{ $value->phone }}</td>
                <td>{{ $value->city_name }}</td>
                <td>{{ $value->district_name }}</td>
                <td>{{ $value->village_name }}</td>
                <td>{{ $value->sub_village }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>