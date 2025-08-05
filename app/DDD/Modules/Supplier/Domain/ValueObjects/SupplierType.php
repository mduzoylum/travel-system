<?php

namespace App\DDD\Modules\Supplier\Domain\ValueObjects;

class SupplierType
{
    private string $type;

    public const OTELBEST = 'otelbest';
    public const X_FIRM = 'x_firm';
    public const Y_FIRM = 'y_firm';
    public const MANUAL = 'manual';

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getValue(): string
    {
        return $this->type;
    }

    public function isOtelBest(): bool
    {
        return $this->type === self::OTELBEST;
    }

    public function isX(): bool
    {
        return $this->type === self::X_FIRM;
    }

    public function isY(): bool
    {
        return $this->type === self::Y_FIRM;
    }

    public function isManual(): bool
    {
        return $this->type === self::MANUAL;
    }

    public function requiresApi(): bool
    {
        return !$this->isManual();
    }

    public static function getAvailableTypes(): array
    {
        return [
            self::OTELBEST => 'OtelBest',
            self::X_FIRM => 'X Firması',
            self::Y_FIRM => 'Y Firması',
            self::MANUAL => 'Manuel'
        ];
    }
} 