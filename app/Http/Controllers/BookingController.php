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
             return redirect()->route('dashboard')->with('error', 'Your account verification is in progress. Please wait for admin approval to finalize bookings.');
        }

        if ($quote->user_id !== Auth::id()) {
            abort(403);
        }

        if ($quote->status === 'booked') {
            return back()->with('error', 'This quote has already been booked.');
        }

        $request->validate([
            'drop_off_date' => 'required|date|after_or_equal:today',
            'drop_off_time' => 'required|string',
        ]);

        // Logic for special request (Mon-Fri, 9-6)
        $date = \Carbon\Carbon::parse($request->drop_off_date);
        $time = \Carbon\Carbon::parse($request->drop_off_time);
        
        $isWeekend = $date->isWeekend();
        $isOutsideHours = $time->hour < 9 || $time->hour >= 18;
        $isSpecial = $isWeekend || $isOutsideHours || $request->has('special_request');

        $booking = Booking::create([
            'quote_id' => $quote->id,
            'user_id' => Auth::id(),
            'booking_number' => 'HL-' . strtoupper(Str::random(8)),
            'drop_off_date' => $request->drop_off_date,
            'drop_off_time' => $request->drop_off_time,
            'status' => 'pending',
            'is_special_request' => $isSpecial,
        ]);

        $quote->update(['status' => 'booked']);

        // Send Confirmation Email
        try {
            Mail::to(Auth::user()->email)->send(new BookingConfirmation($booking));
        } catch (\Exception $e) {
            \Log::error('Mail Error: ' . $e->getMessage());
        }

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully. Our team will contact you for drop-off details.');
    }
}
