<?php

namespace App\DDD\Modules\Contract\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'code',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Üst destinasyon (parent)
     */
    public function parent()
    {
        return $this->belongsTo(Destination::class, 'parent_id');
    }

    /**
     * Alt destinasyonlar
     */
    public function children()
    {
        return $this->hasMany(Destination::class, 'parent_id');
    }

    /**
     * Ülkeler (type = country)
     */
    public static function countries()
    {
        return static::where('type', 'country')->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Şehirler (type = city)
     */
    public static function cities($countryId = null)
    {
        $query = static::where('type', 'city')->where('is_active', true);
        
        if ($countryId) {
            $query->where('parent_id', $countryId);
        }
        
        return $query->orderBy('name')->get();
    }

    /**
     * Alt destinasyonlar (type = sub_destination)
     */
    public static function subDestinations($cityId = null)
    {
        $query = static::where('type', 'sub_destination')->where('is_active', true);
        
        if ($cityId) {
            $query->where('parent_id', $cityId);
        }
        
        return $query->orderBy('name')->get();
    }
}
