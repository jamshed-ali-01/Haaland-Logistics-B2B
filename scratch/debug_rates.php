<?php

use App\Models\Rate;
use App\Models\Warehouse;
use App\Models\Country;
use App\Models\Region;

echo "--- Rate Audit ---\n";
$rates = Rate::all();
echo "Total Rates in DB: " . $rates->count() . "\n\n";

foreach ($rates as $rate) {
    echo "ID: {$rate->id} | Origin: {$rate->origin_id} | Country: {$rate->country_id} | Region: " . ($rate->region_id ?? 'NULL') . " | Service: {$rate->service_type}\n";
}

echo "\n--- Related Models ---\n";
echo "Warehouses: " . Warehouse::count() . "\n";
echo "Countries: " . Country::count() . "\n";
echo "Regions: " . Region::count() . "\n";
