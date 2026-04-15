<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Quote;
use App\Models\Lead;
use App\Mail\NewLeadAlert;
use App\Services\LogisticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'origin_id' => 'nullable|exists:warehouses,id',
            'country_id' => 'nullable|exists:countries,id',
            'region_id' => 'nullable|exists:regions,id',
            'volume' => 'nullable|numeric|min:0.01',
            'volume_unit' => 'nullable|in:CBM,CFT',
        ]);

        $volumeCft = $request->volume;
        if ($request->volume_unit === 'CBM' && $request->volume) {
            $volumeCft = $request->volume * 35.3147;
        }

        $lead = Lead::create([
            'email' => $request->email,
            'origin_id' => $request->origin_id,
            'country_id' => $request->country_id,
            'region_id' => $request->region_id,
            'volume_cft' => $volumeCft,
            'status' => 'new'
        ]);

        // Send Admin Notification
        try {
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                Mail::to($admin->email)->send(new NewLeadAlert($lead));
            }
        } catch (\Exception $e) {
            \Log::error('Mail Error: ' . $e->getMessage());
        }

        // Logic for Authenticated Users
        if (Auth::check()) {
            try {
                $logistics = app(LogisticsService::class);
                $calculation = $logistics->calculateQuote(
                    (int)$request->origin_id,
                    (int)$request->country_id,
                    $request->region_id ? (int)$request->region_id : null,
                    (float)$volumeCft,
                    'Standard'
                );

                if ($calculation['success']) {
                    Quote::create([
                        'user_id' => Auth::id(),
                        'reference_number' => 'Q-' . date('y') . '-' . strtoupper(Str::random(6)),
                        'origin_id' => $request->origin_id,
                        'country_id' => $request->country_id,
                        'region_id' => $request->region_id,
                        'volume_cbm' => $logistics->cftToCbm($volumeCft),
                        'volume_cft' => $volumeCft,
                        'billable_volume_cft' => $calculation['billable_cft'],
                        'rate_per_cft' => $calculation['rate_per_cft'],
                        'total_price' => $calculation['total_price'],
                        'service_type' => 'Standard',
                        'status' => 'active'
                    ]);

                    $lead->update(['status' => 'converted']);
                    return redirect()->route('quotes.index')->with('success', 'Your inquiry has been converted to a formal quote.');
                }
            } catch (\Exception $e) {
                \Log::error('Auth Lead Conversion Error: ' . $e->getMessage());
            }
        }

        return redirect()->route('register', [
            'email' => $request->email,
            'lead_id' => $lead->id,
            'origin_id' => $request->origin_id,
            'country_id' => $request->country_id,
            'region_id' => $request->region_id,
            'volume' => $volumeCft,
            'volume_unit' => 'CFT'
        ])->with('success', 'Inquiry received! Complete your registration below to view your full quote.');
    }
}
