<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rate;
use App\Models\RateTier;
use App\Models\Departure;
use App\Models\Warehouse;
use App\Models\Country;
use App\Models\Region;
use App\Models\Booking;
use App\Models\Quote;
use App\Models\ShippingService;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $query = User::where('role', 'client');

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Filter by Company
        if ($request->has('company') && $request->company != '') {
            $query->where('company_name', $request->company);
        }

        // Sorting
        $sort = $request->get('sort', 'company_name'); // Default to company_name for grouping
        $direction = $request->get('direction', 'asc');
        
        $allowedSorts = ['name', 'company_name', 'created_at', 'status'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        }
        
        // Secondary sort to ensure consistency
        if ($sort !== 'name') {
            $query->orderBy('name', 'asc');
        }

        $users = $query->paginate(15)->withQueryString();
        
        $companies = User::where('role', 'client')
            ->whereNotNull('company_name')
            ->distinct()
            ->pluck('company_name');

        return view('admin.users', compact('users', 'companies'));
    }

    public function pauseUser(User $user)
    {
        $user->update(['status' => 'paused']);
        return back()->with('success', "User {$user->name} has been paused.");
    }

    public function quotes()
    {
        $quotes = Quote::with(['user', 'origin', 'country', 'region', 'destination'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $departures = \App\Models\Departure::whereIn('status', ['open', 'active'])->get();

        return view('admin.quotes', compact('quotes', 'departures'));
    }

    public function showQuote(Quote $quote)
    {
        $quote->load(['user', 'origin', 'country', 'region', 'destination', 'booking']);
        $departures = \App\Models\Departure::whereIn('status', ['open', 'active'])->get();
        return view('admin.quote_details', compact('quote', 'departures'));
    }

    public function updateQuoteNotes(Request $request, Quote $quote)
    {
        $request->validate(['admin_notes' => 'nullable|string']);
        $quote->update(['admin_notes' => $request->admin_notes]);
        return back()->with('success', 'Internal notes updated successfully.');
    }

    public function acceptQuote(Quote $quote)
    {
        if ($quote->booking) {
            return back()->with('error', 'Booking already exists for this quote.');
        }

        $booking = \App\Models\Booking::create([
            'quote_id' => $quote->id,
            'user_id' => $quote->user_id,
            'booking_number' => 'HL-' . strtoupper(Str::random(8)),
            'status' => 'pending',
            'destination_warehouse_id' => $quote->destination_warehouse_id,
            'departure_id' => request('departure_id'),
        ]);

        $quote->update(['status' => 'booked']);

        return back()->with('success', "Quote {$quote->reference_number} has been accepted and converted to Booking {$booking->booking_number}.");
    }

    public function rejectQuote(Quote $quote)
    {
        $quote->update(['status' => 'rejected']);

        // Notify User
        $quote->user->notify(new \App\Notifications\SystemNotification([
            'title' => 'Quote Update',
            'message' => 'Your quote ' . $quote->reference_number . ' has been reviewed and rejected.',
            'type' => 'error',
            'link' => route('quotes.index'),
            'icon' => 'x-circle'
        ]));

        return back()->with('success', "Quote {$quote->reference_number} has been rejected.");
    }

    public function leads()
    {
        $leads = \App\Models\Lead::with(['origin', 'country', 'region'])->orderBy('created_at', 'desc')->get();
        return view('admin.leads', compact('leads'));
    }

    public function settings()
    {
        $settings = \App\Models\SystemSetting::all()->keyBy('key');
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'origin_service_fee' => 'sometimes|required|numeric|min:0',
            'minimum_volume' => 'sometimes|required|numeric|min:0',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
        ]);

        $settingsToUpdate = [
            'origin_service_fee' => 'decimal',
            'origin_fee_usd' => 'decimal',
            'eur_to_usd_rate' => 'decimal',
            'minimum_volume' => 'decimal',
            'mail_host' => 'string',
            'mail_port' => 'integer',
            'mail_username' => 'string',
            'mail_password' => 'string',
            'mail_encryption' => 'string',
            'mail_from_address' => 'string',
            'mail_from_name' => 'string',
        ];

        foreach ($settingsToUpdate as $key => $type) {
            if ($request->has($key)) {
                \App\Models\SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->get($key), 'type' => $type]
                );
            }
        }

        return back()->with('success', 'System settings updated successfully.');
    }

    public function testEmail(Request $request)
    {
        try {
            $fromAddress = \App\Models\SystemSetting::where('key', 'mail_from_address')->value('value') 
                ?? config('mail.from.address');

            if (!$fromAddress) {
                return back()->with('error', 'Please configure a "From Email Address" before testing.');
            }

            Mail::raw('This is a test email to verify SMTP settings for Haaland Logistics.', function ($message) use ($fromAddress) {
                $message->to($fromAddress)
                    ->subject('SMTP Connection Test');
            });

            return back()->with('success', 'Test email sent successfully to ' . $fromAddress);
        } catch (\Exception $e) {
            return back()->with('error', 'Mail Error: ' . $e->getMessage());
        }
    }

    public function rates()
    {
        $rates = Rate::with(['origin', 'country', 'region', 'tiers', 'shippingService', 'serviceType'])
            ->orderBy('origin_id')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('origin_id');
            
        $warehouses = Warehouse::where('type', 'origin')->get();
        $countries = Country::with('regions')->orderBy('name')->get();
        $regions = Region::all();
        $shippingServices = ShippingService::all();
        $serviceTypes = ServiceType::all();

        return view('admin.rates', compact('rates', 'warehouses', 'countries', 'regions', 'shippingServices', 'serviceTypes'));
    }

    public function storeRate(Request $request)
    {
        $request->validate([
            'origin_id' => 'required|exists:warehouses,id',
            'country_id' => 'required|exists:countries,id',
            'shipping_service_id' => 'required|exists:shipping_services,id',
            'service_type_id' => 'required|exists:service_types,id',
            'tiers' => 'required|array',
            'tiers.*.min_volume' => 'required|numeric',
            'tiers.*.price_per_cft' => 'nullable|numeric',
        ]);

        $rate = Rate::create([
            'origin_id' => $request->origin_id,
            'country_id' => $request->country_id,
            'region_id' => $request->region_id,
            'shipping_service_id' => $request->shipping_service_id,
            'service_type_id' => $request->service_type_id,
            // Keep legacy strings for backward compatibility during transition if needed
            'service' => ShippingService::find($request->shipping_service_id)->name,
            'service_type' => ServiceType::find($request->service_type_id)->name,
        ]);

        foreach ($request->tiers as $tier) {
            if (isset($tier['price_per_cft']) && $tier['price_per_cft'] !== '') {
                RateTier::create([
                    'rate_id' => $rate->id,
                    'min_volume' => $tier['min_volume'],
                    'price_per_cft' => $tier['price_per_cft'],
                ]);
            }
        }

        return back()->with('success', 'Rate created successfully.');
    }

    public function updateRate(Request $request, Rate $rate)
    {
        $request->validate([
            'origin_id' => 'required|exists:warehouses,id',
            'country_id' => 'required|exists:countries,id',
            'shipping_service_id' => 'required|exists:shipping_services,id',
            'service_type_id' => 'required|exists:service_types,id',
            'tiers' => 'required|array',
            'tiers.*.min_volume' => 'required|numeric',
            'tiers.*.price_per_cft' => 'nullable|numeric',
        ]);

        $rate->update([
            'origin_id' => $request->origin_id,
            'country_id' => $request->country_id,
            'region_id' => $request->region_id,
            'shipping_service_id' => $request->shipping_service_id,
            'service_type_id' => $request->service_type_id,
            'service' => ShippingService::find($request->shipping_service_id)->name,
            'service_type' => ServiceType::find($request->service_type_id)->name,
        ]);

        // Recreate tiers to ensure clean state
        $rate->tiers()->delete();
        foreach ($request->tiers as $tier) {
            if (isset($tier['price_per_cft']) && $tier['price_per_cft'] !== '') {
                RateTier::create([
                    'rate_id' => $rate->id,
                    'min_volume' => $tier['min_volume'],
                    'price_per_cft' => $tier['price_per_cft'],
                ]);
            }
        }

        return back()->with('success', 'Rate updated successfully.');
    }

    public function destroyRate(Rate $rate)
    {
        $rate->tiers()->delete();
        $rate->delete();

        return back()->with('success', 'Rate and associated tiers deleted successfully.');
    }

    public function departures()
    {
        $departures = Departure::withCount('bookings')
            ->orderBy('departure_date', 'asc')
            ->paginate(10);

        return view('admin.departures', compact('departures'));
    }

    public function storeDeparture(Request $request)
    {
        $request->validate([
            'vessel_name' => 'required|string|max:255',
            'voyage_number' => 'required|string|max:100',
            'cutoff_date' => 'required|date',
            'departure_date' => 'required|date|after:cutoff_date',
            'arrival_date' => 'required|date|after:departure_date',
            'capacity_cft' => 'required|numeric|min:1',
        ]);

        Departure::create([
            'vessel_name' => $request->vessel_name,
            'voyage_number' => $request->voyage_number,
            'cutoff_date' => $request->cutoff_date,
            'departure_date' => $request->departure_date,
            'arrival_date' => $request->arrival_date,
            'capacity_cft' => $request->capacity_cft,
            'status' => 'active',
        ]);

        return back()->with('success', 'Vessel departure published successfully.');
    }

    public function updateDeparture(Request $request, Departure $departure)
    {
        $request->validate([
            'vessel_name' => 'required|string|max:255',
            'voyage_number' => 'required|string|max:100',
            'cutoff_date' => 'required|date',
            'departure_date' => 'required|date|after:cutoff_date',
            'arrival_date' => 'required|date|after:departure_date',
            'capacity_cft' => 'required|numeric|min:1',
        ]);

        $departure->update($request->all());

        return back()->with('success', 'Vessel schedule updated successfully.');
    }

    public function destroyDeparture(Departure $departure)
    {
        if ($departure->bookings()->count() > 0) {
            return back()->with('error', 'Cannot delete a vessel with active bookings.');
        }

        $departure->delete();

        return back()->with('success', 'Vessel departure removed successfully.');
    }

    public function warehouses()
    {
        $warehouses = Warehouse::with(['rates', 'countries'])
            ->withCount('rates')
            ->orderBy('type', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $countries = Country::orderBy('name')->get();

        return view('admin.warehouses', compact('warehouses', 'countries'));
    }

    public function storeWarehouse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:warehouses',
            'address' => 'required|string',
            'type' => 'required|string|in:origin,destination',
        ]);

        Warehouse::create($request->all());

        return back()->with('success', 'Warehouse added successfully.');
    }

    public function updateWarehouse(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:warehouses,code,' . $warehouse->id,
            'address' => 'required|string',
            'type' => 'required|string|in:origin,destination',
        ]);

        $warehouse->update($request->all());

        return back()->with('success', 'Warehouse updated successfully.');
    }

    public function storePoeMapping(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'country_id' => 'required|exists:countries,id',
        ]);

        \App\Models\PoeMapping::updateOrCreate([
            'country_id' => $request->country_id,
        ], [
            'warehouse_id' => $request->warehouse_id,
        ]);

        return back()->with('success', 'Country mapped to POE successfully.');
    }

    public function destroyPoeMapping(\App\Models\PoeMapping $mapping)
    {
        $mapping->delete();
        return back()->with('success', 'Mapping removed successfully.');
    }

    public function destroyWarehouse(Warehouse $warehouse)
    {
        if ($warehouse->rates()->count() > 0) {
            return back()->with('error', 'Cannot delete a warehouse that is linked to active shipping rates.');
        }

        $warehouse->delete();

        return back()->with('success', 'Warehouse deleted successfully.');
    }

    public function externalShipments()
    {
        $bookings = Booking::whereNull('quote_id')
            ->with(['user', 'departure'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $users = User::where('role', 'client')->get();
        $departures = Departure::where('status', 'active')->get();

        return view('admin.external_shipments', compact('bookings', 'users', 'departures'));
    }

    public function operations()
    {
        $departures = Departure::with(['bookings.user', 'bookings.quote'])
            ->where('departure_date', '>=', now()->subDays(30))
            ->orderBy('departure_date', 'asc')
            ->get();

        return view('admin.operations', compact('departures'));
    }

    public function storeExternalShipment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'external_reference' => 'required|string',
            'external_volume_cft' => 'required|numeric|min:0.1',
            'drop_off_date' => 'required|date',
        ]);

        Booking::create([
            'user_id' => $request->user_id,
            'booking_number' => 'EXT-' . strtoupper(Str::random(8)),
            'external_reference' => $request->external_reference,
            'external_volume_cft' => $request->external_volume_cft,
            'drop_off_date' => $request->drop_off_date,
            'status' => 'received',
            'departure_id' => $request->departure_id,
        ]);

        return back()->with('success', 'External shipment logged successfully.');
    }

    public function toggleBookingStatus(Booking $booking)
    {
        $newStatus = $booking->status === 'pending' ? 'scheduled' : 'pending';
        $booking->update(['status' => $newStatus]);
        
        return back()->with('success', 'Booking status updated to ' . $newStatus);
    }

    public function bookings()
    {
        $bookings = Booking::whereNotNull('quote_id')
            ->with(['user', 'quote.origin', 'quote.country', 'departure'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.bookings', compact('bookings'));
    }

    public function approveUser(User $user)
    {
        $user->update(['status' => 'approved']);

        // Notify User
        $user->notify(new \App\Notifications\SystemNotification([
            'title' => 'Account Approved',
            'message' => 'Your Haaland Logistics account has been approved. You can now start booking shipments.',
            'type' => 'success',
            'link' => route('dashboard'),
            'icon' => 'user-check'
        ]));

        return back()->with('success', "User {$user->name} has been approved.");
    }

    public function rejectUser(User $user)
    {
        $user->update(['status' => 'rejected']);

        return back()->with('success', "User {$user->name} has been rejected.");
    }

    // Shipping Services
    public function services()
    {
        $services = ShippingService::withCount('rates')->paginate(10);
        return view('admin.services', compact('services'));
    }

    public function storeService(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:shipping_services,name', 'description' => 'nullable|string']);
        ShippingService::create($request->only('name', 'description'));
        return back()->with('success', 'Service category created.');
    }

    public function updateService(Request $request, ShippingService $service)
    {
        $request->validate(['name' => 'required|string|unique:shipping_services,name,' . $service->id, 'description' => 'nullable|string']);
        $service->update($request->only('name', 'description'));
        return back()->with('success', 'Service category updated.');
    }

    public function destroyService(ShippingService $service)
    {
        if ($service->rates()->exists()) {
            return back()->with('error', 'Cannot delete service that is linked to active rates.');
        }
        $service->delete();
        return back()->with('success', 'Service category deleted.');
    }

    // Service Types
    public function serviceTypes()
    {
        $serviceTypes = ServiceType::withCount(['rates', 'quotes'])->paginate(10);
        return view('admin.service-types', compact('serviceTypes'));
    }

    public function storeServiceType(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:service_types,name', 'description' => 'nullable|string']);
        ServiceType::create($request->only('name', 'description'));
        return back()->with('success', 'Service type created.');
    }

    public function updateServiceType(Request $request, ServiceType $serviceType)
    {
        $request->validate(['name' => 'required|string|unique:service_types,name,' . $serviceType->id, 'description' => 'nullable|string']);
        $serviceType->update($request->only('name', 'description'));
        return back()->with('success', 'Service type updated.');
    }

    public function destroyServiceType(ServiceType $serviceType)
    {
        if ($serviceType->rates()->exists() || $serviceType->quotes()->exists()) {
            return back()->with('error', 'Cannot delete service type that is linked to active rates or quotes.');
        }
        $serviceType->delete();
        return back()->with('success', 'Service type deleted.');
    }

    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $stats = [
                // User Stats
                'total_clients' => User::where('role', 'client')->count(),
                'pending_approvals' => User::where('status', 'pending')->count(),
                
                // Inventory/Settings
                'total_warehouses' => Warehouse::count(),
                'total_countries' => Country::count(),
                'total_regions' => Region::count(),
                'total_services' => ShippingService::count(),
                'total_service_types' => ServiceType::count(),
                'total_rates' => Rate::count(),
                
                // Operational
                'active_departures' => Departure::where('departure_date', '>', now())->count(),
                'total_voyages' => Departure::count(),
                'special_requests_count' => Booking::where('is_special_request', true)->count(),
                
                'total_leads' => \App\Models\Lead::count(),
                'new_leads_count' => \App\Models\Lead::where('status', 'new')->count(),
                
                'total_quotes' => $totalQuotes = Quote::count(),
                'total_bookings' => $totalBookings = Booking::count(),
                'success_rate' => $totalQuotes > 0 ? number_format(($totalBookings / $totalQuotes) * 100, 1) : 0,
                'estimated_revenue' => Booking::whereNotNull('quote_id')->join('quotes', 'bookings.quote_id', '=', 'quotes.id')->sum('quotes.total_price'),
                'total_volume_cft' => Booking::whereNotNull('quote_id')->join('quotes', 'bookings.quote_id', '=', 'quotes.id')->sum('quotes.volume_cft') + Booking::whereNull('quote_id')->sum('external_volume_cft'),
                
                // Recent Activity
                'recent_quotes' => Quote::with(['user', 'country'])->latest()->take(5)->get(),
                'recent_bookings' => Booking::with(['user', 'quote.country'])->latest()->take(5)->get(),
                'recent_leads' => \App\Models\Lead::latest()->take(5)->get(),
                'upcoming_departures' => Departure::where('departure_date', '>', now())->orderBy('departure_date', 'asc')->take(3)->get(),
            ];
            
            return view('dashboard', compact('stats'));
        } else {
            // Client Stats
            $stats = [
                'my_quotes_count' => Quote::where('user_id', $user->id)->count(),
                'my_bookings_count' => Booking::where('user_id', $user->id)->count(),
                'my_total_spending' => Booking::where('bookings.user_id', $user->id)->join('quotes', 'bookings.quote_id', '=', 'quotes.id')->sum('quotes.total_price'),
                'my_volume_cft' => Booking::where('bookings.user_id', $user->id)->join('quotes', 'bookings.quote_id', '=', 'quotes.id')->sum('quotes.volume_cft'),
                'recent_my_quotes' => Quote::where('user_id', $user->id)->latest()->take(5)->get(),
                'recent_my_bookings' => Booking::where('user_id', $user->id)->with('quote.country')->latest()->take(5)->get(),
                'upcoming_departures' => Departure::where('departure_date', '>', now())->orderBy('departure_date', 'asc')->take(5)->get(),
            ];

            $warehouses = Warehouse::all();
            $countries = Country::with('regions')->get();

            return view('dashboard', compact('stats', 'warehouses', 'countries'));
        }
    }
}
