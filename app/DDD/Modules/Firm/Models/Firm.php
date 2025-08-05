<?php

namespace App\DDD\Modules\Firm\Models;

use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_number',
        'email_domain',
        'is_active',
    ];

    public function firmUsers()
    {
        return $this->hasMany(FirmUser::class);
    }

    public function creditAccounts()
    {
        return $this->hasMany(\App\DDD\Modules\Credit\Domain\Entities\CreditAccount::class);
    }

    public function contracts()
    {
        return $this->hasMany(\App\DDD\Modules\Contract\Models\Contract::class);
    }

}
