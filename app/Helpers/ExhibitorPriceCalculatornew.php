<?php

namespace App\Helpers;

use App\Models\SponsorItem;
use InvalidArgumentException;

class ExhibitorPriceCalculatornew
{
    /**
     * Get booth price dynamically from config
     * @param string $boothType ('standard' or 'premium')
     * @param string $membership ('semi_member' or 'non_semi_member')
     * @param string $priceType ('regular' or 'early_bird')
     * @param string $spaceType ('bare' or 'shell')
     * @param string $currency ('INR' or 'EUR')
     * @return float
     */
    public static function getBoothPrice(string $boothType, string $membership, string $priceType, string $spaceType, string $currency): float
    {
        return config("booth_rates.$boothType.$membership.$priceType.$spaceType.$currency", 0);
    }

    /**
     * Calculate stall price based on type and size.
     * @param int $stallSize
     * @param string $stallType ('Shell Scheme' or 'Raw Space')
     * @param string $currency ('INR' or 'EUR')
     * @return float
     */
    public static function calculateStallPrice(int $stallSize, string $stallType, string $currency = 'INR'): float
    {
        $rates = [
            'INR' => ['Shell Scheme' => 11000, 'Raw Space' => 8000],
            'EUR' => ['Shell Scheme' => 300, 'Raw Space' => 250],
        ];

        if (!isset($rates[$currency][$stallType])) {
            throw new InvalidArgumentException("Invalid stall type: $stallType");
        }

        return $rates[$currency][$stallType] * max($stallSize, 9);
    }

    /**
     * Calculate discount amount
     * @param float $discountPercentage
     * @param float $price
     * @return float
     */
    public static function calculateDiscount(float $discountPercentage, float $price): float
    {
        return $price * ($discountPercentage / 100);
    }

    /**
     * Calculate processing charges
     * @param float $totalPrice
     * @return float
     */
    public static function calculateProcessingCharges(float $totalPrice): float
    {
        return $totalPrice * config('booth_rates.processing_charge_rate');
    }

    /**
     * Calculate GST amount
     * @param float $totalPrice
     * @return float
     */
    public static function calculateGST(float $totalPrice): float
    {
        return $totalPrice * config('booth_rates.gst_rate');
    }

    /**
     * Calculate the final total price including processing charges and GST
     * @param float $totalPrice
     * @return float
     */
    public static function calculateFinalTotalPrice(float $totalPrice): float
    {
        return $totalPrice + self::calculateProcessingCharges($totalPrice) + self::calculateGST($totalPrice);
    }

    /**
     * Calculate booth price dynamically
     * @param string $boothType ('standard' or 'premium')
     * @param string $membership ('semi_member' or 'non_semi_member')
     * @param string $priceType ('regular' or 'early_bird')
     * @param string $spaceType ('bare' or 'shell')
     * @param string $currency ('INR' or 'EUR')
     * @param int $stallSize
     * @param int $numberOfStalls
     * @param float $discountPercentage
     * @return array
     */
    public static function calculateBoothPrice(
        string $boothType,
        string $membership,
        string $priceType,
        string $spaceType,
        string $currency = 'INR',
        int $stallSize = 9,
        int $numberOfStalls = 1,
        float $discountPercentage = 0
    ): array {
        $rate = self::getBoothPrice($boothType, $membership, $priceType, $spaceType, $currency);
        $totalPrice = $rate * $stallSize * $numberOfStalls;
        $discount = self::calculateDiscount($discountPercentage, $totalPrice);
        $finalPrice = $totalPrice - $discount;
        $processingCharges = self::calculateProcessingCharges($finalPrice);
        $gst = self::calculateGST($finalPrice + $processingCharges);
        $finalTotalPrice = $finalPrice + $processingCharges + $gst;

        return [
            'currency' => $currency,
            'actual_price' => $totalPrice,
            'discount' => $discount,
            'processing_charges' => $processingCharges,
            'gst' => $gst,
            'final_total_price' => $finalTotalPrice,
        ];
    }






}
