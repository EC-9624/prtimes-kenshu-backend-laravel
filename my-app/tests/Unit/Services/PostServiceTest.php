<?php

namespace Tests\Unit\Services;

use App\Repositories\PostRepository;
use App\Services\PostService;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class PostServiceTest extends TestCase
{

    public function test_get_all_posts_delegates_to_repository()
    {
        $mock = Mockery::mock(PostRepository::class);
        $mock->shouldReceive('fetchAllPosts')
            ->once()
            ->andReturn(collect(['mocked_post']));

        $service = new PostService($mock);
        $result = $service->getAllPosts();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(['mocked_post'], $result->toArray());
    }


    public function test_get_posts_by_tag_slug_delegates_to_repository()
    {
        $mock = Mockery::mock(PostRepository::class);
        $mock->shouldReceive('fetchPostsByTagSlug')
            ->with('tech')
            ->once()
            ->andReturn(collect(['post1', 'post2']));

        $service = new PostService($mock);
        $result = $service->getPostsByTagSlug('tech');

        $this->assertCount(2, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
