<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Label Perangkat</title>
    <style>
        @page { margin: 8mm; }
        body { font-family: DejaVu Sans, sans-serif; margin: 0; }
        .label-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .label-cell {
            display: table-cell;
            width: 33.33%;
            height: 100mm;
            padding: 5mm;
            border: 1px dashed #999;
            vertical-align: top;
            font-size: 9px;
            box-sizing: border-box;
        }
        .label {
            border: 2px solid #000;
            height: 90mm;
            padding: 4mm;
            box-sizing: border-box;
            position: relative;
        }
        .label-header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }
        .label-header h3 { margin: 0; font-size: 10px; }
        .label-header .ticket-no {
            font-family: monospace;
            font-size: 14px;
            font-weight: bold;
            margin-top: 1mm;
        }
        .label-body { font-size: 8px; line-height: 1.3; }
        .label-body .row { margin-bottom: 1.5mm; }
        .label-body .k { color: #555; }
        .label-body .v { font-weight: bold; }
        .label-footer {
            position: absolute;
            bottom: 4mm;
            left: 4mm;
            right: 4mm;
            font-size: 7px;
            border-top: 1px dashed #999;
            padding-top: 1mm;
            text-align: center;
            color: #555;
        }
        .device-badge {
            display: inline-block;
            background: #1f2937;
            color: white;
            padding: 1mm 3mm;
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 1mm;
        }
    </style>
</head>
<body>

@php
    $i = 0;
@endphp

@foreach($tickets as $index => $ticket)
    @if($index > 0)
        <div style="page-break-before: always;"></div>
    @endif

    @php
        $devices = $ticket->devices;
        if ($devices->isEmpty()) {
            $devices = collect([null]);
        }
    @endphp

    @foreach($devices as $device)
        <div class="label">
            <div class="label-header">
                <div class="device-badge">LABEL PERANGKAT</div>
                <h3>{{ $ticket->customer->name }}</h3>
                <div class="ticket-no">{{ $ticket->ticket_number }}</div>
            </div>

            <div class="label-body">
                @if($device)
                    <div class="row"><span class="k">Tipe:</span> <span class="v">{{ $device->device_type ?? '-' }}</span></div>
                    <div class="row"><span class="k">Brand/Model:</span> <span class="v">{{ trim(($device->brand ?? '').' '.($device->model ?? '')) ?: '-' }}</span></div>
                    <div class="row"><span class="k">Serial No:</span><br><span class="v" style="font-family: monospace;">{{ $device->serial_number ?? '-' }}</span></div>
                    <div class="row"><span class="k">Lokasi:</span> <span class="v">{{ $device->location ?? '-' }}</span></div>
                    <div class="row"><span class="k">Tgl Instalasi:</span> <span class="v">{{ $device->installed_at?->format('d M Y') ?? '-' }}</span></div>
                @else
                    <div style="text-align: center; color: #999; font-style: italic; padding: 5mm 0;">Tidak ada data perangkat</div>
                @endif

                <div class="row" style="margin-top: 3mm; padding-top: 2mm; border-top: 1px dotted #ccc;">
                    <span class="k">Kategori:</span> <span class="v">{{ $ticket->category->name }}</span>
                </div>
                <div class="row">
                    <span class="k">Teknisi:</span> <span class="v">{{ $ticket->technician?->name ?? '-' }}</span>
                </div>
                <div class="row">
                    <span class="k">Phone:</span> <span class="v">{{ $ticket->customer->phone }}</span>
                </div>
            </div>

            <div class="label-footer">
                {{ $ticket->customer->address ? mb_substr($ticket->customer->address, 0, 50) : '' }}
                @if($ticket->customer->city) &middot; {{ $ticket->customer->city }} @endif
                <br>Customer: {{ $ticket->customer->customer_code }} &middot; {{ $issuedAt->format('d M Y') }}
            </div>
        </div>

        @php $i++; @endphp
        @if($i % 6 == 0 && !$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
@endforeach

</body>
</html>
