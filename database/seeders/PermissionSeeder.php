<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /////set 3 permission!
        Permission::create([
            'code_permission' => 1,
            'permission_name' => 'admin',
            'is_deleted' => false,
        ]);

        Permission::create([
            'code_permission' => 2,
            'permission_name' => 'user',
            'is_deleted' => false,
        ]);

        Permission::create([
            'code_permission' => 3,
            'permission_name' => 'client',
            'is_deleted' => false,
        ]);
    }
}
