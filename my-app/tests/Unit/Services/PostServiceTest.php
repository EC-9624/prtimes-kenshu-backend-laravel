<?php

namespace Tests\Unit\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use App\Services\PostService;
use Illuminate\Support\Collection;
use Mockery;
use PDOException;
use Tests\TestCase;

class PostServiceTest extends TestCase
{

    public function test_get_all_posts_delegates_to_repository()
    {
        $postRepoMock = Mockery::mock(PostRepository::class);
        $postRepoMock->shouldReceive('fetchAllPosts')
            ->once()
            ->andReturn(collect(['mocked_post']));

        $service = new PostService($postRepoMock);
        $result = $service->getAllPosts();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(['mocked_post'], $result->toArray());
    }

    public function test_get_all_posts_throws_pdo_exception()
    {
        $postRepoMock = Mockery::mock(PostRepository::class);
        $postRepoMock->shouldReceive('fetchAllPosts')
            ->once()
            ->andThrow(new PDOException("Database failure"));

        $service = new PostService($postRepoMock);

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage("Database failure");

        $service->getAllPosts();
    }


    public function test_get_posts_by_tag_slug_delegates_to_repository()
    {
        $postRepoMock = Mockery::mock(PostRepository::class);
        $postRepoMock->shouldReceive('fetchPostsByTagSlug')
            ->with('tech')
            ->once()
            ->andReturn(collect(['post1', 'post2']));

        $service = new PostService($postRepoMock);
        $result = $service->getPostsByTagSlug('tech');

        $this->assertCount(2, $result);
    }

    public function test_get_posts_by_tag_slug_throws_pdo_exception()
    {
        $postRepoMock = Mockery::mock(PostRepository::class);
        $postRepoMock->shouldReceive('fetchPostsByTagSlug')
            ->with('tech')
            ->once()
            ->andThrow(new PDOException("DB error fetching by tag"));

        $service = new PostService($postRepoMock);

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage("DB error fetching by tag");

        $service->getPostsByTagSlug('tech');
    }

    public function test_get_post_by_slug_delegates_to_repository()
    {
        $postMock = Mockery::mock(Post::class);

        $postRepoMock = Mockery::mock(PostRepository::class);
        $postRepoMock->shouldReceive('fetchPostBySlug')
            ->with('post-slug')
            ->once()
            ->andReturn($postMock);

        $service = new PostService($postRepoMock);
        $result = $service->getPostBySlug('post-slug');

        $this->assertSame($postMock, $result);
    }

    public function test_get_post_by_slug_throws_pdo_exception()
    {
        $postRepoMock = Mockery::mock(PostRepository::class);
        $postRepoMock->shouldReceive('fetchPostBySlug')
            ->with('post-slug')
            ->once()
            ->andThrow(new PDOException("DB error fetching by slug"));

        $service = new PostService($postRepoMock);

        $this->expectException(PDOException::class);
        $service->getPostBySlug('post-slug');
    }



    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
