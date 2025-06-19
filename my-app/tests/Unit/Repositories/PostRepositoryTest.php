<?php

namespace Tests\Unit\Repositories;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    // --- fetchPostBySlug Tests ---

    /**
     * @dataProvider fetchPostBySlugNormalCases
     */
    public function test_fetch_post_by_slug_normal_cases($setupCallback, $slugToSearch, $shouldFindPost, $description)
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $setupCallback($post);

        $result = $this->repository->fetchPostBySlug($slugToSearch);

        if ($shouldFindPost) {
            $this->assertNotNull($result, $description);
            $this->assertSame($post->id, $result->id, $description);
        } else {
            $this->assertNull($result, $description);
        }
    }

    public static function fetchPostBySlugNormalCases(): array
    {
        return [
            'slug matches and not soft deleted' => [
                function ($post) {
                    $post->update(['slug' => 'existing-slug', 'deleted_at' => null]);
                },
                'existing-slug',
                true,
                'Should find the post with matching slug and not soft deleted',
            ],
        ];
    }


    /**
     * @dataProvider fetchPostBySlugAbnormalCases
     */
    public function test_fetch_post_by_slug_abnormal_cases($setupCallback, $slugToSearch, $shouldFindPost, $description)
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $setupCallback($post);

        $result = $this->repository->fetchPostBySlug($slugToSearch);

        if ($shouldFindPost) {
            $this->assertNotNull($result, $description);
            $this->assertSame($post->id, $result->id, $description);
        } else {
            $this->assertNull($result, $description);
        }
    }
    public static function fetchPostBySlugAbnormalCases(): array
    {
        return [
            'slug does not match any post' => [
                function ($post) {
                    $post->update(['slug' => 'unrelated-slug']);
                },
                'non-existent-slug',
                false,
                'Should return null when no post matches the slug',
            ],
            'post is soft deleted' => [
                function ($post) {
                    $post->update(['slug' => 'deleted-slug']);
                    $post->delete();
                },
                'deleted-slug',
                false,
                'Should return null when post is soft deleted',
            ],
        ];
    }

    /**
     * @dataProvider createPostCases
     */
    /**
     * @dataProvider createPostCases
     */
    public function test_create_post($title, $slug, $description)
    {
        $user = User::factory()->create();

        $post = $this->repository->createPost([
            'post_id' => (string) Str::uuid(),
            'user_id' => $user->user_id,
            'title' => $title,
            'slug' => $slug,
            'text' => 'Body text',
            'thumbnail_image_id' => null,
        ]);

        $this->assertTrue(
            DB::table('posts')->where('post_id', $post->post_id)->exists(),
            $description
        );
    }

    public static function createPostCases(): array
    {
        return [
            'basic title and slug' => [
                'Test Title',
                'test-title',
                'Should create a post with given title and slug',
            ],
        ];
    }


    /**
     * @dataProvider saveImageCases
     */
    public function test_save_image($imagePath, $altText, $description)
    {
        $post = Post::factory()->create();

        $image = $this->repository->saveImage([
            'post_id' => $post->post_id,
            'image_path' => $imagePath,
            'alt_text' => $altText,
        ]);
        $this->assertTrue(
            DB::table('images')->where('image_id', $image->image_id)->exists(),
            $description
        );

    }

    public static function saveImageCases(): array
    {
        return [
            'simple path and alt' => [
                'posts/test/image.jpg',
                'Alt text',
                'Should create image with correct path and alt',
            ],
            'no alt text' => [
                'posts/test/no-alt.jpg',
                null,
                'Should create image even if alt text is null',
            ],
        ];
    }


    /**
     * @dataProvider getTagIdsBySlugsCases
     */
    public function test_get_tag_ids_by_slugs($existingSlugs, $searchSlugs, $expectedCount, $description)
    {
        foreach ($existingSlugs as $slug) {
            Tag::factory()->create(['slug' => $slug]);
        }

        $tagIds = $this->repository->getTagIdsBySlugs($searchSlugs);

        $this->assertCount($expectedCount, $tagIds, $description);
    }

    public static function getTagIdsBySlugsCases(): array
    {
        return [
            'match two slugs' => [['news', 'tech'], ['news', 'tech'], 2, 'Should match both slugs'],
            'match one of three' => [['news'], ['news', 'unknown'], 1, 'Should match one known slug'],
            'no matches' => [[], ['unknown'], 0, 'Should return empty for no matches'],
        ];
    }

    /**
     * @dataProvider syncPostTagsCases
     */
    public function test_sync_post_tags($tagCount, $description)
    {
        $post = Post::factory()->create();
        $tags = Tag::factory()->count($tagCount)->create();

        $tagIds = $tags->pluck('tag_id')->toArray();
        $this->repository->syncPostTags($post, $tagIds);

        $this->assertSame($tagCount, $post->tags()->count(), $description);
    }

    public static function syncPostTagsCases(): array
    {
        return [
            'sync two tags' => [2, 'Should sync two tags to post'],
            'sync zero tags' => [0, 'Should detach all tags if given empty array'],
        ];
    }


}
