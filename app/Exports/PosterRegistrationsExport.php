<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\PosterRegistration;

class PosterRegistrationsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PosterRegistration::with(['posterAuthors.country', 'posterAuthors.state']);

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('tin_no', 'like', "%{$search}%")
                  ->orWhere('abstract_title', 'like', "%{$search}%")
                  ->orWhere('lead_author_name', 'like', "%{$search}%")
                  ->orWhere('lead_author_email', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['payment_status'])) {
            $query->where('payment_status', $this->filters['payment_status']);
        }

        if (!empty($this->filters['currency'])) {
            $query->where('currency', $this->filters['currency']);
        }

        if (!empty($this->filters['sector'])) {
            $query->where('sector', $this->filters['sector']);
        }

        if (!empty($this->filters['presentation_mode'])) {
            $query->where('presentation_mode', $this->filters['presentation_mode']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function map($registration): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $currencySymbol = $registration->currency === 'USD' ? '$' : '₹';

        // Get author details
        $authorNames = [];
        $authorEmails = [];
        $authorMobiles = [];
        $authorInstitutions = [];
        $attendingAuthors = 0;

        foreach ($registration->posterAuthors->sortBy('author_index') as $author) {
            $name = trim("{$author->title} {$author->first_name} {$author->last_name}");
            if ($author->is_lead_author) {
                $name .= ' (Lead)';
            }
            if ($author->is_presenter) {
                $name .= ' (Presenter)';
            }
            $authorNames[] = $name;
            $authorEmails[] = $author->email ?? '';
            $authorMobiles[] = $author->mobile ?? '';
            $authorInstitutions[] = $author->institution ?? '';
            
            if ($author->will_attend) {
                $attendingAuthors++;
            }
        }

        return [
            $rowNumber,
            $registration->tin_no,
            $registration->created_at ? $registration->created_at->format('Y-m-d H:i:s') : '',
            $registration->sector ?? '',
            $registration->poster_category ?? '',
            $registration->abstract_title ?? '',
            $registration->abstract ?? '',
            $registration->presentation_mode ?? '',
            $registration->lead_author_name ?? '',
            $registration->lead_author_email ?? '',
            $registration->lead_author_mobile ?? '',
            $registration->posterAuthors->count(),
            $attendingAuthors,
            implode('; ', $authorNames),
            implode('; ', $authorEmails),
            implode('; ', $authorMobiles),
            implode('; ', $authorInstitutions),
            $registration->currency ?? '',
            number_format($registration->base_amount, 2),
            number_format($registration->gst_amount, 2),
            number_format($registration->processing_fee, 2),
            number_format($registration->total_amount, 2),
            $registration->payment_status ?? '',
            $registration->payment_method ?? '',
            $registration->payment_transaction_id ?? '',
            $registration->payment_date ? $registration->payment_date->format('Y-m-d H:i:s') : '',
            $registration->publication_permission ? 'Yes' : 'No',
            $registration->authors_approval ? 'Yes' : 'No',
            $registration->status ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            'Sr No',
            'TIN (Transaction ID)',
            'Registration Date',
            'Sector',
            'Poster Category',
            'Abstract Title',
            'Abstract',
            'Presentation Mode',
            'Lead Author Name',
            'Lead Author Email',
            'Lead Author Mobile',
            'Total Authors',
            'Attending Authors',
            'All Author Names',
            'All Author Emails',
            'All Author Mobiles',
            'All Author Institutions',
            'Currency',
            'Base Amount',
            'GST Amount',
            'Processing Fee',
            'Total Amount',
            'Payment Status',
            'Payment Method',
            'Transaction ID',
            'Payment Date',
            'Publication Permission',
            'Authors Approval',
            'Status',
        ];
    }
}
