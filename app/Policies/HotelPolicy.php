<?php

namespace App\Policies;

use App\Models\User;
use App\DDD\Modules\Contract\Models\Hotel;
use App\DDD\Modules\UserAccessRule\Services\AccessRuleEvaluatorService;

class HotelPolicy
{
    public function view(User $user, Hotel $hotel): bool
    {
        return app(AccessRuleEvaluatorService::class)->canUserAccessHotel($user, $hotel);
    }
}
