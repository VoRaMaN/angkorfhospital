<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Income Summary Record - {{ $today }}</title>
    <style>
        @page { margin: 25mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; color: #000; }

        .header { display: table; width: 100%; margin-bottom: 8px; }
        .header-left { display: table-cell; vertical-align: top; width: 55%; }
        .header-left img { width: 70px; margin-bottom: 4px; display: block; }
        .header-left p { font-size: 8.5px; margin: 1px 0; }

        .title { text-align: center; font-weight: bold; font-size: 13px; text-decoration: underline; margin: 6px 0 4px; }
        .range { text-align: center; font-size: 8.5px; margin-bottom: 10px; }

        table.summary { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 6px; }
        table.summary th, table.summary td {
            border: 1px solid #444; padding: 2px 3px; font-size: 6.5px; text-align: left;
            overflow-wrap: break-word; word-break: break-word;
        }
        table.summary th { background: #f0f0f0; font-weight: bold; text-align: center; }
        table.summary td.num { text-align: right; }
        table.summary td.center { text-align: center; }

        .col-no       { width: 3.5%; }
        .col-date     { width: 9.5%; }
        .col-code     { width: 7.5%; }
        .col-name     { width: 12%; }
        .col-sex      { width: 4%; }
        .col-age      { width: 8.5%; }
        .col-ipd      { width: 5.5%; }
        .col-paid     { width: 8.5%; }
        .col-deposit  { width: 8%; }
        .col-paidby   { width: 8.5%; }
        .col-pttype   { width: 6.5%; }
        .col-inv      { width: 8%; }
        .col-cashier  { width: 10%; }

        .totals-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .totals-table td { border: 1px solid #444; padding: 3px 6px; font-size: 9px; }
        .totals-table td.label { font-weight: bold; width: 85%; }
        .totals-table td.value { text-align: right; }

        .payment-summary-title { font-weight: bold; font-size: 10px; margin-bottom: 3px; }
        table.payment-summary { width: 55%; border-collapse: collapse; margin-bottom: 20px; }
        table.payment-summary th, table.payment-summary td { border: 1px solid #444; padding: 3px 6px; font-size: 8.5px; }
        table.payment-summary th { background: #f0f0f0; text-align: left; }
        table.payment-summary td.num { text-align: right; }

        .sig-row { display: table; width: 100%; margin-top: 45mm; }
        .sig-cell { display: table-cell; width: 50%; font-size: 9.5px; vertical-align: top; }
        .sig-cell p { margin: 2px 0; }
    </style>
</head>
<body>

    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/logo1.png') }}" alt="Angkor-F Hospital">
            <p><strong>Angkor F Hospital</strong></p>
            <p>#National Road 6A, Salakonseng Village, Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
            <p>Tel: (855) 31 3 5555 88 | (855) 12 881 307</p>
        </div>
    </div>

    <p class="title">INCOME SUMMARY RECORD</p>
    <p class="range">From: {{ $today }} 00:00:00 &nbsp;&nbsp; To: {{ $today }} 23:59:59</p>

    <table class="summary">
        <colgroup>
            <col class="col-no"><col class="col-date"><col class="col-code"><col class="col-name">
            <col class="col-sex"><col class="col-age"><col class="col-ipd"><col class="col-paid">
            <col class="col-deposit"><col class="col-paidby"><col class="col-pttype"><col class="col-inv">
            <col class="col-cashier">
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th>Date</th>
                <th>Patient Code</th>
                <th>Patient Name</th>
                <th>Sex</th>
                <th>Age</th>
                <th>IPD/OPD</th>
                <th>Paid</th>
                <th>Deposit</th>
                <th>Paid By</th>
                <th>Pt Type</th>
                <th>Inv No.</th>
                <th>Cashier</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td class="center">{{ $row['no'] }}</td>
                <td>{{ $row['datetime'] }}</td>
                <td>{{ $row['patient_code'] }}</td>
                <td>{{ $row['patient_name'] }}</td>
                <td class="center">{{ $row['sex'] }}</td>
                <td>{{ $row['age'] }}</td>
                <td class="center">OPD</td>
                <td class="num">{{ number_format($row['paid'], 2) }}</td>
                <td class="num">0.00</td>
                <td>{{ $row['payment_method'] }}</td>
                <td>{{ $row['patient_type'] }}</td>
                <td>{{ $row['inv_no'] }}</td>
                <td>{{ $row['cashier'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="13" class="center">No paid bills recorded today.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td class="label">TOTAL</td>
            <td class="value">USD {{ number_format($grandTotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">TOTAL REFUND</td>
            <td class="value">USD 0.00</td>
        </tr>
        <tr>
            <td class="label">GRAND TOTAL</td>
            <td class="value">USD {{ number_format($grandTotal, 2) }}</td>
        </tr>
    </table>

    <p class="payment-summary-title">Total Summary</p>
    <table class="payment-summary">
        <thead>
            <tr>
                <th>Payment Method</th>
                <th>Currency</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paymentSummary as $summary)
            <tr>
                <td>{{ $summary['payment_method'] }}</td>
                <td>{{ $summary['currency'] }}</td>
                <td class="num">{{ number_format($summary['amount'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3">—</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="sig-row">
        <div class="sig-cell">
            <p>Close By {{ $closedBy ?: '_________________________' }}</p>
            <p>Date {{ $today }}</p>
        </div>
        <div class="sig-cell">
            <p>Received By _________________________</p>
            <p>Date {{ $today }}</p>
        </div>
    </div>

</body>
</html>
