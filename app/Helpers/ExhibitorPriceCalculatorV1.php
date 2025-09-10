<?php

namespace App\Helpers;

use App\Models\SponsorItem;
use InvalidArgumentException;

class ExhibitorPriceCalculatorV1
{
    public static function universalFunction($param1, $param2)
    {
        // Add your logic here
        return $param1 + $param2; // Example logic
    }

    // Constants for rates
    const SHELL_SCHEME_RATE = 11000; // per sqm
    const RAW_SPACE_RATE = 8000;    // per sqm
    const PROCESSING_CHARGE_RATE = 0.03; // 3%
    const GST_RATE = 0.18; // 18%

    //standard and premium booth price with early bird price for semi and non-semi members
    const STANDARD_BOOTH_RATE_SEMI = 14775;
    const STANDARD_BOOTH_RATE_NON_SEMI = 19450;
    const PREMIUM_BOOTH_RATE_SEMI = 15500;
    const PREMIUM_BOOTH_RATE_NON_SEMI = 20400;
//    const EARLY_BIRD_RATE = ;

    const STANDARD_BOOTH_RATE = 10000;



    /**
     * Calculate the price of a single stall based on its type and size.
     *
     * @param int $stallSize
     * @param string $stallType ('Shell Scheme' or 'Raw Space')
     * @return float
     */
    public static function calculateStallPrice(int $stallSize, string $stallType): float
    {
        $rate = match ($stallType) {
            'Shell Scheme' => self::SHELL_SCHEME_RATE,
            'Raw Space' => self::RAW_SPACE_RATE,
            default => throw new InvalidArgumentException("Invalid stall type: $stallType"),
        };

        return $rate * max($stallSize, 9); // Ensure minimum size is 9 sqm
    }

    /**
     * Calculate the discount amount.
     *
     * @param float $discountPercentage
     * @param float $price
     * @return float
     */
    public static function calculateDiscount(float $discountPercentage, float $price): float
    {
        if ($discountPercentage <= 0) {
            return 0;
        }
        return $price * ($discountPercentage / 100);
    }
    /**
     * Calculate the total price for multiple stalls.
     *
     * @param int $stallSize
     * @param string $stallType
     * @param int $numberOfStalls
     * @return float
     */
    public static function calculateTotalStallPrice(int $stallSize, string $stallType, int $numberOfStalls): float
    {
        $stallPrice = self::calculateStallPrice($stallSize, $stallType);
        return $stallPrice * max($numberOfStalls, 1); // Ensure at least 1 stall
    }

    /**
     * Calculate the processing charges.
     *
     * @param float $totalPrice
     * @return float
     */
    public static function calculateProcessingCharges(float $totalPrice): float
    {
        return $totalPrice * self::PROCESSING_CHARGE_RATE;
    }

    /**
     * Calculate the GST amount.
     *
     * @param float $totalPrice
     * @return float
     */
    public static function calculateGST(float $totalPrice): float
    {
        return $totalPrice * self::GST_RATE;
    }

    /**
     * Calculate the final total price, including processing charges and GST.
     *
     * @param float $totalPrice
     * @return float
     */
    public static function calculateFinalTotalPrice(float $totalPrice): float
    {
        $processingCharges = self::calculateProcessingCharges($totalPrice);
        $gst = self::calculateGST($totalPrice);
        return $totalPrice + $processingCharges + $gst;
    }

    //all function in one function and by default 0% discount is applied and return actual price, discount, processing charges, gst and final total price
    public static function calculatePrice(int $stallSize, string $stallType, int $numberOfStalls = 1, float $discountPercentage = 0): array
    {
        $totalPrice = self::calculateTotalStallPrice($stallSize, $stallType, $numberOfStalls);
        $discount = self::calculateDiscount($discountPercentage, $totalPrice);
        $finalPrice = $totalPrice - $discount;
        $processingCharges = self::calculateProcessingCharges($finalPrice);
        $finalPrice_with_processing = $processingCharges + $finalPrice;
        $gst = self::calculateGST($finalPrice_with_processing);
        $finalTotalPrice = $finalPrice_with_processing + $gst;
        return [
            'actual_price' => $totalPrice,
            'discount' => $discount,
            'processing_charges' => $processingCharges,
            'gst' => $gst,
            'final_total_price' => $finalTotalPrice,
        ];
    }

    //calculate the sponsorship price where item id will be passed and return actual price, discount, processing charges, gst and final total price
    public static function calculateSponsorshipPrice(int $itemId, float $discountPercentage = 0): array
    {
        $item = SponsorItem::find($itemId);
        if (!$item) {
            throw new InvalidArgumentException("Invalid item ID: $itemId");
        }

        $totalPrice = $item->price;
        $discount = self::calculateDiscount($discountPercentage, $totalPrice);
        $finalPrice = $totalPrice - $discount;
        $processingCharges = self::calculateProcessingCharges($finalPrice);
        $finalPrice_with_processing = $processingCharges + $finalPrice;
        $gst = self::calculateGST($finalPrice_with_processing);
        $finalTotalPrice = $finalPrice_with_processing + $gst;
        return [
            'actual_price' => $totalPrice,
            'discount' => $discount,
            'processing_charges' => $processingCharges,
            'gst' => $gst,
            'final_total_price' => $finalTotalPrice,
        ];
    }






}
