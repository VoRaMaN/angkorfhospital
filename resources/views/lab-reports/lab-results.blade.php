<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Results</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #000; padding: 28px 36px; }

        /* ── Header ──────────────────────────────────────────── */
        .header { display: table; width: 100%; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header-left  { display: table-cell; vertical-align: top; width: 55%; }
        .header-left img { width: 130px; display: block; margin-bottom: 4px; }
        .header-left .addr { font-size: 9px; color: #444; line-height: 1.4; }
        .header-right { display: table-cell; vertical-align: top; text-align: left; width: 45%; font-size: 11px; padding-left: 10px; }
        .header-right table { width: 100%; }
        .header-right td { padding: 1px 4px 1px 0; font-size: 11px; }
        .header-right td.lbl { font-weight: bold; white-space: nowrap; width: 1px; padding-right: 4px; }
        .header-right td.sep { width: 8px; text-align: center; }

        .report-title { text-align: center; font-size: 14px; font-weight: bold; margin: 10px 0 4px; text-transform: uppercase; }
        .report-meta { text-align: center; font-size: 10px; color: #444; margin-bottom: 8px; }

        /* ── Results table ────────────────────────────────────── */
        .results-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .results-table th { background: #f0f0f0; border: 1px solid #aaa; padding: 5px 6px; font-size: 11px; font-weight: bold; text-align: left; }
        .results-table td { border: 1px solid #ccc; padding: 4px 6px; font-size: 11px; vertical-align: middle; }
        .results-table td.result { text-align: center; font-weight: bold; }
        .results-table td.unit { text-align: center; }
        .results-table td.date { text-align: center; color: #555; }
        .pending { color: #999; font-style: italic; }

        /* ── Notes / footer ───────────────────────────────────── */
        .notes-section { margin-top: 10px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 10px; }
        .footer { margin-top: 16px; font-size: 9px; color: #666; }
    </style>
</head>
<body>

    <!-- ── Header ──────────────────────────────────────────────── -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/logo1.png') }}" alt="Logo" />
            <div class="addr">
                #National Road 6A, Salakonseng Village, Sangkat Svay Dangkum, Siem Reap, Kingdom Of Cambodia.<br>
                Tel: (855) 31 3 5555 88 or (855) 12 881 307 &nbsp;|&nbsp; E-mail: angkorfhospital@gmail.com
            </div>
        </div>
        <div class="header-right">
            <table>
                <tr>
                    <td class="lbl">Name</td><td class="sep">:</td>
                    <td class="val" style="font-weight:bold;">{{ strtoupper($patientInfo['name'] ?: '—') }}</td>
                    <td class="lbl" style="padding-left:10px;">Age</td><td class="sep">:</td>
                    <td class="val">{{ $patientInfo['age'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">HN</td><td class="sep">:</td>
                    <td class="val">{{ $patientInfo['hn'] ?? '—' }}</td>
                    <td class="lbl" style="padding-left:10px;">Sex</td><td class="sep">:</td>
                    <td class="val">{{ $patientInfo['sex'] ? strtoupper($patientInfo['sex']) : '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Clinic</td><td class="sep">:</td>
                    <td class="val">ANGKOR-F CLINIC</td>
                    <td class="lbl" style="padding-left:10px;">DOB</td><td class="sep">:</td>
                    <td class="val">{{ $patientInfo['dob'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Order No</td><td class="sep">:</td>
                    <td class="val">{{ $order->id }}</td>
                    <td class="lbl" style="padding-left:10px;">Ordered</td><td class="sep">:</td>
                    <td class="val">{{ $order->ordered_at?->format('d/m/Y') ?? $order->created_at?->format('d/m/Y') ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Clinician</td><td class="sep">:</td>
                    <td class="val" colspan="3">{{ $patientInfo['doctor_name'] ?? '—' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="report-title">Laboratory Results</div>
    <div class="report-meta">Generated {{ now()->format('d/m/Y H:i') }}</div>

    <!-- ── Results Table ────────────────────────────────────────── -->
    <table class="results-table">
        <thead>
            <tr>
                <th style="width:32%">Test</th>
                <th style="width:16%">Result</th>
                <th style="width:12%">Unit</th>
                <th style="width:26%">Notes</th>
                <th style="width:14%">Completed</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item->inventory?->item_name ?? $item->item_name ?? 'Unknown Test' }}</td>
                    @if ($item->result_value !== null && $item->result_value !== '')
                        <td class="result">{{ $item->result_value }}</td>
                    @else
                        <td class="result pending">Pending</td>
                    @endif
                    <td class="unit">{{ $item->result_unit ?? '—' }}</td>
                    <td>{{ $item->result_notes ?? '' }}</td>
                    <td class="date">{{ $item->completed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($order->notes)
        <div class="notes-section">
            <strong>Order notes:</strong> {{ $order->notes }}
        </div>
    @endif

    <div class="footer">
        This document was generated automatically from lab results entered in the clinic system.
    </div>

</body>
</html>
