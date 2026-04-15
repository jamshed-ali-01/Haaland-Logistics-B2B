<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departure extends Model
{
    protected $fillable = [
        'vessel_name', 'voyage_number', 'cutoff_date', 'departure_date',
        'arrival_date', 'capacity_cft', 'status'
    ];

    protected $casts = [
        'cutoff_date' => 'datetime',
        'departure_date' => 'datetime',
        'arrival_date' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getUtilizationPercentageAttribute()
    {
        if ($this->capacity_cft <= 0) return 0;

        $bookedVolume = $this->bookings->sum(function($booking) {
            if ($booking->quote_id) {
                return $booking->quote->billable_volume_cft;
            }
            return $booking->external_volume_cft ?? 0;
        });

        return round(($bookedVolume / $this->capacity_cft) * 100, 1);
    }
}
