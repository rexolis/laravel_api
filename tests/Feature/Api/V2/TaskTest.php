<?php

namespace Tests\Feature\Api\V2;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_list_of_tasks(): void 
    {
        // create a fake user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Arrange: create 2 fake tasks
        $tasks = Task::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        // Act: make a get request to the specified endpoint
        $response = $this->getJson('/api/v2/tasks');

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
        $user = User::factory()->create();
        $this->actingAs($user);

        // Arrange: create 1 fake task
        $task = Task::factory()->create(['user_id' => $user->id]);

        // Act: make a get request to the specified endpoint with ID
        $response = $this->getJson('/api/v2/tasks/'. $task->id);

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

    public function test_user_can_create_task(): void 
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: make a post request to the specified endpoint with data
        $response = $this->postJson('/api/v2/tasks', [
            'name' => 'Authenticated user Task',
        ]);

        // Assert
        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'is_completed']
        ]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Authenticated user Task',
        ]);
    }

    public function test_user_cannot_create_invalid_task(): void 
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: make a post request to the specified endpoint with data
        $response = $this->postJson('/api/v2/tasks', [
            'name' => '',
        ]);

        // Assert
        $response->assertStatus(422);
        $response ->assertJsonValidationErrors(['name']);
    }

    public function test_user_can_update_task(): void 
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Arrange: create a fake task data
        $taskData = Task::factory()->create();

        // Act: make a post request to the specified endpoint with data
        $response = $this->putJson('/api/v2/tasks/' . $taskData->id, [
            'name' => 'Updated Test Task',
        ]);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Updated Test Task',
        ]);
    }

    public function test_user_cannot_update_task_with_invalid_data(): void 
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Arrange: create a fake task data
        $taskData = Task::factory()->create();

        // Act: make a post request to the specified endpoint with data
        $response = $this->putJson('/api/v2/tasks/' . $taskData->id, [
            'name' => '',
        ]);

        // Assert
        $response->assertStatus(422);
        $response ->assertJsonValidationErrors(['name']);
    }
    
    public function test_user_can_toggle_task_completion(): void 
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Arrange: create a fake task data
        $taskData = Task::factory()->create([
            'is_completed' => false,
        ]);

        // Act: make a post request to the specified endpoint with data
        $response = $this->patchJson('/api/v2/tasks/' . $taskData->id . '/complete', [
            'is_completed' => true,
        ]);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'is_completed' => true,
        ]);
    }
    
    public function test_user_cannot_toggle_task_completion_with_invalid_data(): void 
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Arrange: create a fake task data
        $taskData = Task::factory()->create([
            'is_completed' => false,
        ]);

        // Act: make a post request to the specified endpoint with data
        $response = $this->patchJson('/api/v2/tasks/' . $taskData->id . '/complete', [
            'is_completed' => 'yes',
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['is_completed']);
    }
    
    public function test_user_can_delete_task(): void 
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        // Arrange: create a fake task data
        $taskData = Task::factory()->create();

        // Act: make a post request to the specified endpoint with data
        $response = $this->deleteJson('/api/v2/tasks/' . $taskData->id);

        // Assert
        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', [
            'id' => $taskData->id,
        ]);
    }
}
