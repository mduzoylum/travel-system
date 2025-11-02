<?php

namespace App\DDD\Modules\Credit\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use App\DDD\Modules\Credit\Domain\ValueObjects\Money;

class CreditTransaction extends Model
{
    protected $fillable = [
        'credit_account_id',
        'type', // 'credit', 'debit'
        'amount',
        'description',
        'reference_type', // 'reservation', 'manual', 'payment'
        'reference_id',
        'balance_after',
        'performed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2'
    ];

    public function creditAccount()
    {
        return $this->belongsTo(CreditAccount::class);
    }

    public function performer()
    {
        return $this->belongsTo(\App\Models\User::class, 'performed_by');
    }

    public function getAmount(): Money
    {
        return new Money($this->amount, $this->creditAccount->currency);
    }

    public function isCredit(): bool
    {
        return $this->type === 'credit';
    }

    public function isDebit(): bool
    {
        return $this->type === 'debit';
    }
} 