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
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Conversion Logic: Create Quote from any existing Leads for this email
        $leads = Lead::where('email', $user->email)->where('status', 'new')->get();
        
        foreach ($leads as $lead) {
            if ($lead->origin_id && $lead->country_id && $lead->volume_cft) {
                try {
                    $logistics = app(LogisticsService::class);
                    $calculation = $logistics->calculateQuote(
                        (int)$lead->origin_id,
                        (int)$lead->country_id,
                        $lead->region_id ? (int)$lead->region_id : null,
                        (float)$lead->volume_cft,
                        'Standard'
                    );

                    if ($calculation['success']) {
                        Quote::create([
                            'user_id' => $user->id,
                            'reference_number' => 'Q-' . date('y') . '-' . strtoupper(Str::random(6)),
                            'origin_id' => $lead->origin_id,
                            'country_id' => $lead->country_id,
                            'region_id' => $lead->region_id,
                            'volume_cbm' => $logistics->cftToCbm($lead->volume_cft),
                            'volume_cft' => $lead->volume_cft,
                            'billable_volume_cft' => $calculation['billable_cft'],
                            'rate_per_cft' => $calculation['rate_per_cft'],
                            'total_price' => $calculation['total_price'],
                            'service_type' => 'Standard',
                            'status' => 'active'
                        ]);

                        $lead->update(['status' => 'converted']);
                    }
                } catch (\Exception $e) {
                    \Log::error('Conversion Error for Lead ID ' . $lead->id . ': ' . $e->getMessage());
                }
            }
        }

        if ($leads->count() > 0) {
            return redirect()->route('quotes.index')->with('success', 'Welcome! We have automatically generated quotes from your previous inquiries.');
        }

        return redirect(route('dashboard', absolute: false));
    }
}
