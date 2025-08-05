<?php

namespace App\DDD\Modules\UserAccessRule\Services;

use App\Models\User;
use App\DDD\Modules\Contract\Models\Hotel;

class AccessRuleEvaluatorService
{
    public function canUserAccessHotel(User $user, Hotel $hotel): bool
    {
        $firmUser = $user->firmUser;

        if (!$firmUser) return false;

        $rule = $firmUser->firm->accessRules()->where('role', $firmUser->role)->first();

        if (!$rule) return true; // kural yoksa serbest

        $rules = $rule->rules;

        if (isset($rules['max_stars']) && $hotel->stars > $rules['max_stars']) return false;
        if (isset($rules['allowed_countries']) && !in_array($hotel->country, $rules['allowed_countries'])) return false;
        if (isset($rules['max_price']) && $hotel->min_price > $rules['max_price']) return false;

        return true;
    }
}
