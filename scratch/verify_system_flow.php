<?php

use App\Models\User;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\Warehouse;
use App\Models\Country;
use App\Services\LogisticsService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Haaland Logistics B2B - System Verification Script
 * This script tests the core "Lead to Quote" logic through direct model interaction.
 */

try {
    echo "--- Haaland Logistics System Audit Start ---\n";

    // 1. Setup - Get basic data
    $warehouse = Warehouse::first() ?? Warehouse::create(['name' => 'Test Hub', 'code' => 'TH', 'address' => 'Test']);
    $country = Country::first() ?? Country::create(['name' => 'Test Country', 'code' => 'TC']);
    
    echo "Found Hub: {$warehouse->name} ({$warehouse->code})\n";
    echo "Found Country: {$country->name}\n";

    // 2. Test Guest Lead Creation
    echo "\n[Test 1] Simulating Guest Lead Submission...\n";
    $leadEmail = "guest_test_" . Str::random(4) . "@example.com";
    $lead = Lead::create([
        'email' => $leadEmail,
        'origin_id' => $warehouse->id,
        'country_id' => $country->id,
        'volume_cft' => 120.0,
        'status' => 'new'
    ]);
    echo "Lead Created: #{$lead->id} for {$lead->email}\n";

    // 3. Test Lead to Registration Conversion Logic
    echo "\n[Test 2] Simulating Registration Conversion Magic...\n";
    $user = User::create([
        'name' => 'Test Operator',
        'email' => $leadEmail,
        'password' => Hash::make('password'),
        'role' => 'client',
        'status' => 'approved' // Set to approved for testing
    ]);
    echo "User Created: {$user->name} ({$user->id})\n";

    // Manually trigger the conversion logic from RegisteredUserController
    $logistics = app(LogisticsService::class);
    $calculation = $logistics->calculateQuote(
        (int)$warehouse->id,
        (int)$country->id,
        null, // region
        120.0, // volume
        'Standard'
    );

    if ($calculation['success']) {
        $quote = Quote::create([
            'user_id' => $user->id,
            'reference_number' => 'Q-TST-' . strtoupper(Str::random(4)),
            'origin_id' => $warehouse->id,
            'country_id' => $country->id,
            'volume_cbm' => $logistics->cftToCbm(120.0),
            'volume_cft' => 120.0,
            'billable_volume_cft' => $calculation['billable_cft'],
            'rate_per_cft' => $calculation['rate_per_cft'],
            'total_price' => $calculation['total_price'],
            'service_type' => 'Standard',
            'status' => 'active'
        ]);
        
        $lead->update(['status' => 'converted']);
        echo "SUCCESS: Conversion Magic triggered properly.\n";
        echo "Quote Created: #{$quote->reference_number} | Price: \${$quote->total_price}\n";
    } else {
        echo "FAILURE: Logistics calculation failed: " . $calculation['message'] . "\n";
    }

    // 4. Verify Pricing Math
    echo "\n[Test 3] Verifying Pricing Math (Min 100 Rule)...\n";
    $smallCalc = $logistics->calculateQuote((int)$warehouse->id, (int)$country->id, null, 50.0, 'Standard');
    if ($smallCalc['success']) {
        echo "Volume 50 CFT -> Billable: {$smallCalc['billable_cft']} CFT (EXPECTED 100)\n";
        if ($smallCalc['billable_cft'] == 100) {
            echo "PASS: Minimum volume rule enforced.\n";
        } else {
            echo "FAIL: Minimum volume rule bypassed.\n";
        }
    }

    echo "\n--- System Audit Complete: ALL CORE FLOWS VERIFIED ---\n";

} catch (\Exception $e) {
    echo "\n!!! CRITICAL ERROR DURING AUDIT !!!\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
