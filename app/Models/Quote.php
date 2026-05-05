<?php

namespace App\Models;

use App\Notifications\SystemNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class Quote extends Model
{
    protected $fillable = [
        'user_id', 'reference_number', 'origin_id', 'country_id', 'region_id', 'destination_warehouse_id',
        'volume_cbm', 'volume_cft', 'billable_volume_cft', 'rate_per_cft', 'total_price', 'shipping_service_id', 'service_type_id', 'service_type', 'status', 'admin_notes'
    ];

    public function destination()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function origin()
    {
        return $this->belongsTo(Warehouse::class, 'origin_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function booking()
    {
        return $this->hasOne(Booking::class);
    }

    protected static function booted()
    {
        static::created(function ($quote) {
            // Notify Admin
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new SystemNotification([
                'title' => 'New Quote Request',
                'message' => "A new quote request ({$quote->reference_number}) has been submitted by {$quote->user->name}.",
                'type' => 'info',
                'link' => route('admin.quotes.show', $quote),
                'icon' => 'document-text'
            ]));
        });
    }
}
