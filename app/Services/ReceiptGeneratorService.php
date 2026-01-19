<?php

namespace App\Services;

use App\Models\Ticket\TicketReceipt;
use App\Models\Ticket\TicketUpgradeRequest;
use App\Models\Ticket\TicketOrder;
use Illuminate\Support\Facades\Log;

class ReceiptGeneratorService
{
    /**
     * Generate upgrade receipt
     */
    public function generateUpgradeReceipt(TicketUpgradeRequest $upgradeRequest, string $type = 'provisional'): TicketReceipt
    {
        $order = $upgradeRequest->upgradeOrder;

        if (!$order) {
            throw new \Exception('Order not found for upgrade request.');
        }

        $receiptNo = $this->generateReceiptNumber($type);

        $receipt = TicketReceipt::updateOrCreate(
            [
                'registration_id' => $order->registration_id,
                'order_id' => $order->id,
                'type' => $type === 'final' ? 'upgrade_final' : 'upgrade_provisional',
            ],
            [
                'receipt_no' => $receiptNo,
                'issued_at' => now(),
            ]
        );

        return $receipt;
    }

    /**
     * Generate PDF receipt
     */
    public function generateReceiptPdf(TicketReceipt $receipt): string
    {
        // Implement PDF generation using a library like dompdf or barryvdh/laravel-dompdf
        // For now, return path to view
        return 'delegate.receipts.pdf';
    }

    /**
     * Get upgrade receipt data structure
     */
    public function getUpgradeReceiptData(TicketUpgradeRequest $upgradeRequest): array
    {
        $order = $upgradeRequest->upgradeOrder;
        $registration = $upgradeRequest->registration;
        $upgradeData = $upgradeRequest->upgrade_data_json;

        return [
            'receipt_no' => $upgradeRequest->upgradeOrder?->receipt?->receipt_no ?? 'PENDING',
            'type' => $upgradeRequest->status === 'paid' ? 'final' : 'provisional',
            'order_no' => $order->order_no ?? null,
            'registration' => [
                'company_name' => $registration->company_name,
                'contact_name' => $registration->contact->name,
                'contact_email' => $registration->contact->email,
            ],
            'upgrade_details' => $upgradeData['tickets'] ?? [],
            'totals' => $upgradeData['totals'] ?? [],
            'created_at' => $upgradeRequest->created_at,
            'paid_at' => $upgradeRequest->status === 'paid' ? now() : null,
        ];
    }

    /**
     * Generate receipt number
     */
    private function generateReceiptNumber(string $type): string
    {
        $prefix = $type === 'final' ? 'RCP-UPG-F-' : 'RCP-UPG-P-';
        $year = date('Y');
        $sequence = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        return $prefix . $year . '-' . $sequence;
    }
}
