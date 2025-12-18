<?php
namespace App\Helpers;

use App\Models\SponsorItem;
use InvalidArgumentException;
class ExhibitorPriceCalculator
{
    // Constants for processing charges and GST
    const PROCESSING_CHARGE_RATE = 0; // 3% (currently 0%)
    const GST_RATE = 0.18; // 18% GST

    // Pricing table dynamically loaded from the provided table
    private static array $pricingTable2 = [
        'INR' => [
            'SEMI' => [
                'Standard' => ['Regular' => ['Bare Space' => 14775, 'Shell Scheme' => 16475], 'Early Bird' => ['Bare Space' => 12250, 'Shell Scheme' => 13450]],
                'Premium' => ['Regular' => ['Bare Space' => 15500, 'Shell Scheme' => 17300], 'Early Bird' => ['Bare Space' => 12875, 'Shell Scheme' => 14125]],
            ],
            'Non-SEMI' => [
                'Standard' => ['Regular' => ['Bare Space' => 19450, 'Shell Scheme' => 21675], 'Early Bird' => ['Bare Space' => 16125, 'Shell Scheme' => 17700]],
                'Premium' => ['Regular' => ['Bare Space' => 20400, 'Shell Scheme' => 22775], 'Early Bird' => ['Bare Space' => 16950, 'Shell Scheme' => 18575]],
            ],
        ],
        'EUR' => [
            'SEMI' => [
                'Standard' => ['Regular' => ['Bare Space' => 287, 'Shell Scheme' => 325], 'Early Bird' => ['Bare Space' => 250, 'Shell Scheme' => 286]],
                'Premium' => ['Regular' => ['Bare Space' => 301, 'Shell Scheme' => 341], 'Early Bird' => ['Bare Space' => 262, 'Shell Scheme' => 300]],
            ],
            'Non-SEMI' => [
                'Standard' => ['Regular' => ['Bare Space' => 376, 'Shell Scheme' => 426], 'Early Bird' => ['Bare Space' => 328, 'Shell Scheme' => 375]],
                'Premium' => ['Regular' => ['Bare Space' => 394, 'Shell Scheme' => 447], 'Early Bird' => ['Bare Space' => 343, 'Shell Scheme' => 393]],
            ],
        ]
    ];

    private static array $pricingTable = [
        'INR' => [
            'SEMI' => [
                'Standard' => [
                    'Regular' => ['Bare Space' => 17730, 'Shell Scheme' => 19770],
                    'Early Bird' => ['Bare Space' => 14700, 'Shell Scheme' => 16140]
                ],
                'Premium' => [
                    'Regular' => ['Bare Space' => 18600, 'Shell Scheme' => 20760],
                    'Early Bird' => ['Bare Space' => 15450, 'Shell Scheme' => 16950]
                ],
            ],
            'Non-SEMI' => [
                'Standard' => [
                    'Regular' => ['Bare Space' => 23340, 'Shell Scheme' => 26010],
                    'Early Bird' => ['Bare Space' => 19350, 'Shell Scheme' => 21240]
                ],
                'Premium' => [
                    'Regular' => ['Bare Space' => 24480, 'Shell Scheme' => 27330],
                    'Early Bird' => ['Bare Space' => 20340, 'Shell Scheme' => 22290]
                ],
            ],
        ],
        'EUR' => [
            'SEMI' => [
                'Standard' => [
                    'Regular' => ['Bare Space' => 26600, 'Shell Scheme' => 30130],
                    'Early Bird' => ['Bare Space' => 23180, 'Shell Scheme' => 26510]
                ],
                'Premium' => [
                    'Regular' => ['Bare Space' => 27900, 'Shell Scheme' => 31610],
                    'Early Bird' => ['Bare Space' => 24290, 'Shell Scheme' => 27810]
                ],
            ],
            'Non-SEMI' => [
                'Standard' => [
                    'Regular' => ['Bare Space' => 34860, 'Shell Scheme' => 39490],
                    'Early Bird' => ['Bare Space' => 30410, 'Shell Scheme' => 34760]
                ],
                'Premium' => [
                    'Regular' => ['Bare Space' => 36520, 'Shell Scheme' => 41440],
                    'Early Bird' => ['Bare Space' => 31800, 'Shell Scheme' => 36430]
                ],
            ],
        ]
    ];


