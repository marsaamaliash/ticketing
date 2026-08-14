<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat {{ $ticket->ticket_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; margin: 15mm; }
        .header { text-align: center; border-bottom: 3px solid #1f2937; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0 0 4px 0; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 10px; color: #6b7280; }
        .section-title { font-size: 10px; color: #6b7280; text-transform: uppercase; margin-top: 14px; margin-bottom: 4px; font-weight: bold; }
        .value { font-size: 13px; font-weight: bold; }
        .status-box { display: inline-block; padding: 6px 14px; border: 2px solid #000; border-radius: 4px; font-weight: bold; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        td { padding: 6px 8px; border: 1px solid #d1d5db; vertical-align: top; font-size: 11px; }
        .signature { margin-top: 40px; }
        .signature table { border: none; }
        .signature td { border: none; text-align: center; }
        .signature-line { margin-top: 60px; border-top: 1px solid #000; padding-top: 4px; width: 70%; margin-left: auto; margin-right: auto; }
        .small { font-size: 10px; color: #6b7280; }
        .footer-note { margin-top: 30px; padding: 6px; border: 1px dashed #9ca3af; font-size: 9px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>

<div class="header">
    <h1>SURAT BUKTI TIKET LAYANAN</h1>
    <p>Dicetak: {{ $issuedAt->format('d M Y H:i') }} &middot; Tiket diberikan kepada pelanggan</p>
</div>

<table style="border: none; margin-top: 0;">
    <tr>
        <td style="border: none; padding: 0 8px 0 0; width: 60%;">
            <div class="section-title">Nomor Tiket</div>
            <div class="value" style="font-size: 16px;">{{ $ticket->ticket_number }}</div>
            <div class="section-title" style="margin-top: 10px;">Tanggal Dibuat</div>
            <div class="value">{{ $ticket->created_at->format('d M Y H:i') }}</div>
        </td>
        <td style="border: none; padding: 0 0 0 8px; width: 40%; text-align: right;">
            <div class="status-box">{{ strtoupper($ticket->status_label) }}</div>
            <div class="section-title" style="margin-top: 8px;">Priority</div>
            <div class="value" style="font-size: 13px;">{{ strtoupper($ticket->priority) }}</div>
        </td>
    </tr>
</table>

<div class="section-title">DATA PELANGGAN</div>
<table>
    <tr><td style="width: 30%;"><strong>Kode</strong></td><td>{{ $ticket->customer->customer_code }}</td></tr>
    <tr><td><strong>Nama</strong></td><td>{{ $ticket->customer->name }}</td></tr>
    <tr><td><strong>No. Telepon</strong></td><td>{{ $ticket->customer->phone }}</td></tr>
    <tr><td><strong>Email</strong></td><td>{{ $ticket->customer->email ?: '-' }}</td></tr>
    <tr><td><strong>Alamat</strong></td><td>{{ $ticket->customer->address ?: '-' }}{{ $ticket->customer->city ? ', '.$ticket->customer->city : '' }}</td></tr>
</table>

<div class="section-title">DETAIL TIKET</div>
<table>
    <tr><td style="width: 30%;"><strong>Kategori</strong></td><td>{{ $ticket->category->name }}</td></tr>
    <tr><td><strong>Judul</strong></td><td>{{ $ticket->title }}</td></tr>
    <tr><td><strong>Deskripsi</strong></td><td>{!! nl2br(e($ticket->description)) !!}</td></tr>
    <tr><td><strong>Dibuat oleh (CS)</strong></td><td>{{ $ticket->creator->name }}</td></tr>
    @if($ticket->technician)
    <tr><td><strong>Teknisi</strong></td><td>{{ $ticket->technician->name }}</td></tr>
    @endif
    @if($ticket->scheduled_at)
    <tr><td><strong>Jadwal Kunjungan</strong></td><td>{{ $ticket->scheduled_at->format('d M Y H:i') }}</td></tr>
    @endif
    @if($ticket->finished_at)
    <tr><td><strong>Tanggal Selesai</strong></td><td>{{ $ticket->finished_at->format('d M Y H:i') }}</td></tr>
    @endif
</table>

@if($ticket->devices->isNotEmpty())
<div class="section-title">PERANGKAT TERKAIT</div>
<table>
    <thead>
        <tr style="background: #f3f4f6;">
            <th style="text-align: left; padding: 6px;">Tipe</th>
            <th style="text-align: left; padding: 6px;">Brand / Model</th>
            <th style="text-align: left; padding: 6px;">Serial Number</th>
            <th style="text-align: left; padding: 6px;">Lokasi</th>
            <th style="text-align: left; padding: 6px;">Tgl Instalasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ticket->devices as $d)
        <tr>
            <td>{{ $d->device_type ?: '-' }}</td>
            <td>{{ trim(($d->brand ?? '').' '.($d->model ?? '')) ?: '-' }}</td>
            <td style="font-family: monospace;">{{ $d->serial_number ?: '-' }}</td>
            <td>{{ $d->location ?: '-' }}</td>
            <td>{{ $d->installed_at ? $d->installed_at->format('d M Y') : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="signature">
    <table>
        <tr>
            <td style="width: 50%;">
                <strong>Customer Service</strong><br>
                <span class="small">{{ $ticket->creator->name }}<br>{{ $ticket->created_at->format('d M Y') }}</span>
            </td>
            <td style="width: 50%;">
                <strong>Pelanggan</strong><br>
                <span class="small">{{ $ticket->customer->name }}<br>Tgl: ___________</span>
            </td>
        </tr>
        <tr>
            <td><div class="signature-line">{{ $ticket->creator->name }}</div></td>
            <td><div class="signature-line">{{ $ticket->customer->name }}</div></td>
        </tr>
    </table>
</div>

<div class="footer-note">
    Simpan tiket ini sebagai bukti layanan.
</div>

</body>
</html>
