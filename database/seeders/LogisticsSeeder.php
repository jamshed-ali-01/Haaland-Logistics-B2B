<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@haalandlogistics.com'],
            [
                'name' => 'Admin User',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
                'status' => 'approved',
            ]
        );

        // System Settings
        \App\Models\SystemSetting::updateOrCreate(['key' => 'origin_service_fee'], ['value' => '3.00', 'type' => 'decimal']);
        \App\Models\SystemSetting::updateOrCreate(['key' => 'minimum_volume'], ['value' => '100.00', 'type' => 'decimal']);

        // Warehouses - Official Data
        $la = \App\Models\Warehouse::updateOrCreate(
            ['code' => 'LA'],
            [
                'name' => 'Los Angeles Warehouse',
                'address' => '15506 Minnesota Ave. Paramount CA. 90723',
                'opening_hours' => '9am - 4pm',
                'type' => 'origin'
            ]
        );

        $miami = \App\Models\Warehouse::updateOrCreate(
            ['code' => 'MIAMI'],
            [
                'name' => 'Miami Logistics Center',
                'address' => '6158 Nw 74 Ave. Miami FL. 33166',
                'opening_hours' => '9am - 4pm',
                'type' => 'origin'
            ]
        );

        $dallas = \App\Models\Warehouse::updateOrCreate(
            ['code' => 'DALLAS'],
            [
                'name' => 'Dallas Hub',
                'address' => 'Future Location - Dallas, TX',
                'opening_hours' => 'TBA',
                'type' => 'origin'
            ]
        );

        // Destination Hubs (POE)
        $rotterdam = \App\Models\Warehouse::updateOrCreate(
            ['code' => 'ROT'],
            [
                'name' => 'Rotterdam Hub',
                'address' => 'Main Port, Rotterdam, Netherlands',
                'type' => 'destination'
            ]
        );

        $valencia = \App\Models\Warehouse::updateOrCreate(
            ['code' => 'VAL'],
            [
                'name' => 'Valencia Terminal',
                'address' => 'Port of Valencia, Spain',
                'type' => 'destination'
            ]
        );

        $uk_poe = \App\Models\Warehouse::updateOrCreate(
            ['code' => 'UKPOE'],
            [
                'name' => 'UK POE',
                'address' => 'Port of London / Southampton, UK',
                'type' => 'destination'
            ]
        );

        // Countries
        $neth = \App\Models\Country::updateOrCreate(['name' => 'Netherlands'], ['has_regions' => true]);
        $eng = \App\Models\Country::updateOrCreate(['name' => 'England'], ['has_regions' => true]);

        // Regions
        $neth_wh = \App\Models\Region::updateOrCreate(['country_id' => $neth->id, 'name' => 'Whse hand-out']);
        $neth_all = \App\Models\Region::updateOrCreate(['country_id' => $neth->id, 'name' => 'All']);
        
        $eng_north = \App\Models\Region::updateOrCreate(['country_id' => $eng->id, 'name' => 'Manchester & north']);
        $eng_south = \App\Models\Region::updateOrCreate(['country_id' => $eng->id, 'name' => 'Manchester south']);

        // POE Mappings
        \App\Models\PoeMapping::updateOrCreate(['country_id' => $neth->id], ['warehouse_id' => $rotterdam->id]);
        \App\Models\PoeMapping::updateOrCreate(['country_id' => $eng->id], ['warehouse_id' => $uk_poe->id]);

        // Sample Tiered Rates for Netherlands (Whse hand-out)
        $rate1 = \App\Models\Rate::updateOrCreate(
            ['origin_id' => $la->id, 'country_id' => $neth->id, 'region_id' => $neth_wh->id, 'service_type' => 'Standard'],
            ['rate_per_cft' => 0.00]
        );
        \App\Models\RateTier::updateOrCreate(['rate_id' => $rate1->id, 'min_volume' => 110], ['price_per_cft' => 2.76]);
        \App\Models\RateTier::updateOrCreate(['rate_id' => $rate1->id, 'min_volume' => 170], ['price_per_cft' => 1.65]);

        // Sample Tiered Rates for England (Manchester & north)
        $rate2 = \App\Models\Rate::updateOrCreate(
            ['origin_id' => $miami->id, 'country_id' => $eng->id, 'region_id' => $eng_north->id, 'service_type' => 'Standard'],
            ['rate_per_cft' => 0.00]
        );
        \App\Models\RateTier::updateOrCreate(['rate_id' => $rate2->id, 'min_volume' => 110], ['price_per_cft' => 7.88]);
        \App\Models\RateTier::updateOrCreate(['rate_id' => $rate2->id, 'min_volume' => 170], ['price_per_cft' => 5.46]);
        \App\Models\RateTier::updateOrCreate(['rate_id' => $rate2->id, 'min_volume' => 350], ['price_per_cft' => 5.13]);
    }
}
