<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    // Test 1: Get all students
    public function test_can_get_all_students(): void
    {
        Student::factory()->count(3)->create();

        $response = $this->getJson('/api/students');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    // Test 2: Create a student
    public function test_can_create_student(): void
    {
        $response = $this->postJson('/api/students', [
            'name' => 'Ahmed Ali',
            'email' => 'ahmed@example.com',
            'age' => 22,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('students', [
            'name' => 'Ahmed Ali',
            'email' => 'ahmed@example.com',
            'age' => 22,
        ]);
    }

    // Test 3: Reject invalid data
    public function test_cannot_create_student_with_invalid_data(): void
    {
        $response = $this->postJson('/api/students', [
            'name' => '',
            'email' => 'invalid-email',
            'age' => 0,
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'name',
            'email',
            'age',
        ]);
    }

    // Test 4: Get one student
    public function test_can_get_single_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->getJson(
            '/api/students/' . $student->id
        );

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $student->id,
        ]);
    }

    // Test 5: Update a student
    public function test_can_update_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->putJson(
            '/api/students/' . $student->id,
            [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
                'age' => 25,
            ]
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'age' => 25,
        ]);
    }

    // Test 6: Delete a student
    public function test_can_delete_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->deleteJson(
            '/api/students/' . $student->id
        );

        $response->assertStatus(200);

        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);
    }

    // Test 7: Reject duplicate email
    public function test_cannot_create_student_with_duplicate_email(): void
    {
        Student::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson('/api/students', [
            'name' => 'Another Student',
            'email' => 'test@example.com',
            'age' => 22,
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'email',
        ]);
    }
    public function test_returns_404_when_student_not_found(): void
{
    $response = $this->getJson('/api/students/999999');

    $response->assertStatus(404);

    $response->assertJson([
        'message' => 'Student not found',
    ]);
}
}

