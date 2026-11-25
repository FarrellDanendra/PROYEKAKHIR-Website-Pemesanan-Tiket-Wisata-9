<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $pemesanan->kode_tiket }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; margin: 20px; }
        .header { border-bottom: 2px solid #059669; padding-bottom: 12px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; color: #059669; margin: 0; }
        .subtitle { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .badge { display: inline-block; padding: 4px 10px; background: #d1fae5; color: #065f46; font-weight: bold; border-radius: 4px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; }
        th { background-color: #f9fafb; font-weight: bold; width: 35%; }
        .total { font-size: 15px; font-weight: bold; color: #059669; }
        .footer { margin-top: 30px; font-size: 10px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">E-TIKET &amp; INVOICE PEMESANAN WISATA</p>
        <p class="subtitle">Kode Tiket: <strong>{{ $pemesanan->kode_tiket }}</strong> &nbsp;|&nbsp; Status: <span class="badge">{{ strtoupper(str_replace('_', ' ', $pemesanan->status)) }}</span></p>
    </div>

    <table>
        <tr>
            <th>Destinasi Wisata</th>
            <td>{{ $pemesanan->wisata->nama ?? '-' }} ({{ $pemesanan->wisata->lokasi ?? '-' }})</td>
        </tr>
        <tr>
            <th>Tanggal Kunjungan</th>
            <td>{{ $pemesanan->tanggal_kunjungan->format('d F Y') }}</td>
        </tr>
        <tr>
            <th>Nama Pemesan</th>
            <td>{{ $pemesanan->nama_pemesan ?? $pemesanan->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $pemesanan->email_pemesan ?? $pemesanan->user->email ?? '-' }}</td>
        </tr>
        <tr>
            <th>Nomor Telepon</th>
            <td>{{ $pemesanan->no_telepon ?? '-' }}</td>
        </tr>
        <tr>
            <th>Alamat</th>
            <td>{{ $pemesanan->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <th>Metode Pembayaran</th>
            <td>{{ strtoupper(str_replace('_', ' ', $pemesanan->metode_pembayaran ?? '-')) }}</td>
        </tr>
        <tr>
            <th>Jumlah Tiket</th>
            <td>{{ $pemesanan->jumlah_tiket }} Orang</td>
        </tr>
        <tr>
            <th>Total Harga</th>
            <td class="total">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Waktu Pembayaran</th>
            <td>{{ $pemesanan->paid_at ? $pemesanan->paid_at->format('d M Y H:i') : '-' }}</td>
        </tr>
    </table>

    <div class="footer">
        Tunjukkan kode tiket <strong>{{ $pemesanan->kode_tiket }}</strong> kepada petugas loket saat kunjungan.<br>
        Dicetak pada {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>