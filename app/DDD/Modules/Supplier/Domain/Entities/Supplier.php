<?php

namespace App\DDD\Modules\Supplier\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use App\DDD\Modules\Supplier\Domain\ValueObjects\SupplierType;
use App\DDD\Modules\Supplier\Domain\ValueObjects\ApiCredentials;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'api_endpoint',
        'api_version',
        'api_credentials',
        'sync_frequency',
        'is_active',
        'sync_enabled',
        'last_sync_at'
    ];

    protected $casts = [
        'api_credentials' => 'array',
        'is_active' => 'boolean',
        'sync_enabled' => 'boolean',
        'last_sync_at' => 'datetime'
    ];

    public function hotels()
    {
        return $this->hasMany(\App\DDD\Modules\Contract\Models\Hotel::class);
    }

    public function contracts()
    {
        return $this->hasMany(\App\DDD\Modules\Contract\Models\Contract::class);
    }

    public function reservations()
    {
        return $this->hasMany(\App\DDD\Modules\Reservation\Models\Reservation::class);
    }

    public function getType(): SupplierType
    {
        return new SupplierType($this->type);
    }

    public function getApiCredentials(): ApiCredentials
    {
        return new ApiCredentials($this->api_credentials);
    }

    public function isOtelBest(): bool
    {
        return $this->type === 'otelbest';
    }

    public function isApiEnabled(): bool
    {
        return $this->is_active && $this->sync_enabled;
    }

    public function markAsSynced(): void
    {
        $this->update(['last_sync_at' => now()]);
    }
} 