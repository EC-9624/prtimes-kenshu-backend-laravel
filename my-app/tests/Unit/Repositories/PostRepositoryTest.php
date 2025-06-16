<?php

namespace Tests\Unit\Repositories;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PostRepository();
    }

    public function test_get_all_posts()
    {
        $user = User::factory()->create();

        $visible = Post::factory()->for($user)->create(['deleted_at' => null]);
        $deleted = Post::factory()->for($user)->create(['deleted_at' => now()]);

        $posts = $this->repository->fetchAllPosts();

        $this->assertTrue($posts->contains($visible));
        $this->assertFalse($posts->contains($deleted));
    }

    public function test_get_posts_by_tag_slug()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['slug' => 'tech']);
        $otherTag = Tag::factory()->create(['slug' => 'other']);


        $postWithTag = Post::factory()->for($user)->create(['deleted_at' => null]);
        $postWithTag->tags()->attach($tag);

        $postWithoutTag = Post::factory()->for($user)->create(['deleted_at' => null]);

        $postWithOtherTag = Post::factory()->for($user)->create(['deleted_at' => now()]);
        $postWithoutTag->tags()->attach($otherTag);

        $deleted = Post::factory()->for($user)->create(['deleted_at' => now()]);
        $deleted->tags()->attach($tag);

        $result = $this->repository->fetchPostsByTagSlug('tech');

        $this->assertTrue($result->contains($postWithTag));
        $this->assertFalse($result->contains($postWithoutTag));
        $this->assertFalse($result->contains($postWithOtherTag));
        $this->assertFalse($result->contains($deleted));
    }


}
