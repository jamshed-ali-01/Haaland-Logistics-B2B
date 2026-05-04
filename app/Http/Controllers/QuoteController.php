<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Country;
use App\Services\LogisticsService;
use App\Mail\QuoteSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    protected $logistics;

    public function __construct(LogisticsService $logistics)
    {
        $this->logistics = $logistics;
    }

    public function index()
    {
        $quotes = Auth::user()->quotes()
            ->with(['origin', 'country', 'region'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('quotes.index', compact('quotes'));
    }

    public function create()
    {
        // Only approved users can create formal quotes
        if (Auth::user()->status !== 'approved') {
            return redirect()->route('dashboard')->with('error', 'Your account verification is in progress. Please wait for admin approval to request formal quotes.');
        }

        $warehouses = Warehouse::all();
        $countries = Country::with('regions')->get();
        
        $prefill = [
            'origin_id' => request('origin_id'),
            'country_id' => request('country_id'),
            'region_id' => request('region_id'),
            'volume' => request('volume'),
            'volume_unit' => request('volume_unit'),
        ];

        $minVol = \App\Models\SystemSetting::where('key', 'minimum_volume')->first()?->value ?? 100;

        return view('quotes.create', compact('warehouses', 'countries', 'prefill', 'minVol'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'origin_id' => 'required|exists:warehouses,id',
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'nullable|exists:regions,id',
            'volume' => 'required|numeric|min:0.01',
            'volume_unit' => 'required|in:CBM,CFT',
            'service_type' => 'required|string',
        ]);

        $cft = $request->volume_unit === 'CBM' 
            ? $this->logistics->cbmToCft($request->volume) 
            : $request->volume;

        $calculation = $this->logistics->calculateQuote(
            (int)$request->origin_id,
            (int)$request->country_id,
            $request->region_id ? (int)$request->region_id : null,
            (float)$cft,
            $request->service_type
        );

        return response()->json($calculation);
    }

    public function store(Request $request)
    {
        $request->validate([
            'origin_id' => 'required|exists:warehouses,id',
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'nullable|exists:regions,id',
            'volume' => 'required|numeric|min:0.01',
            'volume_unit' => 'required|in:CBM,CFT',
            'service_type' => 'required|string',
        ]);

        $cft = $request->volume_unit === 'CBM' 
            ? $this->logistics->cbmToCft($request->volume) 
            : $request->volume;

        $calculation = $this->logistics->calculateQuote(
            (int)$request->origin_id,
            (int)$request->country_id,
            $request->region_id ? (int)$request->region_id : null,
            (float)$cft,
            $request->service_type
        );

        if (!$calculation['success']) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $calculation['message']], 422);
            }
            return back()->withInput()->with('error', $calculation['message']);
        }

        $quote = Quote::create([
            'user_id' => Auth::id(),
            'reference_number' => 'Q-' . date('y') . '-' . strtoupper(Str::random(6)),
            'origin_id' => $request->origin_id,
            'country_id' => $request->country_id,
            'region_id' => $request->region_id,
            'destination_warehouse_id' => $calculation['destination_warehouse_id'],
            'volume_cbm' => $this->logistics->cftToCbm($cft),
            'volume_cft' => $cft,
            'billable_volume_cft' => $calculation['billable_cft'],
            'rate_per_cft' => $calculation['rate_per_cft'],
            'total_price' => $calculation['total_price'],
            'service_type' => $request->service_type,
            'status' => 'active'
        ]);

        // Send Quote Email
        try {
            Mail::to(Auth::user()->email)->send(new QuoteSummary($quote));
        } catch (\Exception $e) {
            \Log::error('Mail Error: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Quote generated successfully.', 'redirect' => route('quotes.index')]);
        }
        
        return redirect()->route('quotes.index')->with('success', 'Quote generated successfully.');
    }
}
