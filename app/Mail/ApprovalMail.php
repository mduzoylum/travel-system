<?php

use Illuminate\Mail\Mailable;
use App\DDD\Modules\Approval\Models\ApprovalRequest;

class ApprovalMail extends Mailable
{
    public function __construct(public ApprovalRequest $approvalRequest) {}

    public function build()
    {
        return $this->subject('Yeni Rezervasyon Talebi')
            ->view('emails.approval')
            ->with([
                'token' => $this->approvalRequest->token,
                'reservation' => $this->approvalRequest->reservation,
            ]);
    }
}
