<?php

namespace App\DDD\Modules\Firm\Models;

use Illuminate\Database\Eloquent\Model;

class FirmUser extends Model
{
    protected $fillable = ['firm_id', 'user_id', 'role', 'department'];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
