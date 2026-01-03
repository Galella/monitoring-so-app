<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'view_users', 'display_name' => 'View Users', 'description' => 'Can view list of users'],
            ['name' => 'create_users', 'display_name' => 'Create Users', 'description' => 'Can create new users'],
            ['name' => 'edit_users', 'display_name' => 'Edit Users', 'description' => 'Can edit existing users'],
            ['name' => 'delete_users', 'display_name' => 'Delete Users', 'description' => 'Can delete users'],

            // Role Management
            ['name' => 'view_roles', 'display_name' => 'View Roles', 'description' => 'Can view list of roles'],
            ['name' => 'create_roles', 'display_name' => 'Create Roles', 'description' => 'Can create new roles'],
            ['name' => 'edit_roles', 'display_name' => 'Edit Roles', 'description' => 'Can edit existing roles'],
            ['name' => 'delete_roles', 'display_name' => 'Delete Roles', 'description' => 'Can delete roles'],

            // Wilayah Management
            ['name' => 'view_wilayahs', 'display_name' => 'View Wilayahs', 'description' => 'Can view list of wilayahs'],
            ['name' => 'create_wilayahs', 'display_name' => 'Create Wilayahs', 'description' => 'Can create new wilayahs'],
            ['name' => 'edit_wilayahs', 'display_name' => 'Edit Wilayahs', 'description' => 'Can edit existing wilayahs'],
            ['name' => 'delete_wilayahs', 'display_name' => 'Delete Wilayahs', 'description' => 'Can delete wilayahs'],

            // Area Management
            ['name' => 'view_areas', 'display_name' => 'View Areas', 'description' => 'Can view list of areas'],
            ['name' => 'create_areas', 'display_name' => 'Create Areas', 'description' => 'Can create new areas'],
            ['name' => 'edit_areas', 'display_name' => 'Edit Areas', 'description' => 'Can edit existing areas'],
            ['name' => 'delete_areas', 'display_name' => 'Delete Areas', 'description' => 'Can delete areas'],

            // Monitoring & Data
            ['name' => 'view_monitoring', 'display_name' => 'View Monitoring', 'description' => 'Can access monitoring dashboard'],
            ['name' => 'view_cms', 'display_name' => 'View CM Data', 'description' => 'Can view CM data'],
            ['name' => 'create_cms', 'display_name' => 'Import/Create CM Data', 'description' => 'Can import or create CM data'],
            ['name' => 'edit_cms', 'display_name' => 'Edit CM Data', 'description' => 'Can edit CM data'],
            ['name' => 'delete_cms', 'display_name' => 'Delete CM Data', 'description' => 'Can delete CM data'],
            
            ['name' => 'view_coins', 'display_name' => 'View Coin Data', 'description' => 'Can view Coin data'],
            ['name' => 'create_coins', 'display_name' => 'Import/Create Coin Data', 'description' => 'Can import or create Coin data'],
            ['name' => 'edit_coins', 'display_name' => 'Edit Coin Data', 'description' => 'Can edit Coin data'],
            ['name' => 'delete_coins', 'display_name' => 'Delete Coin Data', 'description' => 'Can delete Coin data'],
            
            // Monitoring SO
            ['name' => 'view_monitoring_so', 'display_name' => 'View Monitoring SO', 'description' => 'Can access Monitoring SO module'],
            ['name' => 'edit_monitoring_so', 'display_name' => 'Edit SO', 'description' => 'Can update SO number'],

            // Activity Logs
            ['name' => 'view_activity_logs', 'display_name' => 'View Activity Logs', 'description' => 'Can view activity logs'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
