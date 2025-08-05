<?php

namespace App\DDD\Modules\Contract\Domain\ValueObjects;

use Carbon\Carbon;

class PricingPeriod
{
    private Carbon $startDate;
    private Carbon $endDate;
    private string $season;

    public function __construct(Carbon $startDate, Carbon $endDate, string $season = 'medium')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->season = $season;
    }

    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    public function getEndDate(): Carbon
    {
        return $this->endDate;
    }

    public function getSeason(): string
    {
        return $this->season;
    }

    public function contains(Carbon $date): bool
    {
        return $date->between($this->startDate, $this->endDate);
    }

    public function getDurationInDays(): int
    {
        return $this->startDate->diffInDays($this->endDate) + 1;
    }

    public function overlaps(PricingPeriod $other): bool
    {
        return $this->startDate->lt($other->getEndDate()) && 
               $this->endDate->gt($other->getStartDate());
    }

    public function isHighSeason(): bool
    {
        return $this->season === 'high';
    }

    public function isLowSeason(): bool
    {
        return $this->season === 'low';
    }

    public function isMediumSeason(): bool
    {
        return $this->season === 'medium';
    }
} 