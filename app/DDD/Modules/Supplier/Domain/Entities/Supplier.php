<?php

namespace App\DDD\Modules\Supplier\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use App\DDD\Modules\Supplier\Domain\ValueObjects\SupplierType;
use App\DDD\Modules\Supplier\Domain\ValueObjects\ApiCredentials;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'types',
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
        'types' => 'array',
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

    public function getTypes(): array
    {
        return $this->types ?? [];
    }

    public function getFirstType(): ?string
    {
        return !empty($this->types) ? $this->types[0] : null;
    }

    public function getApiCredentials(): ApiCredentials
    {
        return new ApiCredentials($this->api_credentials);
    }

    public function hasType(string $type): bool
    {
        return in_array($type, $this->types ?? []);
    }

    public function isOtelBest(): bool
    {
        return $this->hasType('otelbest');
    }

    public function isHotel(): bool
    {
        return $this->hasType('hotel');
    }

    public function isFlight(): bool
    {
        return $this->hasType('flight');
    }

    public function isCar(): bool
    {
        return $this->hasType('car');
    }

    public function isActivity(): bool
    {
        return $this->hasType('activity');
    }

    public function isTransfer(): bool
    {
        return $this->hasType('transfer');
    }

    public function getTypesLabel(): string
    {
        $typeLabels = [
            'hotel' => 'Otel',
            'flight' => 'Uçuş',
            'car' => 'Araç Kiralama',
            'activity' => 'Aktivite',
            'transfer' => 'Transfer'
        ];

        $labels = [];
        foreach ($this->types ?? [] as $type) {
            $labels[] = $typeLabels[$type] ?? ucfirst($type);
        }

        return implode(', ', $labels);
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