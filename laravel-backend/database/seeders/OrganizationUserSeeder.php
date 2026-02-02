<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add existing organization owners to organization_user table
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            OrganizationUser::firstOrCreate([
                'organization_id' => $organization->id,
                'user_id' => $organization->user_id,
            ], [
                'role' => 'owner',
                'joined_at' => $organization->created_at,
            ]);
        }
    }
}
