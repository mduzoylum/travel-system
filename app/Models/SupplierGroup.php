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
        'sort_order',
        'group_type'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Bu gruba ait tedarikçiler (many-to-many)
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_group_members');
    }

    /**
     * Aktif tedarikçiler
     */
    public function activeSuppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_group_members')
                    ->where('is_active', true);
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

    /**
     * Grup tipi kontrol metodları
     */
    public function isReportGroup(): bool
    {
        return $this->group_type === 'report';
    }

    public function isProfitGroup(): bool
    {
        return $this->group_type === 'profit';
    }

    public function isXmlGroup(): bool
    {
        return $this->group_type === 'xml';
    }

    public function isManualGroup(): bool
    {
        return $this->group_type === 'manual';
    }

    /**
     * Grup tipi etiketleri
     */
    public function getGroupTypeLabelAttribute(): string
    {
        return match($this->group_type) {
            'report' => 'Rapor Grubu',
            'profit' => 'Kar Grubu',
            'xml' => 'XML Tedarikçi',
            'manual' => 'Manuel Tedarikçi',
            default => 'Bilinmiyor'
        };
    }
}
