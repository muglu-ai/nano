<?php

namespace App\Exports;

use App\Models\BillingDetail;
use App\Models\LeadRetrievalUser;
use App\Models\RequirementsOrder;
use App\Exports\Sheets\LeadRetrievalSummarySheet;
use App\Exports\Sheets\LeadRetrievalDetailsSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LeadRetrievalExport implements WithMultipleSheets
{
    protected ?string $paymentStatus;

    /**
     * @param string|null $paymentStatus ('paid' | 'unpaid' | 'all' | null)
     */
    public function __construct(?string $paymentStatus = null)
    {
        $this->paymentStatus = $paymentStatus;
    }

    public function sheets(): array
    {
        $data = $this->prepareData();

        return [
            new LeadRetrievalSummarySheet($data),
            new LeadRetrievalDetailsSheet($data),
        ];
    }

    /**
     * Prepare aggregated data keyed by application_id (company), using ONLY your defined relations:
     * RequirementsOrder -> user, invoice, application, orderItems.requirement
     */
    protected function prepareData(): array
    {
        $statusParam = $this->paymentStatus ?? request()->query('status', 'Paid');

        // Base query using your relations; filter to requirement id=60 (Lead Retrieval)
        $orders = RequirementsOrder::query()
            ->with([
                'user',
                'invoice',
                'application',
                'orderItems.requirement',
            ])
            ->whereHas('orderItems.requirement', fn($q) => $q->where('id', 60))
            ->when(
                $statusParam !== null && strtolower($statusParam) !== 'all',
                function ($q) use ($statusParam) {
                    $map = ['paid' => 'Paid', 'unpaid' => 'Unpaid'];
                    $normalized = strtolower($statusParam);
                    $final = $map[$normalized] ?? $statusParam;
                    $q->whereHas('invoice', fn($qi) => $qi->where('payment_status', $final));
                }
            )
            ->orderByDesc('created_at')
            ->get();

        if ($orders->isEmpty()) {
            return [
                'companies' => collect(),
                'byCompany' => collect(),
            ];
        }

        // Bulk fetch related BillingDetails and LeadRetrievalUsers to avoid N+1
        $applicationIds = $orders->pluck('application_id')->filter()->unique()->values();
        $userIds        = $orders->pluck('user_id')->filter()->unique()->values();

        $billingByAppId = BillingDetail::query()
            ->whereIn('application_id', $applicationIds)
            ->get()
            ->keyBy('application_id');
        

        $leadUsersByUserId = LeadRetrievalUser::query()
            ->whereIn('user_id', $userIds)
            ->orderBy('id')
            ->get()
            ->groupBy('user_id');

           // dd($leadUsersByUserId);

        // Aggregate by company (application_id)
        $byCompany = collect();

        foreach ($orders as $order) {
            // Only items with requirement id=60
            $relevantItems = $order->orderItems->filter(
                fn($it) => $it->requirement && (int)$it->requirement->id === 60
            );
            if ($relevantItems->isEmpty()) {
                continue;
            }

             $assigned = $leadUsersByUserId->get($order->user_id, collect());

            $appId   = $order->application_id;
            $billing = $billingByAppId->get($appId);

            $company = $billing->billing_company ?? 'N/A';
            $company = $assigned->first()->company_name ?? $billing->billing_company ?? 'N/A';

            // dd($company);
            $email   = $billing->email ?? 'N/A';
            $phone   = $billing->phone ?? 'N/A';
            $stall   = optional($order->application)->stallNumber ?? 'N/A';

            $totalQty = (int) $relevantItems->sum('quantity');
            $payment  = optional($order->invoice)->payment_status ?? null;
            $created  = $order->created_at;

           

            // GET existing entry or default (avoid "Indirect modification" issue)
            $entry = $byCompany->get($appId, [
                'application_id'  => $appId,
                'company'         => $company,
                'email'           => $email,
                'phone'           => $phone,
                'stall'           => $stall,
                'total_qty'       => 0,
                'payments'        => collect(),
                'last_order_at'   => null,
                'assigned'        => collect(),
            ]);

            // MODIFY locally
            $entry['total_qty'] += $totalQty;

            if ($payment) {
                $entry['payments']->push($payment);
            }

            if (!$entry['last_order_at'] || ($created && $created->gt($entry['last_order_at']))) {
                $entry['last_order_at'] = $created;
            }

            // Merge assigned users; de-dupe by email if you want strict uniqueness
            $entry['assigned'] = $entry['assigned']->merge($assigned);
            // $entry['assigned'] = $entry['assigned']->merge($assigned)->unique('email')->values();

            // PUT back
            $byCompany->put($appId, $entry);
        }

        // Finalize per-company summaries
        $byCompany = $byCompany->map(function ($c) {
            $payments = $c['payments']->unique()->filter()->values()->all();
            $overall  = 'N/A';
            if (in_array('Paid', $payments, true) && in_array('Unpaid', $payments, true)) {
                $overall = 'Partially Paid';
            } elseif (in_array('Paid', $payments, true)) {
                $overall = 'Paid';
            } elseif (in_array('Unpaid', $payments, true)) {
                $overall = 'Unpaid';
            }

            $assignedCount   = $c['assigned']->count();
            $unassignedCount = max(0, (int)$c['total_qty'] - $assignedCount);

            return [
                'application_id'   => $c['application_id'],
                'company'          => $c['company'],
                'email'            => $c['email'],
                'phone'            => $c['phone'],
                'stall'            => $c['stall'],
                'total_qty'        => (int)$c['total_qty'],
                'assigned_count'   => (int)$assignedCount,
                'unassigned_count' => (int)$unassignedCount,
                'overall_status'   => $overall,
                'last_order_at'    => $c['last_order_at'],
                'assigned'         => $c['assigned'], // pass through to Details
            ];
        })->values();

        return [
            'companies' => $byCompany->pluck('application_id'),
            'byCompany' => $byCompany->keyBy('application_id'),
        ];
    }
}
