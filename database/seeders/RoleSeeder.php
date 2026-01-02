<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'admin',
        ], [
            'description' => 'Administrator role with full access'
        ]);

        Role::firstOrCreate([
            'name' => 'user',
        ], [
            'description' => 'Regular user role'
        ]);

        Role::firstOrCreate([
            'name' => 'moderator',
        ], [
            'description' => 'Moderator role with limited admin access'
        ]);
    }
}
