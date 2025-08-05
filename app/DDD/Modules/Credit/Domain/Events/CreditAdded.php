<?php

namespace App\DDD\Modules\Credit\Domain\Events;

use App\DDD\Modules\Credit\Domain\Entities\CreditAccount;
use App\DDD\Modules\Credit\Domain\ValueObjects\Money;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreditAdded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public CreditAccount $creditAccount,
        public Money $amount,
        public ?string $description = null
    ) {}
} 