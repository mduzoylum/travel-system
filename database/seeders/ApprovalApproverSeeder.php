<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Approval\Models\ApprovalApprover;
use App\DDD\Modules\Approval\Models\ApprovalScenario;
use App\DDD\Modules\Firm\Models\FirmUser;
use App\Models\User;

class ApprovalApproverSeeder extends Seeder
{
    public function run(): void
    {
        $scenarios = ApprovalScenario::all();

        foreach ($scenarios as $scenario) {
            $firm = $scenario->firm;
            $firmUsers = FirmUser::where('firm_id', $firm->id)->get();

            // Get CEO and Manager users for this firm
            $ceoUsers = $firmUsers->where('role', 'ceo');
            $managerUsers = $firmUsers->where('role', 'manager');

            if (str_contains($scenario->name, 'Yüksek Değerli')) {
                // High-value scenario: CEO approval required
                foreach ($ceoUsers as $ceoUser) {
                    ApprovalApprover::firstOrCreate(
                        [
                            'scenario_id' => $scenario->id,
                            'user_id' => $ceoUser->user_id,
                        ],
                        [
                            'step_order' => 1,
                            'approval_type' => 'required',
                            'can_override' => true,
                            'is_active' => true,
                        ]
                    );
                }
            } elseif (str_contains($scenario->name, 'Yurt Dışı')) {
                // International scenario: Manager approval required
                foreach ($managerUsers as $managerUser) {
                    ApprovalApprover::firstOrCreate(
                        [
                            'scenario_id' => $scenario->id,
                            'user_id' => $managerUser->user_id,
                        ],
                        [
                            'step_order' => 1,
                            'approval_type' => 'required',
                            'can_override' => false,
                            'is_active' => true,
                        ]
                    );
                }
            } elseif (str_contains($scenario->name, 'Lüks Otel')) {
                // Luxury hotel scenario: Parallel approval from CEO and Manager
                $stepOrder = 1;
                foreach ($ceoUsers as $ceoUser) {
                    ApprovalApprover::firstOrCreate(
                        [
                            'scenario_id' => $scenario->id,
                            'user_id' => $ceoUser->user_id,
                        ],
                        [
                            'step_order' => $stepOrder,
                            'approval_type' => 'required',
                            'can_override' => true,
                            'is_active' => true,
                        ]
                    );
                }
                
                foreach ($managerUsers as $managerUser) {
                    ApprovalApprover::firstOrCreate(
                        [
                            'scenario_id' => $scenario->id,
                            'user_id' => $managerUser->user_id,
                        ],
                        [
                            'step_order' => $stepOrder,
                            'approval_type' => 'required',
                            'can_override' => false,
                            'is_active' => true,
                        ]
                    );
                }
            } else {
                // Standard scenario: Manager approval required
                foreach ($managerUsers as $managerUser) {
                    ApprovalApprover::firstOrCreate(
                        [
                            'scenario_id' => $scenario->id,
                            'user_id' => $managerUser->user_id,
                        ],
                        [
                            'step_order' => 1,
                            'approval_type' => 'required',
                            'can_override' => false,
                            'is_active' => true,
                        ]
                    );
                }
            }

            // Add backup approvers (CEO as backup for all scenarios, but only if not already an approver)
            foreach ($ceoUsers as $ceoUser) {
                // Check if this user is already an approver for this scenario
                $existingApprover = ApprovalApprover::where('scenario_id', $scenario->id)
                    ->where('user_id', $ceoUser->user_id)
                    ->first();
                
                if (!$existingApprover) {
                    ApprovalApprover::firstOrCreate(
                        [
                            'scenario_id' => $scenario->id,
                            'user_id' => $ceoUser->user_id,
                            'step_order' => 2,
                        ],
                        [
                            'approval_type' => 'backup',
                            'can_override' => true,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
} 