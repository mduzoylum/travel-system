<?php

namespace App\DDD\Modules\Credit\Domain\Entities;

use App\DDD\Modules\Credit\Domain\ValueObjects\Money;
use App\DDD\Modules\Credit\Domain\ValueObjects\CreditLimit;
use App\DDD\Modules\Credit\Domain\Events\CreditAdded;
use App\DDD\Modules\Credit\Domain\Events\CreditUsed;
use App\DDD\Modules\Credit\Domain\Exceptions\InsufficientCreditException;
use Illuminate\Database\Eloquent\Model;

class CreditAccount extends Model
{
    protected $fillable = [
        'firm_id',
        'balance',
        'credit_limit',
        'currency',
        'is_active'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function firm()
    {
        return $this->belongsTo(\App\DDD\Modules\Firm\Models\Firm::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\DDD\Modules\Credit\Domain\Entities\CreditTransaction::class);
    }

    public function addCredit(Money $amount, string $description = null): void
    {
        $this->balance += $amount->getAmount();
        $this->save();

        event(new CreditAdded($this, $amount, $description));
    }

    public function useCredit(Money $amount, string $description = null): void
    {
        if (!$this->hasSufficientCredit($amount)) {
            throw new InsufficientCreditException("Yetersiz kredi bakiyesi");
        }

        $this->balance -= $amount->getAmount();
        $this->save();

        event(new CreditUsed($this, $amount, $description));
    }

    public function hasSufficientCredit(Money $amount): bool
    {
        return $this->balance >= $amount->getAmount();
    }

    public function getAvailableCredit(): Money
    {
        return new Money($this->balance, $this->currency);
    }

    public function getCreditLimit(): CreditLimit
    {
        return new CreditLimit($this->credit_limit, $this->currency);
    }
} 