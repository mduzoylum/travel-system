<?php

namespace App\DDD\Modules\UserAccessRule\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccessRule extends Model
{
    protected $fillable = ['firm_id', 'role', 'rules'];

    protected $casts = [
        'rules' => 'array',
    ];

    public function firm()
    {
        return $this->belongsTo(\App\DDD\Modules\Firm\Models\Firm::class);
    }
}
