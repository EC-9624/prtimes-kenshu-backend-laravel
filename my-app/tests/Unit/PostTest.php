<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_belongs_to_user()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $this->assertInstanceOf(User::class, $post->user);
        $this->assertEquals($user->user_id, $post->user->user_id);
    }

    public function test_post_has_tags()
    {
        $post = Post::factory()->create();

        $tags = Tag::factory()->count(2)->create();

        $post->tags()->attach($tags->pluck('tag_id'));

        $this->assertCount(2, $post->tags);
    }
}
