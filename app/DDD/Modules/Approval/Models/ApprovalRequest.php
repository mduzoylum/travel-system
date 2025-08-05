<?php

namespace App\DDD\Modules\Approval\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ApprovalRequest extends Model
{
    protected $fillable = [
        'scenario_id', 'requested_by', 'request_type', 'request_data', 'status',
        'expires_at', 'approved_at', 'rejected_at', 'approved_by', 'approval_notes', 'rejection_reason'
    ];

    protected $casts = [
        'request_data' => 'array',
        'expires_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    public function scenario()
    {
        return $this->belongsTo(ApprovalScenario::class, 'scenario_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Onay isteğinin süresi dolmuş mu?
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Onay isteğinin durumunu güncelle
     */
    public function updateStatus(string $status, ?User $approvedBy = null, ?string $notes = null): void
    {
        $this->status = $status;
        
        if ($status === 'approved') {
            $this->approved_at = now();
            $this->approved_by = $approvedBy?->id;
            $this->approval_notes = $notes;
        } elseif ($status === 'rejected') {
            $this->rejected_at = now();
            $this->approved_by = $approvedBy?->id;
            $this->rejection_reason = $notes;
        }
        
        $this->save();
    }

    /**
     * Onay isteğinin kalan süresini getir
     */
    public function getRemainingTime(): ?string
    {
        if (!$this->expires_at) {
            return null;
        }

        if ($this->isExpired()) {
            return 'Süresi dolmuş';
        }

        return $this->expires_at->diffForHumans();
    }

    /**
     * Durum badge rengini getir
     */
    public function getStatusBadge(): string
    {
        switch ($this->status) {
            case 'pending':
                return 'bg-warning';
            case 'approved':
                return 'bg-success';
            case 'rejected':
                return 'bg-danger';
            case 'expired':
                return 'bg-secondary';
            default:
                return 'bg-secondary';
        }
    }
}
