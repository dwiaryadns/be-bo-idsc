<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order Details</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/fonts/DejaVuSans.ttf') format('truetype');
        }

        * {
            font-family: 'DejaVu Sans', Arial, sans-serif;
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

        .subtotal {
            font-style: italic;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ $data->po_id }}</h1>
            <h1>{{ $data->tanggal_po }}</h1>
        </div>
        <div class="details">
            <table>
                <tr>
                    <th style="background-color: #0763EC; color: white">Pembeli</th>
                    <th style="background-color: #0763EC; color: white">Supplier</th>
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
        <div class="items">
            <h2>Barang</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kuantitas</th>
                        <th>Harga Beli</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data->detail_pembelians as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->barang->nama_barang }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>Rp {{ number_format($item->barang->harga_beli, 2, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_harga, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class="subtotal" colspan="4">Sub Total</td>
                        <td>Rp {{ number_format($data->total_harga, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <h4>Status Pembelian : {{ ucfirst($data->status) }}</h4>
        </div>
    </div>
</body>

</html>