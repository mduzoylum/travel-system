<?php

namespace App\DDD\Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'name', 'city', 'country', 'address', 'stars', 'min_price', 'is_contracted', 
        'description', 'supplier_id', 'external_id', 'image', 'is_active'
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function supplier()
    {
        return $this->belongsTo(\App\DDD\Modules\Supplier\Domain\Entities\Supplier::class);
    }

}
