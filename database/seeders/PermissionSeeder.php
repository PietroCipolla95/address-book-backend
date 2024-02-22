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
        $permissions =  [
            'user_write',
            'user_read',
            'anagraphics_write',
            'anagraphics_read',
        ];

        foreach ($permissions as $permission) {
            $new_permission = new Permission();
            $new_permission->name = $permission;
            $new_permission->save();
        }
    }
}
