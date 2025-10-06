<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $freePlan = Plan::where('name', 'Free')->first();

        if (!$freePlan) {
            $this->command->error('Free plan not found. Please run PlanSeeder first.');
            return;
        }

        $users = User::all();

        foreach ($users as $user) {
            // Check if user already has an active plan
            $existingPlan = UserPlan::where('user_id', $user->id)->active()->first();

            if (!$existingPlan) {
                UserPlan::create([
                    'user_id' => $user->id,
                    'plan_id' => $freePlan->id,
                    'started_at' => now(),
                    'is_active' => true,
                ]);

                $this->command->info("Assigned Free plan to user: {$user->email}");
            }
        }
    }
}
