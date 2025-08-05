<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Approval\Models\ApprovalRequest;
use App\DDD\Modules\Approval\Models\ApprovalScenario;
use App\DDD\Modules\Reservation\Models\Reservation;
use App\DDD\Modules\Firm\Models\FirmUser;
use Carbon\Carbon;

class ApprovalRequestSeeder extends Seeder
{
    public function run(): void
    {
        $reservations = Reservation::where('status', 'awaiting_approval')->get();
        $scenarios = ApprovalScenario::all();

        foreach ($reservations as $reservation) {
            // Check if contractRoom exists
            if (!$reservation->contractRoom) {
                continue;
            }

            // Find the appropriate scenario based on reservation details
            $scenario = $this->findMatchingScenario($reservation, $scenarios);
            
            if (!$scenario) {
                continue;
            }

            // Get the user who made the reservation
            $requestedBy = $reservation->user_id;
            
            // Get approvers for this scenario
            $approvers = $scenario->approvers()->where('step_order', 1)->get();
            
            foreach ($approvers as $approver) {
                $expiresAt = Carbon::now()->addDays($scenario->max_approval_days);
                
                // Randomly set some requests as expired
                if (rand(1, 10) <= 2) {
                    $expiresAt = Carbon::now()->subDays(rand(1, 5));
                }

                $status = 'pending';
                $approvedAt = null;
                $rejectedAt = null;
                $approvedBy = null;
                $approvalNotes = null;
                $rejectionReason = null;

                // Randomly approve/reject some requests
                if (rand(1, 10) <= 6) {
                    $status = 'approved';
                    $approvedAt = Carbon::now()->subDays(rand(1, 3));
                    $approvedBy = $approver->user_id;
                    $approvalNotes = 'Onaylandı';
                } elseif (rand(1, 10) <= 2) {
                    $status = 'rejected';
                    $rejectedAt = Carbon::now()->subDays(rand(1, 2));
                    $approvedBy = $approver->user_id;
                    $rejectionReason = 'Bütçe aşımı';
                }

                ApprovalRequest::firstOrCreate(
                    [
                        'scenario_id' => $scenario->id,
                        'requested_by' => $requestedBy,
                        'request_type' => 'reservation',
                    ],
                    [
                        'request_data' => json_encode([
                            'reservation_id' => $reservation->id,
                            'hotel_name' => $reservation->contractRoom->contract->hotel->name ?? 'Unknown Hotel',
                            'total_price' => $reservation->total_price,
                            'checkin_date' => $reservation->checkin_date,
                            'checkout_date' => $reservation->checkout_date,
                            'guest_count' => $reservation->guest_count,
                        ]),
                        'status' => $status,
                        'expires_at' => $expiresAt,
                        'approved_at' => $approvedAt,
                        'rejected_at' => $rejectedAt,
                        'approved_by' => $approvedBy,
                        'approval_notes' => $approvalNotes,
                        'rejection_reason' => $rejectionReason,
                    ]
                );
            }
        }
    }

    private function findMatchingScenario($reservation, $scenarios)
    {
        // Check if contractRoom and related data exists
        if (!$reservation->contractRoom || !$reservation->contractRoom->contract || !$reservation->contractRoom->contract->hotel) {
            return $scenarios->first(); // Default to first scenario
        }

        $hotel = $reservation->contractRoom->contract->hotel;
        $totalPrice = $reservation->total_price;

        foreach ($scenarios as $scenario) {
            // Check if scenario matches based on rules
            if ($totalPrice > 5000 && str_contains($scenario->name, 'Yüksek Değerli')) {
                return $scenario;
            } elseif ($hotel->country !== 'Türkiye' && str_contains($scenario->name, 'Yurt Dışı')) {
                return $scenario;
            } elseif ($hotel->stars == 5 && str_contains($scenario->name, 'Lüks Otel')) {
                return $scenario;
            } elseif (str_contains($scenario->name, 'Standart')) {
                return $scenario;
            }
        }

        return $scenarios->first(); // Default to first scenario
    }
} 