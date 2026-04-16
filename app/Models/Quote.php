<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'user_id', 'reference_number', 'origin_id', 'country_id', 'region_id',
        'volume_cbm', 'volume_cft', 'billable_volume_cft', 'rate_per_cft', 'total_price', 'service_type_id', 'service_type', 'status'
    ];

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
}
