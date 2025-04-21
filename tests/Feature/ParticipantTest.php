<?php

namespace Tests\Feature;

use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParticipantTest extends TestCase
{
    // Test: create participant successful
    public function test_create_participant_successfully(): void
    {

        // Prepare the data for creating the participant
        $data = [
            'name' => 'Participant A',
            'email' => 'YHt3j@example.com',
            'organization' => 'Organization A',
            'phone' => '0234567890',
            'title' => 'Title A',
        ];

        // Send a POST request to create the participant
        $response = $this->postJson('/api/v1/participants', $data);

        // Assert that the response status is 201 Created
        $response->assertStatus(201);

        // Assert that the response contains the success message
        $response->assertJson([
            "message" => "អ្នកចូលរួមត្រូវបានបង្កើតដោយជោគជ័យ"
        ]);

        // Optionally, assert that the participant was stored in the database
        $this->assertDatabaseHas('participants', [
            'name' => 'Participant A',
            'email' => 'YHt3j@example.com',
        ]);
    }

    // Test: view participant with null user
    public function test_view_participant_with_null_user()
    {

        $participant = Participant::orderBy('created_at', 'desc')->first();

        $response = $this->get('/api/v1/participants/' . $participant->id);
        $response->assertStatus(200);

        $response->assertJson([
            'id' => $participant->id,
            'user' => null
        ]);
    }

    // Tests: update participant successful
    public function test_update_participant_successfully(): void
    {
        $participant = Participant::orderBy('created_at', 'desc')->first();

        $user = User::orderBy('created_at', 'desc')->first();
        // Prepare the data for updating the participant
        $data = [
            'name' => 'Participant A',
            'email' => 'YHt3j@example.com',
            'organization' => 'Organization A',
            'phone' => '0234567890',
            'title' => 'Title A',
            'user_id' => $user->id,
        ];

        // Send a PUT request to update the participant
        $response = $this->patchJson('/api/v1/participants/' . $participant->id, $data);

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the response contains the success message
        $response->assertJson([
            "message" => "អ្នកចូលរួមត្រូវបានកែប្រែដោយជោគជ័យ"
        ]);
    }

    // Test: view participant with user => because update participant has user
    public function test_view_participant_with_user()
    {

        $participant = Participant::orderBy('created_at', 'desc')->first();

        $response = $this->get('/api/v1/participants/' . $participant->id);
        $response->assertStatus(200);

        $response->assertJson([
            'id' => $participant->id,
            'name' => $participant->name,
            'title' => $participant->title,
            'email' => $participant->email,
            'phone' => $participant->phone,
            'organization' => $participant->organization,
            'user_id' => $participant->user_id,
        ]);

        $response->assertJson([
            'user' => [
                'id' => $participant->user->id,
                'name' => $participant->user->name,
                'email' => $participant->user->email,
            ]
        ]);
    }

    // Test: delete participant successful
    public function test_delete_participant_successfully(): void
    {
        $participant = Participant::orderBy('created_at', 'desc')->first();

        // Send a DELETE request to delete the participant
        $response = $this->delete('/api/v1/participants/' . $participant->id);

        // Assert that the response status is 200 OK
        $response->assertStatus(200);

        // Assert that the response contains the success message
        $response->assertJson([
            "message" => "អ្នកចូលរួមត្រូវបានលុបចោលដោយជោគជ័យ"
        ]);

        // Assert that the participant was deleted from the database
        $this->assertDatabaseMissing('participants', [
            'id' => $participant->id,
        ]);
    }
}


