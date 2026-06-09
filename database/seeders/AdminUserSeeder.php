<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@coptan.cm'],
            [
                'name'      => 'Super Administrateur',
                'password'  => Hash::make('mamounette3.0'),
                'phone'     => null,
                'is_active' => true,
            ]
        );

        $admin->assignRole('super-admin');

        $this->command->info('✅ Compte Super Admin créé.');
        $this->command->info('   Email    : admin@coptan.cm');
        $this->command->info('   Password : mamounette3.0');
        $this->command->warn('   ⚠️  Changez ce mot de passe après la première connexion !');
    
    }
}
