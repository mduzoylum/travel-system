<?php

namespace App\DDD\Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'name', 'city', 'country', 'address', 'stars', 'min_price', 'is_contracted', 
        'description', 'supplier_id', 'accounting_code', 'external_id', 'image', 'is_active',
        'unique_id', 'country_id', 'city_id', 'sub_destination_id',
        'latitude', 'longitude',
        'location_description', 'general_description', 'about',
        'payment_type', 'payment_period_type', 'payment_period_value'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hotel) {
            if (!$hotel->unique_id) {
                $hotel->unique_id = static::generateUniqueId();
            }
        });
    }

    /**
     * 8-9 haneli benzersiz ID üret
     */
    protected static function generateUniqueId(): string
    {
        do {
            // 8 veya 9 haneli rastgele sayı üret
            $length = rand(8, 9);
            $uniqueId = str_pad((string) rand(0, 999999999), $length, '0', STR_PAD_LEFT);
        } while (static::where('unique_id', $uniqueId)->exists());
        
        return $uniqueId;
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function supplier()
    {
        return $this->belongsTo(\App\DDD\Modules\Supplier\Domain\Entities\Supplier::class);
    }

    public function destinationCountry()
    {
        return $this->belongsTo(Destination::class, 'country_id');
    }

    public function destinationCity()
    {
        return $this->belongsTo(Destination::class, 'city_id');
    }

    public function destinationSub()
    {
        return $this->belongsTo(Destination::class, 'sub_destination_id');
    }

}
