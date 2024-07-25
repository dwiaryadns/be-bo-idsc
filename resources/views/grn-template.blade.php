<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $grnData['grn_id'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .header,
        .footer {
            text-align: center;
        }

        .details {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $grnData['grn_id'] }}</h1>
            <p>Tanggal Penerimaan: {{ date('d M Y H:i',strtotime($grnData['tanggal_penerimaan'])) }}</p>
        </div>
        <div class="details">
            <table>
                <tr>
                    <th style="background-color: #0763EC; color: white">Penerima</th>
                    <th style="background-color: #0763EC; color: white">Pengirim</th>
                </tr>
                <tr>
                    <td>
                        PT. Digital Nusantara Sinergi<br>
                        Jl. Rp. Soeroso No.25 9, RT:9/RW:5,Cikini, Kec. Menteng, Kota Jakarta Pusat, DKI Jakarta
                        10330<br>
                        +62 852 18982730<br>
                        xxxxx@gmail.com
                    </td>
                    <td>
                        PT. Digital Nusantara Sinergi<br>
                        Jl. Rp. Soeroso No.25 9, RT:9/RW:5,Cikini, Kec. Menteng, Kota Jakarta Pusat, DKI Jakarta
                        10330<br>
                        +62 852 18982730<br>
                        xxxxx@gmail.com
                    </td>
                </tr>
            </table>
        </div>
        <div class="received-by">
            <p>Diterima Oleh : {{ $grnData['penerima'] }} </p>
            <p>Dicek Oleh : {{ $grnData['pengecek'] }} </p>
        </div>
        <div class="items">
            <h2>Barang</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>QTY Pembelian</th>
                        <th>QTY Penerimaan</th>
                        <th>Action</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grnData['barangs'] as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['nama'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ $item['barangDatang'] }}</td>
                        <td>{{ $item['status'] }}</td>
                        <td>{{ $item['kondisi'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="notes">
            <p>Notes: {{ $grnData['notes'] }}</p>
        </div>
    </div>
</body>

</html>