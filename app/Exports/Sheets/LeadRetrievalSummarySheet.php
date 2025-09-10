<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LeadRetrievalSummarySheet implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected array $data;

    public function __construct(array $prepared)
    {
        $this->data = $prepared;
    }

    public function collection()
    {
        /** @var Collection $byCompany */
        $byCompany = $this->data['byCompany'];

        $rows = $byCompany->map(function ($c) {
            // Compact, multi-line cell with current assignments
            $assigned = $c['assigned'];
            if ($assigned->isEmpty()) {
                $lrCompact = 'Not filled';
            } else {
                $lines = [];
                foreach ($assigned->take(10) as $idx => $lr) {
                    $n = $idx + 1;
                    $lines[] = sprintf(
                        '%d) %s (%s) | %s | %s',
                        $n,
                        $lr->name ?? '—',
                        $lr->email ?? '—',
                        $lr->mobile ?? '—',
                        $lr->designation ?? '—'
                    );
                }
                if ($assigned->count() > 10) {
                    $lines[] = '...';
                }
                $lrCompact = implode("\n", $lines);
            }

            return [
                'Company'                 => $c['company'],
                'Email'                   => $c['email'],
                'Phone'                   => $c['phone'],
                'Stall Number'            => $c['stall'],
                'Total Licenses'          => $c['total_qty'],
                'Licenses Assigned'       => $c['assigned_count'],
                'Licenses Unassigned'     => $c['unassigned_count'],
                'Overall Payment Status'  => $c['overall_status'],
                'Last Order Date'         => $c['last_order_at'] ? $c['last_order_at']->format('Y-m-d') : 'N/A',
                'Lead Retrieval (compact)'=> $lrCompact,
            ];
        });

        return $rows->values();
    }

    public function headings(): array
    {
        return [
            'Company',
            'Email',
            'Phone',
            'Stall Number',
            'Total Licenses',
            'Licenses Assigned',
            'Licenses Unassigned',
            'Overall Payment Status',
            'Last Order Date',
            'Lead Retrieval (compact)',
        ];
    }
}
