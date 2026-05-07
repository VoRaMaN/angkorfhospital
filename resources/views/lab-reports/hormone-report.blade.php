<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hormone Report</title>
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
        .header-right td.val { }

        /* ── Main results table ───────────────────────────────── */
        .results-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .results-table th { background: #f0f0f0; border: 1px solid #aaa; padding: 5px 6px; font-size: 11px; font-weight: bold; text-align: left; }
        .results-table td { border: 1px solid #ccc; padding: 4px 6px; font-size: 11px; vertical-align: middle; }
        .results-table tr.section-row td { font-weight: bold; background: #fafafa; padding: 4px 6px; }
        .results-table tr.specimen-row td { font-weight: bold; font-size: 11px; }
        .results-table td.test-name { padding-left: 14px; }
        .results-table td.result { text-align: center; font-weight: bold; }
        .results-table td.unit  { text-align: center; }
        .results-table td.ref   { text-align: center; color: #555; }

        /* ── Signature row ────────────────────────────────────── */
        .sig-section { margin-top: 14px; }
        .sig-row { display: table; width: 100%; }
        .sig-cell { display: table-cell; width: 50%; vertical-align: top; font-size: 11px; }
        .sig-cell .sig-label { font-weight: bold; }
        .sig-cell .sig-name  { border-bottom: 1px solid #000; display: inline-block; min-width: 160px; font-weight: bold; }
        .sig-cell .sig-date  { margin-top: 3px; font-size: 10px; }

        /* ── Remark ───────────────────────────────────────────── */
        .remark-section { margin-top: 10px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 10px; }
        .remark-section p { margin: 2px 0; }

        /* ── Reference range section ──────────────────────────── */
        .ref-section { margin-top: 18px; page-break-inside: avoid; }
        .ref-title { background: #3b7abf; color: #fff; text-align: center; font-weight: bold; font-size: 12px; padding: 6px 0; }
        .ref-table { width: 100%; border-collapse: collapse; margin-top: 0; }
        .ref-table th, .ref-table td { border: 1px solid #aaa; padding: 4px 5px; font-size: 9.5px; text-align: center; }
        .ref-table th { background: #ddeeff; font-weight: bold; }
        .ref-table td.row-label { text-align: left; font-weight: bold; }
        .ref-table th.women-header { background: #c8dff8; }
        .ref-table th.men-header   { background: #ddeeff; }
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
                    <td class="val" style="font-weight:bold;">{{ strtoupper($report->patient_name ?? '—') }}</td>
                    <td class="lbl" style="padding-left:10px;">Age</td><td class="sep">:</td>
                    <td class="val">{{ $report->patient_age ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">HN</td><td class="sep">:</td>
                    <td class="val">{{ $report->patient_hn ?? '—' }}</td>
                    <td class="lbl" style="padding-left:10px;">Sex</td><td class="sep">:</td>
                    <td class="val">{{ $report->patient_sex ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Clinic</td><td class="sep">:</td>
                    <td class="val">ANGKOR-F CLINIC</td>
                    <td class="lbl" style="padding-left:10px;">DOB</td><td class="sep">:</td>
                    <td class="val">{{ $report->patient_dob ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Collection Date</td><td class="sep">:</td>
                    <td class="val">{{ $report->collection_date ?? '—' }}</td>
                    <td class="lbl" style="padding-left:10px;">Collection Time</td><td class="sep">:</td>
                    <td class="val">{{ $report->collection_time ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Received Date</td><td class="sep">:</td>
                    <td class="val">{{ $report->received_date ?? '—' }}</td>
                    <td class="lbl" style="padding-left:10px;">Received Time</td><td class="sep">:</td>
                    <td class="val">{{ $report->received_time ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Clinician</td><td class="sep">:</td>
                    <td class="val" colspan="3">{{ $report->doctor_name ?? '—' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ── Results Table ────────────────────────────────────────── -->
    <table class="results-table">
        <thead>
            <tr>
                <th style="width:30%">Test</th>
                <th style="width:15%">Method</th>
                <th style="width:15%">Result</th>
                <th style="width:12%">Unit</th>
                <th style="width:28%">Reference range</th>
            </tr>
        </thead>
        <tbody>
            <tr class="specimen-row">
                <td colspan="5"><strong>Specimen :</strong> &nbsp; {{ $report->specimen ?? 'Serum' }}</td>
            </tr>
            <tr class="section-row">
                <td colspan="5" style="text-decoration:underline;">Hormone</td>
            </tr>
            @php
                $tests = [
                    ['LH',           'lh',           'mIU/mL'],
                    ['FSH',          'fsh',           'mIU/mL'],
                    ['Prolactin',    'prolactin',     'ng/mL'],
                    ['Estradiol',    'estradiol',     'pg/mL'],
                    ['Progesterone', 'progesterone',  'ng/mL'],
                    ['Testosterone', 'testosterone',  'ng/mL'],
                    ['TSH',          'tsh',           'mIU/L'],
                    ['AMH',          'amh',           'ng/mL'],
                ];
            @endphp
            @foreach ($tests as [$label, $field, $unit])
            <tr>
                <td class="test-name">{{ $label }}</td>
                <td>ELFA</td>
                <td class="result">
                    @if (!is_null($report->$field) && $report->$field !== '')
                        {{ $report->$field }}
                    @else
                        -
                    @endif
                </td>
                <td class="unit">{{ $unit }}</td>
                <td class="ref">See below</td>
            </tr>
            @endforeach
            {{-- Beta-hCG spacer row --}}
            <tr><td colspan="5" style="padding:2px;border:none;"></td></tr>
            <tr>
                <td class="test-name">Beta - hCG</td>
                <td>ELFA</td>
                <td class="result">
                    @if (!is_null($report->beta_hcg) && $report->beta_hcg !== '')
                        {{ $report->beta_hcg }}
                    @else
                        -
                    @endif
                </td>
                <td class="unit">mIU/mL</td>
                <td class="ref">See below</td>
            </tr>
        </tbody>
    </table>

    <!-- ── Signatures ───────────────────────────────────────────── -->
    <div class="sig-section">
        <div class="sig-row">
            <div class="sig-cell">
                <span class="sig-label">Reported by : </span>
                <span class="sig-name">{{ $report->reported_by ?? '' }}</span><br>
                <span class="sig-date">
                    Date : {{ $report->reported_date ?? '' }}
                    &nbsp;&nbsp; Time : {{ $report->reported_time ?? '' }}
                </span>
            </div>
            <div class="sig-cell">
                <span class="sig-label">Approve by : </span>
                <span class="sig-name">{{ $report->approved_by ?? '' }}</span><br>
                <span class="sig-date">
                    Date : {{ $report->approved_date ?? '' }}
                    &nbsp;&nbsp; Time : {{ $report->approved_time ?? '' }}
                </span>
            </div>
        </div>
    </div>

    <!-- ── Remark ────────────────────────────────────────────────── -->
    <div class="remark-section">
        <p><strong>Remark :</strong> &nbsp; L = Lower than Reference range &nbsp;&nbsp;&nbsp; Repeated = Confirmatory Repeated</p>
        <p style="padding-left:60px;">H = Higher than Reference range</p>
        @if($report->remark)
        <p style="margin-top:4px;">{{ $report->remark }}</p>
        @endif
        <p style="margin-top:6px;"><em>This report certify the specimen analyed only</em></p>
    </div>

    <!-- ── Reference Range Table ─────────────────────────────────── -->
    <div class="ref-section">
        <div class="ref-title">Reference Range of Fertility Hormone</div>
        <table class="ref-table">
            <thead>
                <tr>
                    <th rowspan="3" style="width:14%">TEST</th>
                    <th rowspan="3" style="width:13%">MEN</th>
                    <th colspan="4" class="women-header">WOMEN</th>
                </tr>
                <tr>
                    <th colspan="2" class="women-header">Follicular phase</th>
                    <th rowspan="2" class="women-header">Ovulation<br>Day 0</th>
                    <th rowspan="2" class="women-header">Luteal phase<br>Day +3 to +15</th>
                </tr>
                <tr>
                    <th class="women-header">1 st Half<br>Day -15 to -9</th>
                    <th class="women-header">2nd Half<br>Day -8 to -2</th>
                </tr>
                <tr>
                    <th></th><th></th>
                    <th colspan="2"></th>
                    <th></th>
                    <th>Menopausal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="row-label">LH (mIU/mL)</td>
                    <td>1.70-8.60</td>
                    <td colspan="2">2.95-13.65</td>
                    <td>13.65-95.75</td>
                    <td>1.25-11.00</td>
                </tr>
                <tr>
                    <td class="row-label">FSH (mIU/mL)</td>
                    <td>1.50-12.40</td>
                    <td colspan="2">4.46-12.43</td>
                    <td>4.88-20.96</td>
                    <td>1.96-7.70</td>
                </tr>
                <tr>
                    <td class="row-label">Prolactin (ng/mL)</td>
                    <td>3.45-17.42</td>
                    <td colspan="4">4.6-25.07</td>
                </tr>
                <tr>
                    <td class="row-label">Estradiol (pg/mL)</td>
                    <td>&lt;85</td>
                    <td colspan="2">12-262</td>
                    <td>40-396</td>
                    <td>21-381</td>
                </tr>
                <tr>
                    <td class="row-label" style="font-size:9px;">Progesterone (ng/mL)</td>
                    <td>0.2-1.5</td>
                    <td colspan="2">0.2-2.0</td>
                    <td>0.7-3.5</td>
                    <td>3.0-30.0</td>
                </tr>
                <tr>
                    <td class="row-label">TSH</td>
                    <td colspan="5">0.3-4.2</td>
                </tr>
                <tr>
                    <td class="row-label">AMH</td>
                    <td style="font-size:9px;">(20y-60y) 0.92-13.89</td>
                    <td colspan="2" style="font-size:9px;">(20-29y) 0.88-10.35</td>
                    <td colspan="2" style="font-size:9px;">(30-39y) 0.31-7.86 &nbsp; (40-50y) &lt;5.07</td>
                </tr>
                <tr>
                    <td class="row-label">Testosterone</td>
                    <td style="font-size:9px;">20y-49y (1.61-8.41)<br>≥50y &lt;0.61</td>
                    <td colspan="4" style="font-size:9px;">20-29y ≤ 0.80 &nbsp;&nbsp; ≥50years &lt;0.71</td>
                </tr>
                <tr>
                    <td class="row-label" style="font-size:9px;">Beta-hCG (mIU/mL)</td>
                    <td>Negative<br>0.5</td>
                    <td colspan="2">1st - 2nd week<br>25-1000</td>
                    <td>2nd Trimester<br>10000-30,000</td>
                    <td>2nd - 3rd month<br>30,000-100,000</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>
