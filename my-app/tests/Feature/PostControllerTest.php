<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    /**
     * @dataProvider postsDataProvider
     */
    public function test_posts_display($url, $expectedTitle, $expectedPosts, $unexpectedPosts): void
    {
        $user = User::factory()->create();

        // Create posts and tags
        $tagTechnology = Tag::factory()->create(['slug' => 'technology', 'name' => 'Technology']);
        $tagSports = Tag::factory()->create(['slug' => 'sports', 'name' => 'Sports']);

        $posts = [
            'post1' => Post::factory()->for($user)->create([
                'title' => 'Tech Post One',
                'deleted_at' => null,
                'created_at' => now()->subDays(2),
            ]),
            'post2' => Post::factory()->for($user)->create([
                'title' => 'Tech Post Two',
                'deleted_at' => null,
                'created_at' => now()->addHour(),
            ]),
            'post3' => Post::factory()->for($user)->create([
                'title' => 'Sports Post',
                'deleted_at' => null,
            ]),
            'deletedPost' => Post::factory()->for($user)->create([
                'title' => 'Deleted Tech Post',
                'deleted_at' => now(),
            ]),
        ];

        // Attach tags
        $posts['post1']->tags()->attach($tagTechnology);
        $posts['post2']->tags()->attach($tagTechnology);
        $posts['post3']->tags()->attach($tagSports);
        $posts['deletedPost']->tags()->attach($tagTechnology);

        $response = $this->get($url);

        $response->assertOk();
        $response->assertViewIs('index');
        $response->assertViewHas('title', $expectedTitle);
        $response->assertViewHas('data');

        $viewPosts = $response->viewData('data');

        // Assert posts that should appear
        foreach ($expectedPosts as $postKey) {
            $this->assertTrue($viewPosts->contains($posts[$postKey]));
            $response->assertSee($posts[$postKey]->title);
        }

        // Assert posts that should NOT appear
        foreach ($unexpectedPosts as $postKey) {
            $this->assertFalse($viewPosts->contains($posts[$postKey]));
            $response->assertDontSee($posts[$postKey]->title);
        }
    }

    public static function postsDataProvider(): array
    {
        return [
            'index page posts' => [
                '/',
                'Home Page',
                ['post1', 'post2','post3'], //expected
                [ 'deletedPost'], //unexpected
            ],
            'technology tag posts' => [
                '/categories/technology',
                'Posts Tagged: technology',
                ['post1', 'post2'],
                ['post3', 'deletedPost'],
            ],
            'sports tag posts' => [
                '/categories/sports',
                'Posts Tagged: sports',
                ['post3'],
                ['post1', 'post2', 'deletedPost'],
            ],
        ];
    }

}
