<?php

namespace App\Services;

class TicketGstCalculationService
{
    /**
     * Determine GST type based on registration data
     * 
     * @param array|object $registrationData Registration data (array from session or TicketRegistration model)
     * @return string 'cgst_sgst' or 'igst'
     */
    public function determineGstType($registrationData): string
    {
        // Get organizer state from config
        $organizerState = config('constants.GST_STATE', 'Karnataka');
        $organizerStateCode = $this->getOrganizerStateCode();
        
        // Get organization state from registration data
        $companyState = is_array($registrationData) 
            ? ($registrationData['company_state'] ?? null)
            : ($registrationData->company_state ?? null);
        
        // If organization state is NOT Karnataka, apply IGST (inter-state)
        if ($companyState !== $organizerState) {
            return 'igst';
        }
        
        // If organization state IS Karnataka, check GST requirements
        $gstRequired = is_array($registrationData)
            ? ($registrationData['gst_required'] ?? false)
            : ($registrationData->gst_required ?? false);
        
        $gstin = is_array($registrationData)
            ? ($registrationData['gstin'] ?? null)
            : ($registrationData->gstin ?? null);
        
        // If GST Invoice is required and GSTIN is provided
        if ($gstRequired && $gstin) {
            $customerGstinStateCode = $this->getGstinStateCode($gstin);
            
            // If GSTIN state code matches organizer state code, apply CGST + SGST
            if ($customerGstinStateCode && $customerGstinStateCode === $organizerStateCode) {
                return 'cgst_sgst';
            }
        }
        
        // Default to IGST for Karnataka if GST requirements not met
        return 'igst';
    }
    
    /**
     * Calculate GST amounts based on GST type
     * 
     * @param float $subtotal Base amount before GST
     * @param string $gstType 'cgst_sgst' or 'igst'
     * @return array Contains all GST rates and amounts
     */
    public function calculateGst(float $subtotal, string $gstType): array
    {
        $cgstRate = config('constants.CGST_RATE', 9);
        $sgstRate = config('constants.SGST_RATE', 9);
        $igstRate = config('constants.IGST_RATE', 18);
        
        if ($gstType === 'cgst_sgst') {
            $cgstAmount = round(($subtotal * $cgstRate) / 100);
            $sgstAmount = round(($subtotal * $sgstRate) / 100);
            $totalGst = round($cgstAmount + $sgstAmount);
            
            return [
                'cgst_rate' => $cgstRate,
                'cgst_amount' => $cgstAmount,
                'sgst_rate' => $sgstRate,
                'sgst_amount' => $sgstAmount,
                'igst_rate' => null,
                'igst_amount' => null,
                'gst_type' => 'cgst_sgst',
                'total_gst' => $totalGst,
            ];
        } else {
            // IGST
            $igstAmount = round(($subtotal * $igstRate) / 100);
            
            return [
                'cgst_rate' => null,
                'cgst_amount' => null,
                'sgst_rate' => null,
                'sgst_amount' => null,
                'igst_rate' => $igstRate,
                'igst_amount' => $igstAmount,
                'gst_type' => 'igst',
                'total_gst' => $igstAmount,
            ];
        }
    }
    
    /**
     * Get organizer state code from GSTIN
     * 
     * @return string|null State code (first 2 digits of GSTIN)
     */
    public function getOrganizerStateCode(): ?string
    {
        $gstin = config('constants.GSTIN');
        if (!$gstin || strlen($gstin) < 2) {
            return null;
        }
        
        return substr($gstin, 0, 2);
    }
    
    /**
     * Extract state code from GSTIN
     * 
     * @param string $gstin GSTIN number
     * @return string|null State code (first 2 digits)
     */
    public function getGstinStateCode(?string $gstin): ?string
    {
        if (!$gstin || strlen($gstin) < 2) {
            return null;
        }
        
        return substr($gstin, 0, 2);
    }
    
    /**
     * Check if customer and organizer are in same state
     * 
     * @param string|null $customerStateCode Customer GSTIN state code
     * @param string|null $organizerStateCode Organizer GSTIN state code
     * @return bool
     */
    public function isSameState(?string $customerStateCode, ?string $organizerStateCode): bool
    {
        if (!$customerStateCode || !$organizerStateCode) {
            return false;
        }
        
        return $customerStateCode === $organizerStateCode;
    }
}
