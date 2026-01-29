<?php

namespace App\Exports;

use App\Models\ExhibitorInfo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ExhibitorInfoExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * Fetch data from the database.
     */
    public function collection()
    {
        Log::info('ExhibitorInfoExport initiated', [
            'user_id' => Auth::check() ? Auth::id() : null,
            'date' => now(),
            'ip' => request()->ip(),
        ]);

        return ExhibitorInfo::with(['application.user'])
            ->get();
    }

    /**
     * Define column headers.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Stall Number',
            'Application ID',
            'Company Name',
            'Fascia Name',
            'Contact Person',
            'Designation',
            'Email',
            'Phone',
            'Telephone',
            'Address',
            'City',
            'State',
            'Country',
            'Zip Code',
            'Website',
            'Description',
            'Sector',
            'Category',
            'LinkedIn',
            'Instagram',
            'Facebook',
            'YouTube',
            // 'Submission Status',
            // 'API Status',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * Map data to match headings.
     */
    public function map($exhibitor): array
    {
        return [
            $exhibitor->id ?? 'N/A',
            optional($exhibitor->application)->stallNumber ?? 'N/A',
            $exhibitor->application_id ?? 'N/A',
            $exhibitor->company_name ?? 'N/A',
            $exhibitor->fascia_name ?? 'N/A',
            $exhibitor->contact_person ?? 'N/A',
            $exhibitor->designation ?? 'N/A',
            $exhibitor->email ?? 'N/A',
            $exhibitor->phone ?? 'N/A',
            $exhibitor->telPhone ?? 'N/A',
            $exhibitor->address ?? 'N/A',
            $exhibitor->city ?? 'N/A',
            $exhibitor->state ?? 'N/A',
            $exhibitor->country ?? 'N/A',
            $exhibitor->zip_code ?? 'N/A',
            $exhibitor->website ?? 'N/A',
            $exhibitor->description ?? 'N/A',
            $exhibitor->sector ?? 'Startup Booth/POD',
            $exhibitor->category ?? 'N/A',
            $exhibitor->linkedin ?? 'N/A',
            $exhibitor->instagram ?? 'N/A',
            $exhibitor->facebook ?? 'N/A',
            $exhibitor->youtube ?? 'N/A',
            // $exhibitor->submission_status == 1 ? 'Completed' : 'Incomplete',
            // $exhibitor->api_status == 1 ? 'Success' : ($exhibitor->api_status == 0 ? 'Pending' : 'N/A'),
            $exhibitor->created_at ? $exhibitor->created_at->format('Y-m-d H:i:s') : 'N/A',
            $exhibitor->updated_at ? $exhibitor->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }
}

