<?php

namespace App\Services\Estimates;

use App\Models\RepairEstimateRelease;
use Dompdf\Dompdf;

class EstimatePdf
{
    public function render(RepairEstimateRelease $release): string
    {
        $data = $release->snapshot;
        $html = '<meta charset="UTF-8"><style>body{font:12px DejaVu Sans;color:#172554}table{width:100%;border-collapse:collapse}td,th{padding:6px;border-bottom:1px solid #ddd;text-align:left}p{white-space:pre-wrap}</style>';
        $html .= '<h1>BidFreq Estimate</h1><p>'.e($data['number']).' · Version '.$release->version.'<br>'.e($data['address']).'</p>';
        $html .= '<h2>Scope of Work</h2><table><thead><tr><th>Task</th><th>Qty</th><th>Unit</th><th>Rate (USD)</th><th>Total</th></tr></thead><tbody>';
        foreach ($data['lines'] as $line) {
            $html .= '<tr><td>'.e($line['name']).'</td><td>'.e($line['quantity']).'</td><td>'.e($line['unit']).'</td><td>'.e($line['rate']).'</td><td>'.e($line['line_total']).'</td></tr>';
        }
        $html .= '</tbody></table><h2>Total: $'.e($data['total']).' USD</h2>';
        foreach ($data['analysis'] as $field => $value) {
            $html .= '<h3>'.e(ucfirst($field)).'</h3><p>'.e(is_array($value) ? implode("\n", $value) : $value).'</p>';
        }
        $pdf = new Dompdf;
        $pdf->loadHtml($html);
        $pdf->setPaper('letter');
        $pdf->render();

        return $pdf->output();
    }
}
