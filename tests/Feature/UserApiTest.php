<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Faker\Factory as Faker;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Enable session middleware
        $this->app->make('Illuminate\Contracts\Http\Kernel')->pushMiddleware('Illuminate\Session\Middleware\StartSession');
    }

    /** @test */
    public function admin_can_create_user()
    {
        $faker = Faker::create();

        // Create an admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' =>  $faker->unique()->safeEmail,
            'password' => bcrypt('password123'),
            'is_admin' => true,
        ]);

        // Authenticate as admin to get the token
        $response = $this->postJson('/api/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $token = $response['user']['api_token'];

        // Create a new user using the obtained token
        $userData = [
            'name' => 'John Doe',
            'email' => $faker->unique()->safeEmail,
            'password' => 'password456',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/admin/users', $userData);
        $response->assertStatus(201);

        // Assert the user was created in the database
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }

    /** @test */
    public function user_can_create_post()
    {
        $faker = Faker::create();

        // Create a regular user
        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' =>  $faker->unique()->safeEmail,
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);

        // Authenticate as regular user to get the token
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $token = $response['user']['api_token'];

        // Create a new post using the obtained token
        $postData = [
            'title' => 'Sample Post',
            'content' => 'This is a sample post content.',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson('/api/posts', $postData);
        $response->assertStatus(201);

        // Assert the post was created in the database
        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
        ]);
    }

    /** @test */
    public function user_can_report_post()
    {
        $faker = Faker::create();

        // Create a regular user
        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' =>  $faker->unique()->safeEmail,
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);

        // Create another user who will own the post
        $postOwner = User::factory()->create([
            'name' => 'Post Owner',
            'email' =>  $faker->unique()->safeEmail,
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);

        // Create a post
        $post = Post::create([
            'user_id' => $postOwner->id,
            'title' => 'Sample Post',
            'content' => 'This is a sample post content.',
        ]);

        // Authenticate as regular user to get the token
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $token = $response['user']['api_token'];

        // Report the post using the obtained token
        $reportData = [
            'post_id' => $post->id,
            'reason' => 'Inappropriate content',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson("/api/report", $reportData);
        $response->assertStatus(200);


        // Assert the report was created in the database
        $this->assertDatabaseHas('reports', [
            'post_id' => $post->id,
            'reason' => $reportData['reason'],
        ]);
    }


    /** @test */
    public function user_cannot_report_own_post()
    {
        $faker = Faker::create();

        // Create a regular user
        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' =>  $faker->unique()->safeEmail,
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);

        // Create a post owned by the user
        $post = Post::create([
            'user_id' => $user->id,
            'title' => 'Sample Post',
            'content' => 'This is a sample post content.',
        ]);

        // Authenticate as regular user to get the token
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $token = $response['user']['api_token'];

        // Try to report the own post using the obtained token
        $reportData = [
            'post_id' => $post->id,
            'reason' => 'Inappropriate content',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")->postJson("/api/report", $reportData);
        $response->assertStatus(403); // Expecting Forbidden status

        // Assert the report was not created in the database
        $this->assertDatabaseMissing('reports', [
            'post_id' => $post->id,
            'reason' => $reportData['reason'],
        ]);
    }
}
