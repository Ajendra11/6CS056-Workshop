<?php

namespace Tests\Feature\Api;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Workshop3ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_crud_and_validation(): void
    {
        // Index (empty)
        $this->getJson('/api/students')
            ->assertOk()
            ->assertExactJson(['data' => []]);

        // Store validation failure
        $this->postJson('/api/students', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone']);

        // Store success (201)
        $storeResponse = $this->postJson('/api/students', [
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'phone' => '9800000001',
            'address' => 'Kathmandu',
            'date_of_birth' => '2000-05-15',
        ]);

        $storeResponse->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'phone', 'address', 'date_of_birth', 'age'],
            ])
            ->assertJsonPath('data.name', 'Alice Smith')
            ->assertJsonPath('data.email', 'alice@example.com')
            ->assertJsonPath('data.date_of_birth', '2000-05-15');

        $studentId = $storeResponse->json('data.id');

        // Duplicate email fails validation
        $this->postJson('/api/students', [
            'name' => 'Another Alice',
            'email' => 'alice@example.com',
            'phone' => '9800000002',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Show (200)
        $this->getJson("/api/students/{$studentId}")
            ->assertOk()
            ->assertJsonPath('data.id', $studentId)
            ->assertJsonPath('data.email', 'alice@example.com');

        // Update keeping same email (200)
        $this->putJson("/api/students/{$studentId}", [
            'name' => 'Alice Updated',
            'email' => 'alice@example.com',
            'phone' => '9811111111',
            'address' => 'Lalitpur',
            'date_of_birth' => '2000-05-15',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Alice Updated')
            ->assertJsonPath('data.phone', '9811111111');

        // Destroy (204)
        $this->deleteJson("/api/students/{$studentId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('students', ['id' => $studentId]);

        // Show missing (404)
        $this->getJson("/api/students/{$studentId}")
            ->assertNotFound();
    }

    public function test_courses_crud_and_validation(): void
    {
        // Index (empty)
        $this->getJson('/api/courses')
            ->assertOk()
            ->assertExactJson(['data' => []]);

        // Store validation failure
        $this->postJson('/api/courses', [
            'difficulty' => 'Expert',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'duration', 'fee', 'difficulty']);

        // Store success (201)
        $storeResponse = $this->postJson('/api/courses', [
            'name' => 'Laravel REST APIs',
            'description' => 'Build REST APIs with Laravel',
            'duration' => 6,
            'fee' => 15000.50,
            'difficulty' => 'Medium',
        ]);

        $storeResponse->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'description', 'duration', 'fee', 'difficulty', 'is_active'],
            ])
            ->assertJsonPath('data.name', 'Laravel REST APIs')
            ->assertJsonPath('data.fee', '15000.50')
            ->assertJsonPath('data.difficulty', 'Medium')
            ->assertJsonPath('data.is_active', true);

        $courseId = $storeResponse->json('data.id');

        // Show (200)
        $this->getJson("/api/courses/{$courseId}")
            ->assertOk()
            ->assertJsonPath('data.id', $courseId)
            ->assertJsonPath('data.name', 'Laravel REST APIs');

        // Update (200)
        $this->putJson("/api/courses/{$courseId}", [
            'name' => 'Advanced Laravel REST APIs',
            'description' => 'Updated description',
            'duration' => 8,
            'fee' => 20000,
            'difficulty' => 'Hard',
            'is_active' => false,
        ])->assertOk()
            ->assertJsonPath('data.name', 'Advanced Laravel REST APIs')
            ->assertJsonPath('data.duration', 8)
            ->assertJsonPath('data.difficulty', 'Hard')
            ->assertJsonPath('data.is_active', false);

        // Destroy (204)
        $this->deleteJson("/api/courses/{$courseId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('courses', ['id' => $courseId]);

        // Show missing (404)
        $this->getJson("/api/courses/{$courseId}")
            ->assertNotFound();
    }
}
