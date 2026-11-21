<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrCreate(
            ['email' => 'osamaday27@gmail.com'],
            [
                'name' => 'ahmed osama',
                'password' => bcrypt('123456'),
                'roles_name' => ['owner'],
                'Status' => 'Enabled',
            ]
        );

        $role = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
