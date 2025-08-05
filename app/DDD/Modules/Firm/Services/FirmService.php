<?php

namespace App\DDD\Modules\Firm\Services;

use App\DDD\Modules\Firm\Models\Firm;

class FirmService
{
    public function create(array $data): Firm
    {
        return Firm::create([
            'name' => $data['name'],
            'email_domain' => $data['email_domain'],
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function findByDomain(string $domain): ?Firm
    {
        return Firm::where('email_domain', $domain)->first();
    }
}
