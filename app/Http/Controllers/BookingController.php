<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Quote;
use App\Mail\BookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class BookingController extends Controller
{
    public function index()
    {
        $bookings = Auth::user()->bookings()
            ->with(['quote.origin', 'quote.country', 'departure'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create(Quote $quote)
    {
        if (Auth::user()->status !== 'approved') {
            return redirect()->route('dashboard')->with('error', 'Your account verification is in progress. Please wait for admin approval to finalize bookings.');
        }

        if ($quote->user_id !== Auth::id()) {
            abort(403);
        }

        return view('bookings.create', compact('quote'));
    }

    public function store(Request $request, Quote $quote)
    {
        // Security check
        if (Auth::user()->status !== 'approved') {
             return redirect()->route('dashboard')->with('error', 'Your account verification is in progress. Please wait for admin approval to request bookings.');
        }

        if ($quote->user_id !== Auth::id()) {
            abort(403);
        }

        if ($quote->status !== 'active') {
            return back()->with('error', 'This quote is no longer available for booking.');
        }

        // Just update status to requested
        $quote->update(['status' => 'requested']);

        // Notify Admin (Logic can be added later)

        return redirect()->route('quotes.index')->with('success', 'Booking request sent successfully. Our team will review it and assign a vessel shortly.');
    }
}
