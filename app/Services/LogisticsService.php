<?php

namespace App\Services;

use App\Models\Rate;
use App\Models\Warehouse;
use App\Models\Country;
use App\Models\Region;

class LogisticsService
{
    const CBM_TO_CFT = 35.3147;
    const MIN_CFT = 100;

    /**
     * Convert CBM to CFT
     */
    public function cbmToCft(float $cbm): float
    {
        return round($cbm * self::CBM_TO_CFT, 2);
    }

    /**
     * Convert CFT to CBM
     */
    public function cftToCbm(float $cft): float
    {
        return round($cft / self::CBM_TO_CFT, 2);
    }

    /**
     * Calculate quote based on parameters
     */
    public function calculateQuote(int $originId, int $countryId, ?int $regionId, float $cft, string $serviceType): array
    {
        $minVolSetting = \App\Models\SystemSetting::where('key', 'minimum_volume')->first();
        $minVol = $minVolSetting ? (float)$minVolSetting->value : self::MIN_CFT;
        
        $serviceFeeSetting = \App\Models\SystemSetting::where('key', 'origin_service_fee')->first();
        $serviceFeePerCft = $serviceFeeSetting ? (float)$serviceFeeSetting->value : 3.00;

        // Apply minimum volume rule
        $billableCft = max($cft, $minVol);

        // Fetch Base Rate Entry
        // Step 1: Search for specific Region + Country + Origin
        $rate = Rate::where('origin_id', $originId)
            ->where('country_id', $countryId)
            ->where('service_type', $serviceType)
            ->when($regionId, function($q) use ($regionId) {
                return $q->where('region_id', $regionId);
            }, function($q) {
                return $q->whereNull('region_id');
            })
            ->first();

        // Step 2: Fallback to Country + Origin (Region NULL) if Step 1 failed
        if (!$rate && $regionId) {
            $rate = Rate::where('origin_id', $originId)
                ->where('country_id', $countryId)
                ->where('service_type', $serviceType)
                ->whereNull('region_id')
                ->first();
        }

        if (!$rate) {
            return ['success' => false, 'message' => 'Rate not found for selected criteria.'];
        }

        // Fetch Applicable Tier (The highest min_volume that is <= billableCft)
        $tier = \App\Models\RateTier::where('rate_id', $rate->id)
            ->where('min_volume', '<=', $billableCft)
            ->orderBy('min_volume', 'desc')
            ->first();

        // If no tier found (e.g., volume is less than the first tier), fallback to the first tier
        if (!$tier) {
            $tier = \App\Models\RateTier::where('rate_id', $rate->id)
                ->orderBy('min_volume', 'asc')
                ->first();
        }

        if (!$tier) {
            return ['success' => false, 'message' => 'No pricing tiers defined for this route.'];
        }

        $oceanFreight = $billableCft * $tier->price_per_cft;
        $originHandling = $billableCft * $serviceFeePerCft;
        $totalPrice = $oceanFreight + $originHandling;

        return [
            'success' => true,
            'rate_per_cft' => $tier->price_per_cft,
            'service_fee_per_cft' => $serviceFeePerCft,
            'billable_cft' => $billableCft,
            'ocean_freight' => round($oceanFreight, 2),
            'origin_handling' => round($originHandling, 2),
            'total_price' => round($totalPrice, 2),
            'applied_min' => $cft < $minVol
        ];
    }
}
