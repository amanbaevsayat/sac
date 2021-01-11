<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->roles() as $role) {
            Role::create($role);
        }
    }

    private function roles()
    {
        return [
            ['code' => 'host', 'title' => 'Админ'],
            ['code' => 'head', 'title' => 'Head'],
            ['code' => 'operator', 'title' => 'Оператор'],
        ];
    }
}
