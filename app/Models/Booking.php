<?php

namespace App\Models;

use App\Notifications\SystemNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class Booking extends Model
{
    protected $fillable = [
        'quote_id', 'user_id', 'booking_number', 'drop_off_date', 
        'drop_off_time', 'status', 'departure_id', 'is_special_request',
        'external_reference', 'external_volume_cft', 'destination_warehouse_id', 'admin_notes'
    ];

    public function destination()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function departure()
    {
        return $this->belongsTo(Departure::class);
    }

    protected static function booted()
    {
        static::created(function ($booking) {
            // Notify Admin
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new SystemNotification([
                'title' => 'New Booking Received',
                'message' => "A new booking ({$booking->booking_number}) has been created by {$booking->user->name}.",
                'type' => 'success',
                'link' => route('admin.bookings.index'),
                'icon' => 'shopping-cart'
            ]));
        });

        static::updated(function ($booking) {
            if ($booking->isDirty('status')) {
                // Notify User
                $booking->user->notify(new SystemNotification([
                    'title' => 'Booking Status Updated',
                    'message' => "Your booking ({$booking->booking_number}) is now: " . ucfirst($booking->status),
                    'type' => 'info',
                    'link' => route('bookings.index'),
                    'icon' => 'refresh'
                ]));
            }
        });
    }
}
