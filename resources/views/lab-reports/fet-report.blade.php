<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Summary of FET Report</title>
    <style>
        @page { margin: 10mm 12mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8.5px; line-height: 1.35; color: #000; }

        /* Header */
        .header { display: table; width: 100%; margin-bottom: 4px; }
        .header-left { display: table-cell; vertical-align: top; width: 42%; }
        .header-left img { width: 44px; float: left; margin-right: 6px; }
        .hosp-name { font-weight: bold; font-size: 12px; padding-top: 2px; }
        .hosp-info { font-size: 6px; color: #333; }
        .header-title { display: table-cell; vertical-align: middle; width: 58%; text-align: center; }
        .header-title p { font-weight: bold; font-size: 13px; text-decoration: underline; }

        .box { border: 1.1px solid #222; border-radius: 4px; padding: 5px 9px; margin-bottom: 5px; }
        .box-title { text-align: center; font-weight: bold; font-size: 10px; margin-bottom: 3px; }

        .cols { display: table; width: 100%; table-layout: fixed; }
        .col { display: table-cell; vertical-align: top; padding-right: 8px; }
        .col:last-child { padding-right: 0; }

        .ln { font-size: 8.5px; margin-bottom: 2px; }
        .ln .l { color: #000; }
        .ln .v { font-weight: bold; }

        /* Day 3 / Day 5 slots */
        .dev-title { font-weight: bold; font-size: 9.5px; text-align: center; margin-bottom: 2px; }
        .slot-row { font-size: 8.5px; margin-bottom: 1px; }
        .slot-idx { display: inline-block; width: 12px; color: #555; }
        .slot-row .v { font-weight: bold; }

        /* Picture grid */
        .pic-meta { font-size: 8.5px; margin-bottom: 3px; }
        .pic-meta .v { font-weight: bold; border-bottom: 1px solid #000; display: inline-block; min-width: 40px; text-align: center; }
        table.pics { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 3px; }
        table.pics td { border: 1px solid #444; height: 78px; text-align: center; vertical-align: middle; padding: 1px; }
        table.pics td .pic-no { font-size: 8px; font-weight: bold; display: block; text-align: left; padding: 0 2px; }
        table.pics td img { max-width: 100%; max-height: 62px; }

        /* Grading */
        .grading-images { display: table; width: 100%; table-layout: fixed; margin: 2px 0 3px; }
        .grading-img-col { display: table-cell; text-align: center; }
        .grading-img-col p { font-size: 7.5px; color: #333; margin-bottom: 1px; }
        .grading-img-col img { width: 40px; height: 40px; object-fit: contain; }
        .grading-legend { display: table; width: 100%; font-size: 7.5px; }
        .grading-legend .gl { display: table-cell; vertical-align: top; }
        .grading-legend .gl.label { width: 16%; font-weight: bold; }
        .grading-legend p { margin: 0; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/logo1.png') }}" alt="Angkor F Hospital" />
            <p class="hosp-name">Angkor-F Hospital</p>
            <p class="hosp-info">#National Road 6A, Salakonseng Village, Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
            <p class="hosp-info">Tel: (855) 31 3 5555 88 | (855) 12 881 307</p>
        </div>
        <div class="header-title">
            <p>Summary of FET Report</p>
        </div>
    </div>

    <!-- Patient box -->
    <div class="box">
        <div class="cols">
            <div class="col">
                <div class="ln"><span class="l">Name (Female) :</span> <span class="v">{{ $report->female_patient_name }}</span></div>
                <div class="ln" style="padding-left:24px;"><span class="l">H.N. :</span> <span class="v">{{ $report->female_hn }}</span></div>
                <div class="ln" style="padding-left:24px;"><span class="l">DOB :</span> <span class="v">{{ $report->female_dob }}</span></div>
                <div class="ln"><span class="l">Procedure :</span> <span class="v">{{ $report->procedure ?? 'FET' }}</span></div>
                <div class="ln"><span class="l">Doctor :</span> <span class="v">{{ $doctorName }}</span></div>
            </div>
            <div class="col">
                <div class="ln"><span class="l">(Male) :</span> <span class="v">{{ $report->male_patient_name }}</span></div>
                <div class="ln" style="padding-left:24px;"><span class="l">H.N. :</span> <span class="v">{{ $report->male_hn }}</span></div>
                <div class="ln" style="padding-left:24px;"><span class="l">DOB :</span> <span class="v">{{ $report->male_dob }}</span></div>
                <div class="ln"><span class="l">Date of FET :</span> <span class="v">{{ $report->fet_date }}</span></div>
            </div>
        </div>
    </div>

    <!-- Embryo Thawing box -->
    <div class="box">
        <p class="box-title">Embryo Thawing</p>
        <div class="cols">
            <div class="col">
                <div class="ln"><span class="l">Date&amp;Time of Freeze :</span> <span class="v">{{ $report->freeze_datetime }}</span></div>
                <div class="ln"><span class="l">No. of freeze :</span> <span class="v">{{ $report->no_of_freeze }}</span></div>
                <div class="ln"><span class="l">Stage of freeze :</span> <span class="v">{{ $report->stage_of_freeze }}</span></div>
            </div>
            <div class="col">
                <div class="ln"><span class="l">Date&amp;Time of thaw :</span> <span class="v">{{ $report->thaw_datetime }}</span></div>
                <div class="ln"><span class="l">No. of Thaw :</span> <span class="v">{{ $report->no_of_thaw }}</span></div>
                <div class="ln"><span class="l">No. of Survival :</span> <span class="v">{{ $report->no_of_survival }}</span></div>
                <div class="ln"><span class="l">No. of Remaining :</span> <span class="v">{{ $report->no_of_remaining }}</span></div>
            </div>
            <div class="col">
                <div class="ln"><span class="l">Thawing Media :</span> <span class="v">{{ $report->thawing_media }}</span></div>
                <div class="ln"><span class="l">Lot No. :</span> <span class="v">{{ $report->lot_no }}</span></div>
                <div class="ln"><span class="l">Exp. :</span> <span class="v">{{ $report->exp_date }}</span></div>
                <div class="ln"><span class="l">Thawing by :</span> <span class="v">{{ $report->thawing_by }}</span></div>
            </div>
        </div>
    </div>

    <!-- Embryo Development Day 3 / Day 5 box -->
    <div class="box">
        <div class="cols">
            <div class="col">
                <p class="dev-title">Embryo Development ( Day 3 )</p>
                <div class="ln"><span class="l">Date &amp; Time :</span> <span class="v">{{ $report->day3_datetime }}</span></div>
                @foreach([1,2,3,4,5] as $n)
                <div class="slot-row"><span class="slot-idx">{{ $n }}</span> <span class="v">{{ $report->{'day3_embryo_'.$n} }}</span></div>
                @endforeach
            </div>
            <div class="col">
                <p class="dev-title">Embryo Development ( Day 5 )</p>
                <div class="ln"><span class="l">Date &amp; Time :</span> <span class="v">{{ $report->day5_datetime }}</span></div>
                @foreach([1,2,3,4,5] as $n)
                <div class="slot-row"><span class="slot-idx">{{ $n }}</span> <span class="v">{{ $report->{'day5_embryo_'.$n} }}</span></div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Picture of Embryo Development box -->
    <div class="box">
        <p class="box-title">Picture of Embryo Development</p>
        <p class="pic-meta">
            Day &nbsp; <span class="v">{{ $report->picture_day }}</span>
            &nbsp;&nbsp; Date &amp;time &nbsp; <span class="v" style="min-width:110px;">{{ $report->picture_datetime }}</span>
        </p>
        @php
            $pictures = collect($report->embryo_pictures ?? [])->take(10)->values();
        @endphp
        @foreach([0, 5] as $rowStart)
        <table class="pics">
            <tr>
                @for($i = $rowStart; $i < $rowStart + 5; $i++)
                @php
                    $pic = $pictures[$i] ?? null;
                    $picPath = $pic['path'] ?? null;
                    $picFile = $picPath ? storage_path('app/private/'.$picPath) : null;
                @endphp
                <td>
                    <span class="pic-no">{{ $pic['no'] ?? '' }}</span>
                    @if($picFile && file_exists($picFile))
                        <img src="{{ $picFile }}" alt="Embryo {{ $pic['no'] ?? $i + 1 }}" />
                    @endif
                </td>
                @endfor
            </tr>
        </table>
        @endforeach
    </div>

    <!-- Embryo for ET box -->
    <div class="box">
        <p class="box-title">Embryo for ET</p>
        <div class="cols">
            <div class="col">
                <div class="ln"><span class="l">No. of ET :</span> <span class="v">{{ $report->no_of_et }}</span></div>
                <div class="ln"><span class="l">Day :</span> <span class="v">{{ $report->et_day }}</span></div>
                <div class="ln"><span class="l">Date&amp;Time ET :</span> <span class="v">{{ $report->et_datetime }}</span></div>
                <div class="ln"><span class="l">Assisted Hatching :</span> <span class="v">{{ $report->assisted_hatching }}</span></div>
            </div>
            <div class="col">
                <div class="ln"><span class="l">ET Volume :</span> <span class="v">{{ $report->et_volume }}</span></div>
                <div class="ln"><span class="l">ET Catheter :</span> <span class="v">{{ $report->et_catheter }}</span></div>
                <div class="ln"><span class="l">ET Doctor :</span> <span class="v">{{ $report->et_doctor }}</span></div>
                <div class="ln"><span class="l">ET Embryologist :</span> <span class="v">{{ $report->et_embryologist }}</span></div>
            </div>
            <div class="col">
                <div class="ln"><span class="l">Number of Transfer :</span> <span class="v">{{ $report->number_of_transfer }}</span></div>
                <div class="ln"><span class="l">Number of Freeze :</span> <span class="v">{{ $report->number_of_freeze_et }}</span></div>
                <div class="ln"><span class="l">Number of Discard :</span> <span class="v">{{ $report->number_of_discard }}</span></div>
                <div class="ln"><span class="l">Embryologist report :</span> <span class="v">{{ $report->embryologist_report }}</span></div>
                <div class="ln"><span class="l">Embryologist approve :</span> <span class="v">{{ $report->embryologist_approve }}</span></div>
            </div>
        </div>
        <div class="ln" style="margin-top:3px;"><span class="l">Remark :</span> <span class="v">{{ $report->remark }}</span></div>
    </div>

    <!-- Embryo Development Grading box -->
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
