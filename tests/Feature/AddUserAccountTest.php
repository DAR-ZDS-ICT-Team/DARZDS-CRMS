<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AddUserAccountTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // use RefreshDatabase; // Reset the database after each test

    /**
     * Test creating a user account.
     *
     * @return void
     */
    // public function test_user_can_create_account()
    // {
    //     // Create an admin user
    //     $user = User::factory()->create([
    //         'account_type' => 'user', // Assuming 'admin' account type
    //     ]);

    //     // Act: Perform the request to create a user account
    //     $response = $this->actingAs($user)->post('/accounts/add');

    //     // Assert: Verify that regular user cannot create account
    //     $response->assertStatus(403); // Assuming forbidden response for regular user

    // }

    public function test_admin_can_create_account()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'account_type' => 'admin', // Assuming 'admin' account type
        ]);

        // Act: Perform the request to create a user account
        $response = $this->actingAs($admin)->post('/accounts/add');

        // Assert: Verify the user was successfully created
        $response->assertStatus(302); // Assuming successful redirect after registration
    }
}
