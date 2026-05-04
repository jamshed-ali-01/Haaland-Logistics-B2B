<?php

namespace App\Services;

use App\Models\Rate;
use App\Models\Warehouse;
use App\Models\Country;
use App\Models\Region;

class LogisticsService
{
    const CBM_TO_CFT = 35.34; // User requested 35.34
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
        return round($cft / self::CBM_TO_CFT, 4);
    }

    /**
     * Calculate quote based on parameters
     */
    public function calculateQuote(int $originId, int $countryId, ?int $regionId, float $cft, string $serviceType): array
    {
        // Settings
        $minVolSetting = \App\Models\SystemSetting::where('key', 'minimum_volume')->first();
        $minVol = $minVolSetting ? (float)$minVolSetting->value : self::MIN_CFT;
        
        $fxRateSetting = \App\Models\SystemSetting::where('key', 'eur_to_usd_rate')->first();
        $fxRate = $fxRateSetting ? (float)$fxRateSetting->value : 1.08; // Default fallback

        $originFeeSetting = \App\Models\SystemSetting::where('key', 'origin_fee_usd')->first();
        $originFeePerCft = $originFeeSetting ? (float)$originFeeSetting->value : 2.55; // Miami default

        // Internally work in CBM
        $volCbm = $cft / self::CBM_TO_CFT;
        $minVolCbm = $minVol / self::CBM_TO_CFT;
        
        // Apply minimum volume rule in CBM
        $billableCbm = max($volCbm, $minVolCbm);

        // Fetch Base Rate Entry (Source of Truth is EUR/CBM)
        $rate = Rate::where('origin_id', $originId)
            ->where('country_id', $countryId)
            ->where('service_type', $serviceType)
            ->when($regionId, function($q) use ($regionId) {
                return $q->where('region_id', $regionId);
            }, function($q) {
                return $q->whereNull('region_id');
            })
            ->first();

        // Fallback to Country (Region NULL)
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

        // Fetch Applicable Tier based on CFT (since DB tiers are in CFT)
        $billableCft = $billableCbm * self::CBM_TO_CFT;
        
        $tier = \App\Models\RateTier::where('rate_id', $rate->id)
            ->where('min_volume', '<=', $billableCft)
            ->orderBy('min_volume', 'desc')
            ->first();

        if (!$tier) {
            $tier = \App\Models\RateTier::where('rate_id', $rate->id)
                ->orderBy('min_volume', 'asc')
                ->first();
        }

        if (!$tier) {
            return ['success' => false, 'message' => 'No pricing tiers defined.'];
        }

        // Calculation: (Rate EUR/CBM * Vol CBM * FX) + (Origin Fee USD * Vol CFT)
        $oceanFreightEur = $billableCbm * $tier->price_per_cft; // Tier price is EUR/CBM
        $oceanFreightUsd = $oceanFreightEur * $fxRate;
        
        $billableCft = $billableCbm * self::CBM_TO_CFT;
        $originHandlingUsd = $billableCft * $originFeePerCft;
        $totalPriceUsd = $oceanFreightUsd + $originHandlingUsd;

        // Find Destination POE
        $poeMapping = \App\Models\PoeMapping::where('country_id', $countryId)->first();
        $destinationWarehouseId = $poeMapping ? $poeMapping->warehouse_id : null;

        $ratePerCftUsd = $billableCft > 0 ? $oceanFreightUsd / $billableCft : 0;

        return [
            'success' => true,
            'rate_per_cbm_eur' => $tier->price_per_cft,
            'fx_rate' => $fxRate,
            'billable_cft' => round($billableCft, 2),
            'billable_cbm' => round($billableCbm, 4),
            'ocean_freight_usd' => round($oceanFreightUsd, 2),
            'rate_per_cft' => round($ratePerCftUsd, 2),
            'origin_handling_usd' => round($originHandlingUsd, 2),
            'total_price' => round($totalPriceUsd, 2),
            'applied_min' => $cft < $minVol,
            'destination_warehouse_id' => $destinationWarehouseId
        ];
    }
}
