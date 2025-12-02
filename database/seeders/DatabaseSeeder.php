<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@support.com',
            'password' => Hash::make('Admin123!'),
            'user_type' => 'admin'
        ]);

        // Create support agents
        $agents = [
            ['name' => 'John Walker', 'email' => 'john@support.com', 'password' => 'Support123!'],
            ['name' => 'Sarah Naidoo', 'email' => 'sarah@support.com', 'password' => 'Support123!'],
            ['name' => 'Mike Hadebe', 'email' => 'mike@support.com', 'password' => 'Support123!'],
        ];

        foreach ($agents as $agent) {
            User::create([
                'name' => $agent['name'],
                'email' => $agent['email'],
                'password' => Hash::make($agent['password']),
                'user_type' => 'support_agent'
            ]);
        }

        //$this->call(InterestsTableSeeder::class);
    }
}