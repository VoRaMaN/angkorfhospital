<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Summary of OPU Report</title>
    <style>
        @page { margin: 8mm 10mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; line-height: 1.4; color: #000; }

        /* Header */
        .header { display: table; width: 100%; margin-bottom: 9px; }
        .header-left { display: table-cell; vertical-align: top; width: 42%; }
        .header-left img { width: 58px; float: left; margin-right: 6px; }
        .hosp-name { font-weight: bold; font-size: 13px; padding-top: 4px; }
        .hosp-info { font-size: 6.5px; color: #333; }
        .header-title { display: table-cell; vertical-align: middle; width: 58%; text-align: center; }
        .header-title p { font-weight: bold; font-size: 14px; text-decoration: underline; }

        /* Bordered section boxes */
        .box { border: 1.2px solid #222; border-radius: 8px; padding: 7px 10px; margin-bottom: 9px; }
        .box-title { text-align: center; font-weight: bold; font-size: 11px; margin-bottom: 5px; }

        /* Generic column layouts */
        .cols { display: table; width: 100%; table-layout: fixed; }
        .col { display: table-cell; vertical-align: top; padding-right: 8px; }
        .col:last-child { padding-right: 0; }

        .grp-title { font-weight: bold; font-size: 10px; margin-bottom: 3px; }

        .ln { font-size: 9px; margin-bottom: 3px; }
        .ln .l { color: #000; }
        .ln .v { font-weight: bold; }

        /* Sperm preparation rows with unit hints */
        .srow { display: table; width: 100%; margin-bottom: 3px; }
        .srow .sl { display: table-cell; width: 46%; font-size: 9px; }
        .srow .sv { display: table-cell; font-size: 9px; font-weight: bold; }
        .srow .su { display: table-cell; width: 26%; text-align: right; font-size: 8px; color: #444; }

        /* Checkbox */
        .cb { display: inline-block; width: 9px; height: 9px; border: 1px solid #000; font-size: 8px; line-height: 9px; text-align: center; vertical-align: middle; margin-right: 3px; }

        /* Embryo development numbered grid */
        .dev-meta { display: table; width: 100%; font-size: 9px; margin-bottom: 2px; }
        .dev-meta .dm { display: table-cell; }
        .dev-meta .v { font-weight: bold; }
        .embryo-grid { display: table; width: 100%; table-layout: fixed; }
        .embryo-col { display: table-cell; vertical-align: top; padding-right: 6px; }
        .embryo-row { font-size: 9px; line-height: 1.75; }
        .embryo-idx { display: inline-block; width: 16px; color: #555; }
        .embryo-row .v { font-weight: bold; }

        /* Remark + signatures */
        .remark-ln { font-size: 9px; margin-top: 4px; }
        .sig-row { display: table; width: 100%; margin-top: 6px; }
        .sig-cell { display: table-cell; width: 50%; font-size: 9px; }
        .sig-cell.right { text-align: right; }
        .sig-cell .v { font-weight: bold; }

        /* Grading */
        .grading-images { display: table; width: 100%; table-layout: fixed; margin: 3px 0 5px; }
        .grading-img-col { display: table-cell; text-align: center; }
        .grading-img-col p { font-size: 8px; color: #333; margin-bottom: 2px; }
        .grading-img-col img { width: 62px; height: 62px; object-fit: contain; }
        .grading-legend { display: table; width: 100%; font-size: 8.5px; }
        .grading-legend .gl { display: table-cell; vertical-align: top; }
        .grading-legend .gl.label { width: 17%; font-weight: bold; }
        .grading-legend p { margin: 1px 0; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/logo1.png') }}" alt="Angkor F Hospital" />
            <p class="hosp-name">Angkor-F Hospital</p>
            <p class="hosp-info">#National Road 6A, Salakonseng Village, Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
            <p class="hosp-info">Tel: (855) 31 3 5555 88 | (855) 12 881 307 &nbsp; E-mail: angkorfhospital@gmail.com</p>
        </div>
        <div class="header-title">
            <p>Summary of OPU Report</p>
        </div>
    </div>

    <!-- Patient info box -->
    <div class="box">
        <div class="cols">
            <div class="col">
                <p class="grp-title">(Female)</p>
                <div class="ln"><span class="l">Name :</span> <span class="v">{{ $report['female_patient_name'] ?? '' }}</span></div>
                <div class="ln"><span class="l">H.N. :</span> <span class="v">{{ $report['female_hn'] ?? '' }}</span></div>
                <div class="ln"><span class="l">DOB :</span> <span class="v">{{ $report['female_dob'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Procedure :</span> <span class="v">{{ $report['procedure'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Doctor :</span> <span class="v">{{ $report['doctor_name'] ?? '' }}</span></div>
            </div>
            <div class="col">
                <p class="grp-title">(Male)</p>
                <div class="ln"><span class="l">Name :</span> <span class="v">{{ $report['male_patient_name'] ?? '' }}</span></div>
                <div class="ln"><span class="l">H.N. :</span> <span class="v">{{ $report['male_hn'] ?? '' }}</span></div>
                <div class="ln"><span class="l">DOB :</span> <span class="v">{{ $report['male_dob'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Date&amp;time of OPU :</span> <span class="v">{{ $report['opu_datetime'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Report date :</span> <span class="v">{{ $reportDate }}</span></div>
            </div>
        </div>
    </div>

    <!-- Main data box: Maturation | Fertilization | Sperm preparation | Embryo Freezing -->
    <div class="box">
        <div class="cols">
            <div class="col" style="width: 25%;">
                <p class="grp-title">Maturation</p>
                @php
                    $opuTotal = ($report['no_of_opu_right'] !== null || $report['no_of_opu_left'] !== null)
                        ? (int) ($report['no_of_opu_right'] ?? 0) + (int) ($report['no_of_opu_left'] ?? 0)
                        : null;
                @endphp
                <div class="ln"><span class="l">No of OPU</span> <span class="v">(Rt = {{ $report['no_of_opu_right'] ?? ' ' }}, Lt = {{ $report['no_of_opu_left'] ?? ' ' }}){{ $opuTotal !== null ? ' = '.$opuTotal : '' }}</span></div>
                <div class="ln"><span class="l">Date&amp;Time :</span> <span class="v">{{ $report['maturation_datetime'] ?? '' }}</span></div>
                <div class="ln"><span class="l">M II :</span> <span class="v">{{ $report['m_ii'] ?? '' }}</span></div>
                <div class="ln"><span class="l">M I :</span> <span class="v">{{ $report['m_i'] ?? '' }}</span></div>
                <div class="ln"><span class="l">GV :</span> <span class="v">{{ $report['gv'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Post mature :</span> <span class="v">{{ $report['post_mature'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Abnormal :</span> <span class="v">{{ $report['maturation_abnormal'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Dead :</span> <span class="v">{{ $report['maturation_dead'] ?? '' }}</span></div>
            </div>
            <div class="col" style="width: 21%;">
                <p class="grp-title">Fertilization</p>
                <div class="ln"><span class="l">Date&amp;Time :</span> <span class="v">{{ $report['fertilization_datetime'] ?? '' }}</span></div>
                <div class="ln"><span class="l">2 PN :</span> <span class="v">{{ $report['pn2'] ?? '' }}</span></div>
                <div class="ln"><span class="l">1 PN :</span> <span class="v">{{ $report['pn1'] ?? '' }}</span></div>
                <div class="ln"><span class="l">3 PN :</span> <span class="v">{{ $report['pn3'] ?? '' }}</span></div>
                <div class="ln"><span class="l">4 PN :</span> <span class="v">{{ $report['pn4'] ?? '' }}</span></div>
                <div class="ln"><span class="l">No PN :</span> <span class="v">{{ $report['no_pn'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Dead :</span> <span class="v">{{ $report['fert_dead'] ?? '' }}</span></div>
            </div>
            <div class="col" style="width: 30%;">
                <p class="grp-title" style="text-align:center;">Sperm preparation</p>
                <div class="srow"><span class="sl">Date&amp;Time :</span><span class="sv">{{ $report['sperm_prep_datetime'] ?? '' }}</span><span class="su"></span></div>
                <div class="srow"><span class="sl">Volume :</span><span class="sv">{{ $report['sperm_volume_ml'] ?? '' }}</span><span class="su">(ml.)</span></div>
                <div class="srow"><span class="sl">Count :</span><span class="sv">{{ $report['sperm_count_per_ml'] ?? '' }}</span><span class="su">(x10&#8310;/ml.)</span></div>
                <div class="srow"><span class="sl">Total count :</span><span class="sv">{{ $report['sperm_total_count'] ?? '' }}</span><span class="su">(x10&#8310;)</span></div>
                <div class="srow"><span class="sl">Motile :</span><span class="sv">{{ $report['sperm_motile_per_ml'] ?? '' }}</span><span class="su">(x10&#8310;/ml.)</span></div>
                <div class="srow"><span class="sl">Total motile :</span><span class="sv">{{ $report['sperm_total_motile'] ?? '' }}</span><span class="su">(x10&#8310;)</span></div>
                <div class="srow"><span class="sl">Motility :</span><span class="sv">{{ $report['sperm_motility_pct'] ?? '' }}</span><span class="su">(%)</span></div>
                @php
                    $spermType = strtolower((string) ($report['sperm_type'] ?? ''));
                    $isFrozen = str_contains($spermType, 'froz');
                    $isFresh = $spermType !== '' && ! $isFrozen;
                @endphp
                <div class="ln" style="margin-top:2px;">
                    <span class="cb">{!! $isFresh ? '&#10003;' : '&nbsp;' !!}</span> Fresh sperm
                    &nbsp;&nbsp;
                    <span class="cb">{!! $isFrozen ? '&#10003;' : '&nbsp;' !!}</span> Frozen sperm
                </div>
            </div>
            <div class="col" style="width: 24%;">
                <p class="grp-title">Embryo Freezing</p>
                <div class="ln"><span class="l">Date&amp;Time :</span> <span class="v">{{ $report['embryo_freeze_datetime'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Day :</span> <span class="v">{{ $report['freeze_day'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Stage :</span> <span class="v">{{ $report['freeze_stage'] ?? '' }}</span></div>
                <div class="ln"><span class="l">No. of Embryo :</span> <span class="v">{{ $report['no_of_embryo'] ?? '' }}</span></div>
                <div class="ln"><span class="l">No. of straw :</span> <span class="v">{{ $report['no_of_straw'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Position :</span> <span class="v">{{ $report['freeze_position'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Method :</span> <span class="v">{{ $report['freeze_method'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Freeze media :</span> <span class="v">{{ $report['freeze_media'] ?? '' }}</span></div>
            </div>
        </div>
    </div>

    <!-- Embryo Development (one box per recorded day) -->
    @foreach($report['embryo_developments'] ?? [] as $dev)
    <div class="box">
        <p class="box-title">Embryo Development ( Day {{ $dev['day'] }} )</p>
        <div class="dev-meta">
            <span class="dm"><span class="l">Date&amp;Time :</span> <span class="v">{{ $dev['datetime'] ?? '' }}</span></span>
            <span class="dm" style="text-align:center;"><span class="l">Checked by :</span> <span class="v">{{ $dev['checked_by'] ?? '' }}</span></span>
        </div>
        <div class="embryo-grid">
            @php $embryos = $dev['embryos'] ?? array_fill(0, 20, null); @endphp
            @foreach([[0,1,2,3,4],[5,6,7,8,9],[10,11,12,13,14],[15,16,17,18,19]] as $col)
            <div class="embryo-col">
                @foreach($col as $idx)
                <div class="embryo-row">
                    <span class="embryo-idx">{{ $idx + 1 }}</span>
                    <span class="v">{{ $embryos[$idx] ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <!-- Embryo for ET -->
    <div class="box">
        <p class="box-title">Embryo for ET</p>
        <div class="cols">
            <div class="col">
                <div class="ln"><span class="l">No. of ET :</span> <span class="v">{{ $report['et_no'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Day :</span> <span class="v">{{ $report['et_day'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Date&amp;Time :</span> <span class="v">{{ $report['et_datetime'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Assisted hatching :</span> <span class="v">{{ isset($report['assisted_hatching']) ? ($report['assisted_hatching'] ? 'Yes' : 'No') : '' }}</span></div>
            </div>
            <div class="col">
                <div class="ln"><span class="l">ET Volume :</span> <span class="v">{{ $report['et_volume'] ?? '' }}</span></div>
                <div class="ln"><span class="l">ET Catheter :</span> <span class="v">{{ $report['et_catheter'] ?? '' }}</span></div>
                <div class="ln"><span class="l">ET Doctor :</span> <span class="v">{{ $report['et_doctor'] ?? '' }}</span></div>
                <div class="ln"><span class="l">ET Embryologist :</span> <span class="v">{{ $report['et_embryologist'] ?? '' }}</span></div>
            </div>
            <div class="col">
                <div class="ln"><span class="l">Number of Transfer :</span> <span class="v">{{ $report['number_of_transfer'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Number of Freeze :</span> <span class="v">{{ $report['number_of_freeze'] ?? '' }}</span></div>
                <div class="ln"><span class="l">Number of Discard :</span> <span class="v">{{ $report['number_of_discard'] ?? '' }}</span></div>
            </div>
        </div>
        <div class="remark-ln"><span class="l">Remark :</span> <span class="v">{{ $report['remark'] ?? '' }}</span></div>
        <div class="sig-row">
            <div class="sig-cell">Embryologist report : <span class="v">{{ $report['embryologist_report'] ?? '' }}</span></div>
            <div class="sig-cell right">Embryologist approve: <span class="v">{{ $report['embryologist_approve'] ?? '' }}</span></div>
        </div>
    </div>

    <!-- Embryo Development Grading -->
    <div class="box">
        <p class="box-title">Embryo Development Grading</p>
        <div class="grading-images">
            @foreach([1,2,3,4,5] as $day)
            <div class="grading-img-col">
                <p>Day {{ $day }}</p>
                <img src="{{ public_path("images/embryo-grading/day{$day}.png") }}" alt="Day {{ $day }} embryo" />
            </div>
            @endforeach
        </div>
        <div class="grading-legend">
            <div class="gl label">Embryo Grading :</div>
            <div class="gl">
                <p>g4 = grade 4 (Very good embryo)</p>
                <p>g3 = grade 3 (Good embryo)</p>
            </div>
            <div class="gl">
                <p>g2 = grade 2 (Fair embryo)</p>
                <p>g1 = grade 1 (Not good embryo)</p>
            </div>
            <div class="gl">
                <p>A = Very good</p>
                <p>B = Good</p>
                <p>C = Fair</p>
            </div>
        </div>
    </div>

</body>
</html>
