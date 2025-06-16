<?php

namespace Tests\Unit\Repositories;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PostRepository();
    }

    // --- fetchAllPosts Tests ---

    /**
     * @dataProvider fetchAllPostsNormalCases
     */
    public function test_fetch_all_posts_normal_cases($deletedAt, $shouldBeIncluded, $description)
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create(['deleted_at' => $deletedAt]);

        $posts = $this->repository->fetchAllPosts();

        if ($shouldBeIncluded) {
            $this->assertTrue($posts->contains($post), $description);
        } else {
            $this->assertFalse($posts->contains($post), $description);
        }
    }

    public static function fetchAllPostsNormalCases(): array
    {
        return [
            'visible post should be included' => [null, true, 'Visible post should be included in results'],
        ];
    }

    /**
     * @dataProvider fetchAllPostsAbnormalCases
     */
    public function test_fetch_all_posts_abnormal_cases($deletedAt, $shouldBeIncluded, $description)
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create(['deleted_at' => $deletedAt]);

        $posts = $this->repository->fetchAllPosts();

        if ($shouldBeIncluded) {
            $this->assertTrue($posts->contains($post), $description);
        } else {
            $this->assertFalse($posts->contains($post), $description);
        }
    }

    public static function fetchAllPostsAbnormalCases(): array
    {
        return [
            'deleted post should be excluded' => [now(), false, 'Deleted post should be excluded from results'],
        ];
    }

    // --- fetchPostsByTagSlug Tests ---

    /**
     * @dataProvider fetchPostsByTagSlugNormalCases
     */
    public function test_fetch_posts_by_tag_slug_normal_cases($setupCallback, $tagSlug, $shouldBeIncluded, $description)
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $setupCallback($post);

        $result = $this->repository->fetchPostsByTagSlug($tagSlug);

        if ($shouldBeIncluded) {
            $this->assertTrue($result->contains('id', $post->id), $description);
        } else {
            $this->assertFalse($result->contains('id', $post->id), $description);
        }
    }

    public static function fetchPostsByTagSlugNormalCases(): array
    {
        return [
            'post with matching tag should be included' => [
                function ($post) {
                    $tag = Tag::factory()->create(['slug' => 'tech']);
                    $post->update(['deleted_at' => null]);
                    $post->tags()->attach($tag);
                },
                'tech',
                true,
                'Post with matching tag should be included'
            ],
        ];
    }

    /**
     * @dataProvider fetchPostsByTagSlugAbnormalCases
     */
    public function test_fetch_posts_by_tag_slug_abnormal_cases($setupCallback, $tagSlug, $shouldBeIncluded, $description)
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $setupCallback($post);

        $result = $this->repository->fetchPostsByTagSlug($tagSlug);

        if ($shouldBeIncluded) {
            $this->assertTrue($result->contains('id', $post->id), $description);
        } else {
            $this->assertFalse($result->contains('id', $post->id), $description);
        }
    }

    public static function fetchPostsByTagSlugAbnormalCases(): array
    {
        return [
            'post without tag should be excluded' => [
                function ($post) {
                    Tag::factory()->create(['slug' => 'tech']);
                    $post->update(['deleted_at' => null]);
                },
                'tech',
                false,
                'Post without any tags should be excluded'
            ],

            'post with different tag should be excluded' => [
                function ($post) {
                    Tag::factory()->create(['slug' => 'tech']);
                    $otherTag = Tag::factory()->create(['slug' => 'other']);
                    $post->update(['deleted_at' => null]);
                    $post->tags()->attach($otherTag);
                },
                'tech',
                false,
                'Post with different tag should be excluded'
            ],

            'deleted post with matching tag should be excluded' => [
                function ($post) {
                    $tag = Tag::factory()->create(['slug' => 'tech']);
                    $post->tags()->attach($tag);
                    $post->delete();
                },
                'tech',
                false,
                'Deleted post with matching tag should be excluded'
            ],
        ];
    }

    public function test_fetch_posts_by_nonexistent_tag_slug_returns_empty()
    {
        try {
            $result = $this->repository->fetchPostsByTagSlug('nonexistent');
            $this->assertTrue($result->isEmpty());
        } catch (ModelNotFoundException $e) {
            $this->addToAssertionCount(1);
        }
    }
}
