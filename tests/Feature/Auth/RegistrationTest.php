<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'father_name' => 'Father Name',
            'mother_name' => 'Mother Name',
            'dob' => '1998-01-01',
            'gender' => 'male',
            'category' => 'GEN',
            'marital_status' => 'single',
            'nationality' => 'Indian',
            'mobile' => '9876543210',
            'email' => 'test@example.com',
            'correspondence_address' => 'Test correspondence address',
            'permanent_address' => 'Test permanent address',
            'aadhaar_number' => '123456789012',
            'id_proof_upload' => UploadedFile::fake()->create('id-proof.pdf', 200, 'application/pdf'),
            'declaration' => '1',
            'password' => 'StrongPass#123',
            'password_confirmation' => 'StrongPass#123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