    /**
     * Get stall price based on membership, booth type, stall type, and pricing category.
     *
     * @param string $membershipType ('SEMI' or 'Non-SEMI')
     * @param string $boothType ('Standard' or 'Premium')
     * @param string $stallType ('Bare Space' or 'Shell Scheme')
     * @param bool $earlyBird (true for early bird pricing)
     * @param string $currencyType ('INR' or 'EUR')
     * @return float
     */
    public static function getStallPrice(string $membershipType, string $boothType, string $stallType, string $earlyBird, string $currencyType): float
    {
        $priceCategory = $earlyBird ? 'Early Bird' : 'Regular';

        if (!isset(self::$pricingTable[$currencyType][$membershipType][$boothType][$priceCategory][$stallType])) {
            throw new InvalidArgumentException("Invalid parameters provided for pricing.");
        }

        return self::$pricingTable[$currencyType][$membershipType][$boothType][$priceCategory][$stallType];
    }

    /**
     * Calculate the price of a single stall based on parameters.
     *
     * @param int $stallSize
     * @param string $membershipType
     * @param string $boothType
     * @param string $stallType
     * @param bool $earlyBird
     * @param string $currencyType
     * @return float
     */
    public static function calculateStallPrice(int $stallSize, string $membershipType, string $boothType, string $stallType, bool $earlyBird, string $currencyType): float
    {
        $rate = self::getStallPrice($membershipType, $boothType, $stallType, $earlyBird, $currencyType);
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
        return ($discountPercentage > 0) ? ($price * ($discountPercentage / 100)) : 0;
    }

    /**
     * Calculate the total price for multiple stalls.
     *
     * @param int $stallSize
     * @param string $membershipType
     * @param string $boothType
     * @param string $stallType
     * @param bool $earlyBird
     * @param string $currencyType
     * @param int $numberOfStalls
     * @return float
     */
    public static function calculateTotalStallPrice(int $stallSize, string $membershipType, string $boothType, string $stallType, bool $earlyBird, string $currencyType, int $numberOfStalls): float
    {
        $stallPrice = self::calculateStallPrice($stallSize, $membershipType, $boothType, $stallType, $earlyBird, $currencyType);
        return $stallPrice * max($numberOfStalls, 1); // Ensure at least 1 stall
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
     * Calculate the final total price.
     *
     * @param int $stallSize
     * @param string $membershipType
     * @param string $boothType
     * @param string $stallType
     * @param bool $earlyBird
     * @param string $currencyType
     * @param int $numberOfStalls
     * @param float $discountPercentage
     * @return array
     */
    public static function calculatePrice(int $stallSize, string $membershipType, string $boothType, string $stallType, bool $earlyBird, string $currencyType, int $numberOfStalls = 1, float $discountPercentage = 0): array
    {
        $totalPrice = self::calculateTotalStallPrice($stallSize, $membershipType, $boothType, $stallType, $earlyBird, $currencyType, $numberOfStalls);
        $discount = self::calculateDiscount($discountPercentage, $totalPrice);
        $finalPrice = $totalPrice - $discount;
        $gst = self::calculateGST($finalPrice);
        $finalTotalPrice = $finalPrice + $gst;

        return [
            'actual_price' => round($totalPrice),      
            'discount' => round($discount),             
            'gst' => round($gst),                       
            'final_total_price' => round($finalTotalPrice), 
            'processing_charges' => 0,
        ];
    }

    public static function calculateSponsorshipPrice(int $itemId, $member, $quantity, float $discountPercentage = 0): array
    {
        $item = SponsorItem::find($itemId);
        if (!$item) {
            throw new InvalidArgumentException("Invalid item ID: $itemId");
        }

        //if member is true then use mem_price else use price
        if ($member) {
            $price= $item->mem_price;
        } else {
            $price = $item->price;
        }

        // Calculate the total price based on quantity
        $price = $price * $quantity;

        $totalPrice = $price;
        $discount = self::calculateDiscount($discountPercentage, $totalPrice);
        $finalPrice = $totalPrice - $discount;
        $processingCharges = 0;
        $finalPrice_with_processing = $processingCharges + $finalPrice;
        $gst = self::calculateGST($finalPrice_with_processing);
        $finalTotalPrice = $finalPrice_with_processing + $gst;
        return [
            'actual_price' => round($totalPrice),
            'discount' => round($discount),
            'processing_charges' => round($processingCharges),
            'gst' => round($gst),
            'final_total_price' => round($finalTotalPrice),
        ];
    }


}
