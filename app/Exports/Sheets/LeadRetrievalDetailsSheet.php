<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LeadRetrievalDetailsSheet implements FromCollection, WithHeadings, ShouldAutoSize
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

        $rows = [];

        foreach ($byCompany as $c) {
            $assigned = $c['assigned']->values();

            foreach ($assigned as $lr) {
                $rows[] = [
                    'Company Name'   => $c['company'],
                    'Stall Number'   => $c['stall'],
                    'Name'           => $lr->name        ?? '',
                    'Email'          => $lr->email       ?? '',
                    'Contact Number' => $lr->mobile      ?? '',
                    'Job Title'      => $lr->designation ?? '',
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Company Name',
            'Stall Number',
            'Name',
            'Email',
            'Contact Number',
            'Job Title',
        ];
    }
}
