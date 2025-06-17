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
     * @dataProvider indexPagePostsProvider
     */
    public function test_index_page_displays_correct_posts($url, $expectedTitle, $expectedPosts, $unexpectedPosts): void
    {
        $this->performPostDisplayAssertions($url, $expectedTitle, $expectedPosts, $unexpectedPosts);
    }

    /**
     * @dataProvider technologyTagPostsProvider
     */
    public function test_technology_tag_displays_correct_posts($url, $expectedTitle, $expectedPosts, $unexpectedPosts): void
    {
        $this->performPostDisplayAssertions($url, $expectedTitle, $expectedPosts, $unexpectedPosts);
    }

    /**
     * @dataProvider sportsTagPostsProvider
     */
    public function test_sports_tag_displays_correct_posts($url, $expectedTitle, $expectedPosts, $unexpectedPosts): void
    {
        $this->performPostDisplayAssertions($url, $expectedTitle, $expectedPosts, $unexpectedPosts);
    }

    protected function performPostDisplayAssertions($url, $expectedTitle, $expectedPosts, $unexpectedPosts): void
    {
        $user = User::factory()->create();

        // Tags
        $tagTechnology = Tag::factory()->create(['slug' => 'technology', 'name' => 'Technology']);
        $tagSports = Tag::factory()->create(['slug' => 'sports', 'name' => 'Sports']);

        // Posts
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

        // Tag attachments
        $posts['post1']->tags()->attach($tagTechnology);
        $posts['post2']->tags()->attach($tagTechnology);
        $posts['post3']->tags()->attach($tagSports);
        $posts['deletedPost']->tags()->attach($tagTechnology);

        // Make the request
        $response = $this->get($url);

        $response->assertOk();
        $response->assertViewIs('index');
        $response->assertViewHas('title', $expectedTitle);
        $response->assertViewHas('data');

        $viewPosts = $response->viewData('data');

        foreach ($expectedPosts as $key) {
            $this->assertTrue($viewPosts->contains($posts[$key]), "Expected post `{$key}` not found.");
            $response->assertSee($posts[$key]->title);
        }

        foreach ($unexpectedPosts as $key) {
            $this->assertFalse($viewPosts->contains($posts[$key]), "Unexpected post `{$key}` found.");
            $response->assertDontSee($posts[$key]->title);
        }
    }

    public static function indexPagePostsProvider(): array
    {
        return [
            'index page posts' => [
                '/',
                'Home Page',
                ['post1', 'post2', 'post3'],
                ['deletedPost'],
            ],
        ];
    }

    public static function technologyTagPostsProvider(): array
    {
        return [
            'technology tag posts' => [
                '/categories/technology',
                'Posts Tagged: technology',
                ['post1', 'post2'],
                ['post3', 'deletedPost'],
            ],
        ];
    }

    public static function sportsTagPostsProvider(): array
    {
        return [
            'sports tag posts' => [
                '/categories/sports',
                'Posts Tagged: sports',
                ['post3'],
                ['post1', 'post2', 'deletedPost'],
            ],
        ];
    }
}
