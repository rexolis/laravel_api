<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_list_of_tasks(): void 
    {
        // Arrange: create 2 fake tasks
        $tasks = Task::factory()->count(2)->create();

        // Act: make a get request to the specified endpoint
        $response = $this->getJson('/api/v1/tasks');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
                ['id', 'name', 'is_completed']
            ]
        ]);
    }

    public function test_user_can_get_single_task(): void 
    {
        // Arrange: create 1 fake task
        $task = Task::factory()->create();

        // Act: make a get request to the specified endpoint with ID
        $response = $this->getJson('/api/v1/tasks/'. $task->id);

        // Assert
        $response->assertOk();
        $response->assertJson([
            'data' => [
                'id'            => $task->id,
                'name'          => $task->name,
                'is_completed'  => $task->is_completed,
            ]
        ]);
    }
}
