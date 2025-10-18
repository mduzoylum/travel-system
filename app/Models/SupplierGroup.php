<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\DDD\Modules\Supplier\Domain\Entities\Supplier;

class SupplierGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Bu gruba ait tedarikçiler
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'group_id');
    }

    /**
     * Aktif tedarikçiler
     */
    public function activeSuppliers()
    {
        return $this->hasMany(Supplier::class, 'group_id')->where('is_active', true);
    }

    /**
     * Grup rengini getir
     */
    public function getColorAttribute($value)
    {
        return $value ?: '#007bff';
    }

    /**
     * Grup badge'i
     */
    public function getBadgeAttribute()
    {
        return '<span class="badge" style="background-color: ' . $this->color . '">' . $this->name . '</span>';
    }
}
