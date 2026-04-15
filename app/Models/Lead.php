<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'email', 'origin_id', 'country_id', 'region_id', 
        'volume_cft', 'service_type', 'message', 'status'
    ];

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
}
