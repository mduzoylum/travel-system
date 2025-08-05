<?php

namespace App\DDD\Modules\Approval\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ApprovalApprover extends Model
{
    protected $fillable = [
        'scenario_id', 'user_id', 'step_order', 'approval_type', 'can_override', 'is_active'
    ];

    protected $casts = [
        'can_override' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function scenario()
    {
        return $this->belongsTo(ApprovalScenario::class, 'scenario_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Onaylayıcı tipinin açıklamasını getir
     */
    public function getTypeDescription(): string
    {
        $types = [
            'required' => 'Zorunlu',
            'optional' => 'İsteğe Bağlı',
            'backup' => 'Yedek'
        ];

        return $types[$this->approval_type] ?? $this->approval_type;
    }

    /**
     * Onaylayıcının yetkilerini kontrol et
     */
    public function canApprove(): bool
    {
        return $this->is_active && $this->user && $this->user->is_active;
    }
} 