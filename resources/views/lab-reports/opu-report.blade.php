<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OPU Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #000; padding: 20px 30px; }

        .header { display: table; width: 100%; margin-bottom: 8px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header-left { display: table-cell; vertical-align: top; width: 55%; }
        .header-left img { width: 100px; display: block; margin-bottom: 3px; }
        .header-left p { font-size: 9px; margin: 1px 0; color: #333; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; width: 45%; font-size: 10px; }
        .header-right p { margin: 2px 0; }
        .header-right .label { font-weight: bold; }

        .lab-title { text-align: center; margin: 4px 0 8px; }
        .lab-title p { font-weight: bold; font-size: 12px; }
        .lab-title .sub { font-size: 11px; font-weight: normal; }

        .section-label { font-weight: bold; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; color: #555; border-bottom: 1px solid #aaa; padding-bottom: 2px; margin-bottom: 4px; margin-top: 7px; }

        .two-col { display: table; width: 100%; }
        .col-l { display: table-cell; width: 50%; vertical-align: top; padding-right: 8px; }
        .col-r { display: table-cell; width: 50%; vertical-align: top; }

        .three-col { display: table; width: 100%; }
        .col-third { display: table-cell; width: 33.33%; vertical-align: top; padding-right: 6px; }

        .field { margin-bottom: 2px; font-size: 9px; }
        .field .lbl { font-weight: bold; }

        table.grid { width: 100%; border-collapse: collapse; font-size: 9px; }
        table.grid th { text-align: center; background: #f0f0f0; padding: 2px 3px; border: 1px solid #ccc; font-size: 8px; }
        table.grid td { text-align: center; padding: 2px 3px; border: 1px solid #ccc; }
        table.grid td.left { text-align: left; padding-left: 4px; }

        .embryo-grid { display: table; width: 100%; }
        .embryo-col { display: table-cell; width: 25%; vertical-align: top; padding-right: 4px; }
        .embryo-row { font-size: 9px; padding: 1px 0; }
        .embryo-idx { display: inline-block; width: 14px; color: #888; }

        .sig-row { display: table; width: 100%; margin-top: 8px; padding-top: 5px; border-top: 1px solid #ccc; font-size: 9px; }
        .sig-cell { display: table-cell; width: 33%; }
        .sig-cell .sig-label { font-weight: bold; font-size: 8px; color: #555; text-transform: uppercase; margin-bottom: 2px; }

        .remark { margin-top: 5px; font-size: 9px; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/logo1.png') }}" alt="Angkor-F Hospital" />
            <p>#National Road 6A, Salakonseng Village,</p>
            <p>Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
            <p>Tel: (855) 31 3 5555 88 | (855) 12 881 307</p>
            <p>E-mail: angkorfhospital@gmail.com</p>
        </div>
        <div class="header-right">
            <p><span class="label">Date:</span> {{ $reportDate }}</p>
            <p><span class="label">Doctor:</span> {{ $report['doctor_name'] ?? '—' }}</p>
            <p><span class="label">Procedure:</span> {{ $report['procedure'] ?? '—' }}</p>
            <p><span class="label">OPU Date/Time:</span> {{ $report['opu_datetime'] ?? '—' }}</p>
        </div>
    </div>

    <!-- Lab Title -->
    <div class="lab-title">
        <p>IVF LAB</p>
        <p class="sub">( OPU Report )</p>
    </div>

    <!-- Patient Info -->
    <p class="section-label">Patient Information</p>
    <div class="two-col">
        <div class="col-l">
            <p style="font-size:8px;font-weight:bold;color:#666;margin-bottom:2px;">FEMALE PATIENT</p>
            <div class="field"><span class="lbl">Name:</span> {{ $report['female_patient_name'] ?? '—' }}</div>
            <div class="field"><span class="lbl">H.N:</span> {{ $report['female_hn'] ?? '—' }}</div>
            <div class="field"><span class="lbl">DOB:</span> {{ $report['female_dob'] ?? '—' }}</div>
        </div>
        <div class="col-r">
            <p style="font-size:8px;font-weight:bold;color:#666;margin-bottom:2px;">MALE PATIENT (PARTNER)</p>
            <div class="field"><span class="lbl">Name:</span> {{ $report['male_patient_name'] ?? '—' }}</div>
            <div class="field"><span class="lbl">H.N:</span> {{ $report['male_hn'] ?? '—' }}</div>
            <div class="field"><span class="lbl">DOB:</span> {{ $report['male_dob'] ?? '—' }}</div>
        </div>
    </div>

    <!-- OPU Egg Count + Maturation -->
    <div class="two-col">
        <div class="col-l">
            <p class="section-label">OPU Egg Count</p>
            <div class="field"><span class="lbl">Right Ovary:</span> {{ $report['no_of_opu_right'] ?? '—' }}</div>
            <div class="field"><span class="lbl">Left Ovary:</span> {{ $report['no_of_opu_left'] ?? '—' }}</div>
        </div>
        <div class="col-r">
            <p class="section-label">Maturation Check</p>
            <div class="field" style="margin-bottom:3px;">{{ $report['maturation_datetime'] ?? '—' }}</div>
            <table class="grid">
                <thead>
                    <tr><th>MII</th><th>MI</th><th>GV</th><th>Post-Mature</th><th>Abnormal</th><th>Dead</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $report['m_ii'] ?? '—' }}</td>
                        <td>{{ $report['m_i'] ?? '—' }}</td>
                        <td>{{ $report['gv'] ?? '—' }}</td>
                        <td>{{ $report['post_mature'] ?? '—' }}</td>
                        <td>{{ $report['maturation_abnormal'] ?? '—' }}</td>
                        <td>{{ $report['maturation_dead'] ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fertilization + Sperm Prep -->
    <div class="two-col">
        <div class="col-l">
            <p class="section-label">Fertilization Check</p>
            <div class="field" style="margin-bottom:3px;">{{ $report['fertilization_datetime'] ?? '—' }}</div>
            <table class="grid">
                <thead>
                    <tr><th>2PN</th><th>1PN</th><th>3PN</th><th>4PN</th><th>0PN</th><th>Dead</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $report['pn2'] ?? '—' }}</td>
                        <td>{{ $report['pn1'] ?? '—' }}</td>
                        <td>{{ $report['pn3'] ?? '—' }}</td>
                        <td>{{ $report['pn4'] ?? '—' }}</td>
                        <td>{{ $report['no_pn'] ?? '—' }}</td>
                        <td>{{ $report['fert_dead'] ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-r">
            <p class="section-label">Sperm Preparation</p>
            <div class="field" style="margin-bottom:3px;">{{ $report['sperm_prep_datetime'] ?? '—' }}</div>
            <table class="grid">
                <thead>
                    <tr><th>Type</th><th>Vol(ml)</th><th>Cnt/ml(M)</th><th>Tot(M)</th><th>Motile/ml</th><th>TotMot</th><th>Mot%</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $report['sperm_type'] ?? '—' }}</td>
                        <td>{{ $report['sperm_volume_ml'] ?? '—' }}</td>
                        <td>{{ $report['sperm_count_per_ml'] ?? '—' }}</td>
                        <td>{{ $report['sperm_total_count'] ?? '—' }}</td>
                        <td>{{ $report['sperm_motile_per_ml'] ?? '—' }}</td>
                        <td>{{ $report['sperm_total_motile'] ?? '—' }}</td>
                        <td>{{ $report['sperm_motility_pct'] ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Embryo Freezing -->
    <p class="section-label">Embryo Freezing</p>
    <div class="field" style="margin-bottom:3px;">{{ $report['embryo_freeze_datetime'] ?? '—' }}</div>
    <table class="grid">
        <thead>
            <tr><th>Day</th><th>Stage</th><th>No. Embryo</th><th>No. Straw</th><th>Position</th><th>Method</th><th>Media</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $report['freeze_day'] ?? '—' }}</td>
                <td>{{ $report['freeze_stage'] ?? '—' }}</td>
                <td>{{ $report['no_of_embryo'] ?? '—' }}</td>
                <td>{{ $report['no_of_straw'] ?? '—' }}</td>
                <td>{{ $report['freeze_position'] ?? '—' }}</td>
                <td>{{ $report['freeze_method'] ?? '—' }}</td>
                <td>{{ $report['freeze_media'] ?? '—' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Day 3 Embryos -->
    <p class="section-label">Embryo Development (Day 3)</p>
    <div class="field" style="margin-bottom:3px;">
        <span class="lbl">Date/Time:</span> {{ $report['day3_datetime'] ?? '—' }}
        @if(!empty($report['day3_checked_by'])) &nbsp; <span class="lbl">Checked by:</span> {{ $report['day3_checked_by'] }} @endif
    </div>
    <div class="embryo-grid">
        @php $day3 = $report['day3_embryos'] ?? array_fill(0, 20, null); @endphp
        @foreach([[0,1,2,3,4],[5,6,7,8,9],[10,11,12,13,14],[15,16,17,18,19]] as $col)
        <div class="embryo-col">
            @foreach($col as $idx)
            <div class="embryo-row">
                <span class="embryo-idx">{{ $idx + 1 }}.</span>
                {{ $day3[$idx] ?? '—' }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <!-- Day 5 Embryos -->
    <p class="section-label">Embryo Development (Day 5)</p>
    <div class="field" style="margin-bottom:3px;">
        <span class="lbl">Date/Time:</span> {{ $report['day5_datetime'] ?? '—' }}
        @if(!empty($report['day5_checked_by'])) &nbsp; <span class="lbl">Checked by:</span> {{ $report['day5_checked_by'] }} @endif
    </div>
    <div class="embryo-grid">
        @php $day5 = $report['day5_embryos'] ?? array_fill(0, 20, null); @endphp
        @foreach([[0,1,2,3,4],[5,6,7,8,9],[10,11,12,13,14],[15,16,17,18,19]] as $col)
        <div class="embryo-col">
            @foreach($col as $idx)
            <div class="embryo-row">
                <span class="embryo-idx">{{ $idx + 1 }}.</span>
                {{ $day5[$idx] ?? '—' }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <!-- Embryo for ET -->
    <p class="section-label">Embryo for ET (Embryo Transfer)</p>
    <div class="two-col">
        <div class="col-l">
            <div class="field"><span class="lbl">No. of ET:</span> {{ $report['et_no'] ?? '—' }}</div>
            <div class="field"><span class="lbl">Day:</span> {{ $report['et_day'] ?? '—' }}</div>
            <div class="field"><span class="lbl">Date &amp; Time:</span> {{ $report['et_datetime'] ?? '—' }}</div>
            <div class="field"><span class="lbl">ET Volume:</span> {{ $report['et_volume'] ?? '—' }}</div>
            <div class="field"><span class="lbl">ET Catheter:</span> {{ $report['et_catheter'] ?? '—' }}</div>
        </div>
        <div class="col-r">
            <div class="field"><span class="lbl">No. of Transfer:</span> {{ $report['number_of_transfer'] ?? '—' }}</div>
            <div class="field"><span class="lbl">No. of Freeze:</span> {{ $report['number_of_freeze'] ?? '—' }}</div>
            <div class="field"><span class="lbl">No. of Discard:</span> {{ $report['number_of_discard'] ?? '—' }}</div>
            <div class="field"><span class="lbl">Assisted Hatching:</span> {{ isset($report['assisted_hatching']) ? ($report['assisted_hatching'] ? 'Yes' : 'No') : '—' }}</div>
            <div class="field"><span class="lbl">ET Doctor:</span> {{ $report['et_doctor'] ?? '—' }}</div>
            <div class="field"><span class="lbl">ET Embryologist:</span> {{ $report['et_embryologist'] ?? '—' }}</div>
        </div>
    </div>

    <!-- Remark -->
    @if(!empty($report['remark']))
    <div class="remark"><strong>Remark:</strong> {{ $report['remark'] }}</div>
    @endif

    <!-- Signatures -->
    <div class="sig-row">
        <div class="sig-cell">
            <p class="sig-label">Embryologist Report</p>
            <p>{{ $report['embryologist_report'] ?? '—' }}</p>
        </div>
        <div class="sig-cell">
            <p class="sig-label">Embryologist Approve</p>
            <p>{{ $report['embryologist_approve'] ?? '—' }}</p>
        </div>
        <div class="sig-cell"></div>
    </div>

</body>
</html>
