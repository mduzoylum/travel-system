<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\DDD\Modules\Approval\Models\ApprovalScenario;
use App\DDD\Modules\Firm\Models\Firm;

class ApprovalScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $firms = Firm::all();

        foreach ($firms as $firm) {
            // High-value reservation approval scenario
            ApprovalScenario::firstOrCreate(
                [
                    'name' => 'Yüksek Değerli Rezervasyon Onayı',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => '5000 TL üzeri rezervasyonlar için onay süreci',
                    'is_active' => true,
                    'approval_type' => 'multi_step',
                    'max_approval_days' => 3,
                    'require_all_approvers' => false,
                    'notification_settings' => json_encode([
                        'email_notifications' => true,
                        'sms_notifications' => false,
                        'reminder_interval_hours' => 24,
                    ]),
                ]
            );

            // International travel approval scenario
            ApprovalScenario::firstOrCreate(
                [
                    'name' => 'Yurt Dışı Seyahat Onayı',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => 'Yurt dışı rezervasyonlar için onay süreci',
                    'is_active' => true,
                    'approval_type' => 'single',
                    'max_approval_days' => 5,
                    'require_all_approvers' => false,
                    'notification_settings' => json_encode([
                        'email_notifications' => true,
                        'sms_notifications' => true,
                        'reminder_interval_hours' => 12,
                    ]),
                ]
            );

            // Luxury hotel approval scenario
            ApprovalScenario::firstOrCreate(
                [
                    'name' => 'Lüks Otel Onayı',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => '5 yıldızlı otel rezervasyonları için onay süreci',
                    'is_active' => true,
                    'approval_type' => 'parallel',
                    'max_approval_days' => 2,
                    'require_all_approvers' => true,
                    'notification_settings' => json_encode([
                        'email_notifications' => true,
                        'sms_notifications' => true,
                        'reminder_interval_hours' => 6,
                    ]),
                ]
            );

            // Standard approval scenario
            ApprovalScenario::firstOrCreate(
                [
                    'name' => 'Standart Rezervasyon Onayı',
                    'firm_id' => $firm->id,
                ],
                [
                    'description' => 'Genel rezervasyonlar için onay süreci',
                    'is_active' => true,
                    'approval_type' => 'single',
                    'max_approval_days' => 1,
                    'require_all_approvers' => false,
                    'notification_settings' => json_encode([
                        'email_notifications' => true,
                        'sms_notifications' => false,
                        'reminder_interval_hours' => 48,
                    ]),
                ]
            );
        }
    }
} 