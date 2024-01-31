<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\RoleHierarchy;

class UsersAndNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfUsers = 100;
        $faker = Faker::create();
        /* Create roles */
        $adminRole = Role::create(['name' => 'admin']);
        RoleHierarchy::create([
            'role_id' => $adminRole->id,
            'hierarchy' => 1,
        ]);
        $userRole = Role::create(['name' => 'user']);
        RoleHierarchy::create([
            'role_id' => $userRole->id,
            'hierarchy' => 2,
        ]);
        $guestRole = Role::create(['name' => 'guest']);
        RoleHierarchy::create([
            'role_id' => $guestRole->id,
            'hierarchy' => 3,
        ]);
        /*  insert users   */
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'menuroles' => 'user,admin',
            'post_back_amount' => config('dnm.defaultFormSettings.postBackAmount'),
            'personal_min_req' => config('dnm.defaultFormSettings.personalMinReq'),
            'personal_channel_id' => config('dnm.credentials.personalChannelId'),
            'personal_password' => config('dnm.credentials.personalPassword'),
            'lead_channel_id' => config('dnm.credentials.leadChannelId'),
            'lead_password' => config('dnm.credentials.leadPassword'),
        ]);
        $user->assignRole('admin');
        $user->assignRole('user');
        for($i = 0; $i<$numberOfUsers; $i++){
            $user = User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'post_back_amount' => random_int(7,12)*100,
                'personal_min_req' => random_int(12,20)*100,
                'personal_channel_id' => Str::random(5),
                'personal_password' => Str::random(32),
                'lead_channel_id' => Str::random(5),
                'lead_password' => Str::random(32),
                'menuroles' => 'user'
            ]);
            $user->assignRole('user');
        }
    }
}
