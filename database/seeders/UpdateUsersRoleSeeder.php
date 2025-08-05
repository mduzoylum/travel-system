<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUsersRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mevcut tüm kullanıcıları admin yap
        User::query()->update(['role' => 'admin']);
        
        $this->command->info('Tüm kullanıcılar admin yapıldı.');
    }
}
