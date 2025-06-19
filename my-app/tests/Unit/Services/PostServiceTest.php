<?php

namespace Tests\Unit\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\Image;
use App\Repositories\PostRepository;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery;
use PDOException;
use Tests\TestCase;
use Throwable;

class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PostRepository $postRepoMock;
    protected PostService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->postRepoMock = Mockery::mock(PostRepository::class);
        $this->service = new PostService($this->postRepoMock);
    }

    public function test_get_all_posts_delegates_to_repository(): void
    {
        $this->postRepoMock->shouldReceive('fetchAllPosts')
            ->once()
            ->andReturn(collect(['mocked_post']));

        $result = $this->service->getAllPosts();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(['mocked_post'], $result->toArray());
    }

    public function test_get_all_posts_throws_pdo_exception(): void
    {
        $this->postRepoMock->shouldReceive('fetchAllPosts')
            ->once()
            ->andThrow(new PDOException("Database failure"));

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage("Database failure");

        $this->service->getAllPosts();
    }

    public function test_get_posts_by_tag_slug_delegates_to_repository(): void
    {
        $this->postRepoMock->shouldReceive('fetchPostsByTagSlug')
            ->with('tech')
            ->once()
            ->andReturn(collect(['post1', 'post2']));

        $result = $this->service->getPostsByTagSlug('tech');

        $this->assertCount(2, $result);
    }

    public function test_get_posts_by_tag_slug_throws_pdo_exception(): void
    {
        $this->postRepoMock->shouldReceive('fetchPostsByTagSlug')
            ->with('tech')
            ->once()
            ->andThrow(new PDOException("DB error fetching by tag"));

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage("DB error fetching by tag");

        $this->service->getPostsByTagSlug('tech');
    }

    public function test_get_post_by_slug_delegates_to_repository(): void
    {
        $postMock = Mockery::mock(Post::class);

        $this->postRepoMock->shouldReceive('fetchPostBySlug')
            ->with('post-slug')
            ->once()
            ->andReturn($postMock);

        $result = $this->service->getPostBySlug('post-slug');

        $this->assertSame($postMock, $result);
    }

    public function test_get_post_by_slug_throws_pdo_exception(): void
    {
        $this->postRepoMock->shouldReceive('fetchPostBySlug')
            ->with('post-slug')
            ->once()
            ->andThrow(new PDOException("DB error fetching by slug"));

        $this->expectException(PDOException::class);
        $this->service->getPostBySlug('post-slug');
    }

    /**
     * @throws Throwable
     */
    public function test_create_post_with_thumbnail_and_tags(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $postId = Str::uuid()->toString();

        $thumbnail = UploadedFile::fake()->image('thumb.jpg');
        $additional = [
            UploadedFile::fake()->image('img1.jpg'),
            UploadedFile::fake()->image('img2.jpg'),
        ];

        $validated = [
            'title' => 'Test Post',
            'slug' => 'test-post',
            'text' => 'Some text',
            'thumbnail_image' => $thumbnail,
            'alt_text' => 'Alt text',
            'additional_images' => $additional,
            'tag_slugs' => ['laravel', 'tech'],
        ];

        // Mock Post instance
        $mockPost = Mockery::mock(Post::class)->makePartial();
        $mockPost->post_id = $postId;

        $this->postRepoMock
            ->shouldReceive('createPost')
            ->once()
            ->with(Mockery::on(fn ($data) => $data['post_id'] === $postId))
            ->andReturn($mockPost);

        // Mock image creation for thumbnail
        $mockThumbnailImage = Image::factory()->make(['post_id' => $postId]);
        $this->postRepoMock
            ->shouldReceive('saveImage')
            ->once()
            ->with(Mockery::on(fn ($data) =>
            str_starts_with($data['image_path'], "posts/{$postId}/thumbnail")
            ))
            ->andReturn($mockThumbnailImage);

        // Expect post update after thumbnail image is created
        $mockPost
            ->shouldReceive('update')
            ->once()
            ->with(['thumbnail_image_id' => $mockThumbnailImage->image_id]);

        // Mock additional images
        $this->postRepoMock
            ->shouldReceive('saveImage')
            ->times(count($additional))
            ->with(Mockery::on(fn ($data) =>
            str_starts_with($data['image_path'], "posts/{$postId}/additional")
            ));

        // Mock tag syncing
        $this->postRepoMock
            ->shouldReceive('getTagIdsBySlugs')
            ->once()
            ->with(['laravel', 'tech'])
            ->andReturn([1, 2]);

        $this->postRepoMock
            ->shouldReceive('syncPostTags')
            ->once()
            ->with($mockPost, [1, 2]);

        // Act
        $this->service->createPost($validated, $user->user_id, $postId);

        // Assert fake storage
        Storage::disk('public')->assertExists("posts/{$postId}/thumbnail/{$thumbnail->hashName()}");

        foreach ($additional as $img) {
            Storage::disk('public')->assertExists("posts/{$postId}/additional/{$img->hashName()}");
        }
    }


    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
