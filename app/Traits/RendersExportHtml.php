<?php

namespace App\Traits;

trait RendersExportHtml
{
    private function renderExportHtml(
        string $title,
        array $headers,
        array $rows,
        string $csvContent,
        string $filename
    ): \Illuminate\Http\Response {
        $csvBase64 = base64_encode($csvContent);
        $total = count($rows);
        $generatedAt = now()->format('d/m/Y H:i:s');
        $safeTitle = htmlspecialchars($title);
        $safeFilename = htmlspecialchars($filename);

        $theadHtml = '<tr>'.implode('', array_map(
            fn ($h) => '<th>'.htmlspecialchars((string) $h).'</th>',
            $headers
        )).'</tr>';

        $tbodyHtml = '';
        foreach ($rows as $row) {
            $tbodyHtml .= '<tr>';
            foreach (array_values((array) $row) as $cell) {
                $tbodyHtml .= '<td>'.htmlspecialchars((string) $cell).'</td>';
            }
            $tbodyHtml .= '</tr>';
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>{$safeTitle}</title>
<style>
  *{box-sizing:border-box}
  body{font-family:Arial,sans-serif;padding:20px 24px;background:#f9fafb;color:#111;margin:0}
  h1{font-size:20px;margin:0 0 3px}
  .meta{color:#666;font-size:13px;margin:0 0 14px}
  .toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
  .count{font-size:13px;color:#555}
  a.btn{display:inline-block;background:#166534;color:#fff;padding:7px 16px;border-radius:6px;text-decoration:none;font-size:13px;font-weight:600}
  a.btn:hover{background:#14532d}
  table{width:100%;border-collapse:collapse;background:#fff;border-radius:6px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.1)}
  th{background:#166534;color:#fff;padding:9px 12px;text-align:left;font-size:13px;white-space:nowrap}
  td{padding:8px 12px;font-size:13px;border-bottom:1px solid #e5e7eb;vertical-align:top}
  tr:last-child td{border-bottom:none}
  tr:hover td{background:#f0fdf4}
  @media print{.toolbar{display:none}}
</style>
</head>
<body>
<h1>{$safeTitle}</h1>
<p class="meta">Generated on {$generatedAt}</p>
<div class="toolbar">
  <span class="count">{$total} record(s)</span>
  <a class="btn" href="data:text/csv;charset=UTF-8;base64,{$csvBase64}" download="{$safeFilename}">&#8595; Download CSV</a>
</div>
<table>
  <thead>{$theadHtml}</thead>
  <tbody>{$tbodyHtml}</tbody>
</table>
</body>
</html>
HTML;

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function buildCsvString(array $headers, array $rows): string
    {
        $escape = static function (string $field): string {
            $field = str_replace('"', '""', $field);
            if (str_contains($field, ',') || str_contains($field, '"') || str_contains($field, "\n")) {
                return '"'.$field.'"';
            }

            return $field;
        };

        $csv = implode(',', array_map(fn ($h) => $escape((string) $h), $headers))."\n";
        foreach ($rows as $row) {
            $csv .= implode(',', array_map(fn ($v) => $escape((string) $v), array_values((array) $row)))."\n";
        }

        return $csv;
    }
}
