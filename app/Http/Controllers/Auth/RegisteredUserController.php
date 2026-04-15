<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lead;
use App\Models\Quote;
use App\Services\LogisticsService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Conversion Logic: Create Quote from Lead data if present
        if ($request->lead_id && $request->origin_id && $request->country_id) {
            try {
                $logistics = app(LogisticsService::class);
                $calculation = $logistics->calculateQuote(
                    (int)$request->origin_id,
                    (int)$request->country_id,
                    $request->region_id ? (int)$request->region_id : null,
                    (float)$request->volume,
                    'Standard' // Default service type
                );

                if ($calculation['success']) {
                    Quote::create([
                        'user_id' => $user->id,
                        'reference_number' => 'Q-' . date('y') . '-' . strtoupper(Str::random(6)),
                        'origin_id' => $request->origin_id,
                        'country_id' => $request->country_id,
                        'region_id' => $request->region_id,
                        'volume_cbm' => $logistics->cftToCbm($request->volume),
                        'volume_cft' => $request->volume,
                        'billable_volume_cft' => $calculation['billable_cft'],
                        'rate_per_cft' => $calculation['rate_per_cft'],
                        'total_price' => $calculation['total_price'],
                        'service_type' => 'Standard',
                        'status' => 'active'
                    ]);

                    Lead::where('id', $request->lead_id)->update(['status' => 'converted']);
                    
                    return redirect()->route('quotes.index')->with('success', 'Welcome! We have automatically generated your requested quote.');
                }
            } catch (\Exception $e) {
                \Log::error('Conversion Error: ' . $e->getMessage());
            }
        }

        return redirect(route('dashboard', absolute: false));
    }
}
