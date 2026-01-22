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
        
        // Normalize state names for comparison (case-insensitive)
        $organizerStateNormalized = trim(strtolower($organizerState));
        
        // Get nationality/currency
        $nationality = is_array($registrationData)
            ? ($registrationData['nationality'] ?? 'Indian')
            : ($registrationData->nationality ?? 'Indian');
        $isInternational = (strtolower($nationality) === 'international');
        
        // If currency is international, apply IGST
        if ($isInternational) {
            return 'igst';
        }
        
        // Get GST required status
        $gstRequiredRaw = is_array($registrationData)
            ? ($registrationData['gst_required'] ?? false)
            : ($registrationData->gst_required ?? false);
        
        // Normalize gst_required (can be string "0"/"1" or boolean)
        $gstRequired = ($gstRequiredRaw === '1' || $gstRequiredRaw === 1 || $gstRequiredRaw === true);
        
        if (!$gstRequired) {
            // GST required is NO: Match organization state with event state
            $companyState = is_array($registrationData) 
                ? ($registrationData['company_state'] ?? null)
                : ($registrationData->company_state ?? null);
            
            if ($companyState) {
                $companyStateNormalized = trim(strtolower($companyState));
                // If organization state matches event state, apply CGST + SGST
                if ($companyStateNormalized === $organizerStateNormalized) {
                    return 'cgst_sgst';
                }
            }
            // If doesn't match, apply IGST
            return 'igst';
        } else {
            // GST required is YES: Match GST state (from GSTIN) with event state
            $gstin = is_array($registrationData)
                ? ($registrationData['gstin'] ?? null)
                : ($registrationData->gstin ?? null);
            
            // Clean GSTIN (remove spaces, convert to uppercase)
            $gstin = $gstin ? strtoupper(trim($gstin)) : null;
            
            if ($gstin && strlen($gstin) >= 2) {
                $customerGstinStateCode = $this->getGstinStateCode($gstin);
                
                // If GSTIN state code matches organizer state code, apply CGST + SGST
                if ($customerGstinStateCode && $customerGstinStateCode === $organizerStateCode) {
                    return 'cgst_sgst';
                }
            }
            // If doesn't match or GSTIN not provided, apply IGST
            return 'igst';
        }
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
