<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->users() as $user) {
            $user['email_verified_at'] = now();
            // $user['password'] = bcrypt('1234qwer');
            $user['remember_token'] = Str::random(10);

            User::create($user);
        }
    }

    private function users()
    {
        return [
            [
                'account' => "sayat.a",
                'role_id' => Role::findByCode('host')->id,
                'email' => "amanbaev.sayat@gmail.com",
                'phone' => "+77763442424",
                'password' => bcrypt('1234qwer'),
            ],
            [
                'account' => "temirlan.b",
                'role_id' => Role::findByCode('host')->id,
                'email' => "balymbetov.temirlan@gmail.com",
                'phone' => "+77073207636",
                'password' => bcrypt('1234qwer'),
            ],
            [
                'account' => "account.1",
                'role_id' => Role::findByCode('operator')->id,
                'email' => "account.1@example.com",
                'phone' => "+77770000001",
                'password' => bcrypt('Xt2JuNhJdURjqK'),
            ],
            [
                'account' => "account.2",
                'role_id' => Role::findByCode('operator')->id,
                'email' => "account.2@example.com",
                'phone' => "+77770000002",
                'password' => bcrypt('4xCPZ37hjuzfkM'),
            ],
            [
                'account' => "account.3",
                'role_id' => Role::findByCode('operator')->id,
                'email' => "account.3@example.com",
                'phone' => "+77770000003",
                'password' => bcrypt('QNMsdGFrhEP4aG'),
            ],
        ];
    }
}
