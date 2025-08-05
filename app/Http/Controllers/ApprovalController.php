<?php

namespace App\Http\Controllers;

use App\DDD\Modules\Approval\Models\ApprovalRequest;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function accept($token)
    {
        $approval = ApprovalRequest::where('token', $token)->firstOrFail();

        $approval->update([
            'status' => 'approved',
            'responded_at' => now()
        ]);

        $approval->reservation->update([
            'status' => 'approved'
        ]);

        return "✅ Rezervasyon onaylandı.";
    }

    public function reject($token)
    {
        $approval = ApprovalRequest::where('token', $token)->firstOrFail();

        $approval->update([
            'status' => 'rejected',
            'responded_at' => now()
        ]);

        $approval->reservation->update([
            'status' => 'rejected'
        ]);

        return "❌ Rezervasyon reddedildi.";
    }
}
