<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PostFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_only_published_posts()
    {
        $user = User::factory()->create();

        Post::factory()->create([
            'user_id' => $user->id,
            'is_draft' => false,
            'published_at' => now()->subMinute(),
        ]);

        Post::factory()->create([
            'user_id' => $user->id,
            'is_draft' => true,
            'published_at' => now()->addMinutes(5),
        ]);

        $response = $this->actingAs($user)->getJson('/posts');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_authenticated_user_can_create_post()
    {
        $user = User::factory()->create();

        $postData = [
            'title' => 'Judul Tes',
            'content' => 'Isi Tes',
            'is_draft' => false,
            'published_at' => Carbon::now()->addMinutes(5)->toDateTimeString(),
        ];

        $response = $this->actingAs($user)->postJson('/posts', $postData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', [
            'title' => 'Judul Tes',
            'user_id' => $user->id,
        ]);
    }

    public function test_guest_cannot_create_post()
    {
        $postData = [
            'title' => 'Unauthorized Post',
            'content' => 'Not allowed',
            'is_draft' => false,
            'published_at' => now()->toDateTimeString(),
        ];

        $response = $this->postJson('/posts', $postData);

        $response->assertUnauthorized();
    }

    public function test_show_returns_post_if_published()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
            'is_draft' => false,
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($user)->getJson("/posts/{$post->id}");

        $response->assertOk()
            ->assertJsonFragment(['title' => $post->title]);
    }

    public function test_show_returns_404_if_draft_or_scheduled()
    {
        $user = User::factory()->create();

        $draft = Post::factory()->create([
            'user_id' => $user->id,
            'is_draft' => true,
            'published_at' => now(),
        ]);

        $scheduled = Post::factory()->create([
            'user_id' => $user->id,
            'is_draft' => false,
            'published_at' => now()->addMinutes(10),
        ]);

        $this->actingAs($user)->getJson("/posts/{$draft->id}")->assertNotFound();
        $this->actingAs($user)->getJson("/posts/{$scheduled->id}")->assertNotFound();
    }

    public function test_only_author_can_update_post()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $post = Post::factory()->create(['user_id' => $user->id]);

        $update = ['title' => 'Updated', 'content' => 'Updated content'];

        $this->actingAs($user)
            ->putJson("/posts/{$post->id}", $update)
            ->assertOk();

        $this->actingAs($otherUser)
            ->putJson("/posts/{$post->id}", $update)
            ->assertForbidden();
    }

    public function test_only_author_can_delete_post()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $post = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->deleteJson("/posts/{$post->id}")
            ->assertStatus(200);

        $newPost = Post::factory()->create(['user_id' => $user->id]);

        $this->actingAs($otherUser)
            ->deleteJson("/posts/{$newPost->id}")
            ->assertForbidden();
    }
}
