<?php

// Test da proverim da li je sve OK sa feature
use Illuminate\Foundation\Testing\TestCase;
use App\Models\Competition;
use App\Models\Organization;
use App\Models\User;

class UpdateKnockoutCountTest extends TestCase
{
    public function test_update_knockout_count()
    {
        // Create user and organization
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['user_id' => $user->id]);
        
        // Create tournament competition
        $competition = Competition::factory()->create([
            'organization_id' => $organization->id,
            'type' => 'tournament',
            'knockout_matches_count' => 7,
            'current_phase' => 'knockout'
        ]);
        
        // Act: Send POST request to update knockout count
        $response = $this->actingAs($user)->post(
            route('organizations.competitions.update-knockout-count', [$organization, $competition]),
            ['knockout_matches_count' => 15]
        );
        
        // Assert: Check redirect and database update
        $response->assertRedirect();
        $this->assertEquals(15, $competition->fresh()->knockout_matches_count);
    }
}
