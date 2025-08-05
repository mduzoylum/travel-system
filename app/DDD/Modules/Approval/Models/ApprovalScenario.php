<?php

namespace App\DDD\Modules\Approval\Models;

use Illuminate\Database\Eloquent\Model;
use App\DDD\Modules\Firm\Models\Firm;
use App\Models\User;

class ApprovalScenario extends Model
{
    protected $fillable = [
        'name', 'description', 'firm_id', 'is_active', 'approval_type',
        'max_approval_days', 'require_all_approvers', 'notification_settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'require_all_approvers' => 'boolean',
        'notification_settings' => 'array'
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    public function rules()
    {
        return $this->hasMany(ApprovalRule::class, 'scenario_id');
    }

    public function approvers()
    {
        return $this->hasMany(ApprovalApprover::class, 'scenario_id');
    }

    public function requests()
    {
        return $this->hasMany(ApprovalRequest::class, 'scenario_id');
    }

    /**
     * Aktif kuralları getir
     */
    public function activeRules()
    {
        return $this->rules()->where('is_active', true)->orderBy('priority');
    }

    /**
     * Aktif onaylayıcıları getir
     */
    public function activeApprovers()
    {
        return $this->approvers()->where('is_active', true)->orderBy('step_order');
    }

    /**
     * Bekleyen onay isteklerini getir
     */
    public function pendingRequests()
    {
        return $this->requests()->where('status', 'pending');
    }

    /**
     * Senaryonun geçerli olup olmadığını kontrol et
     */
    public function isValid(): bool
    {
        return $this->is_active && $this->activeApprovers()->count() > 0;
    }

    /**
     * Bildirim ayarlarını getir
     */
    public function getNotificationSettings(): array
    {
        return $this->notification_settings ?? [
            'email' => true,
            'sms' => false,
            'push' => false,
            'reminder_hours' => 24
        ];
    }
} 