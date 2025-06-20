<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
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

    /**
     * @dataProvider technologyTagPostsProvider
     */
    public function test_technology_tag_displays_correct_posts($url, $expectedTitle, $expectedPosts, $unexpectedPosts): void
    {
        $this->performPostDisplayAssertions($url, $expectedTitle, $expectedPosts, $unexpectedPosts);
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


    /**
     * @dataProvider sportsTagPostsProvider
     */
    public function test_sports_tag_displays_correct_posts($url, $expectedTitle, $expectedPosts, $unexpectedPosts): void
    {
        $this->performPostDisplayAssertions($url, $expectedTitle, $expectedPosts, $unexpectedPosts);
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

    /**
     * @dataProvider postDetailProvider
     */
    public function test_post_detail_displays_correct_posts($slugKey, $shouldSee, $shouldNotSee): void
    {
        $user = User::factory()->create();

        $posts = [
            'visiblePost' => Post::factory()->for($user)->create([
                'title' => 'Visible Post',
                'slug' => 'visible-post',
                'deleted_at' => null,
            ]),
            'deletedPost' => Post::factory()->for($user)->create([
                'title' => 'Deleted Post',
                'slug' => 'deleted-post',
                'deleted_at' => now(),
            ]),
        ];

        $slug = $posts[$slugKey]->slug;


        $response = $this->get("/posts/{$slug}");

        if ($shouldSee) {
            $response->assertOk();
            $response->assertViewIs('post');
            $response->assertViewHas('data', $posts[$slugKey]);
            $response->assertSee($posts[$slugKey]->title);
        } else {
            $response->assertNotFound();
            $response->assertDontSee($posts[$slugKey]->title);
        }
    }

    public static function postDetailProvider(): array
    {
        return [
            'visible post should display' => [
                'slugKey' => 'visiblePost',
                'shouldSee' => true,
                'shouldNotSee' => false,
            ],
            'deleted post should not display' => [
                'slugKey' => 'deletedPost',
                'shouldSee' => false,
                'shouldNotSee' => true,
            ],
        ];
    }

    public function test_post_detail_returns_404_for_nonexistent_slug(): void
    {
        $response = $this->get('/posts/non-existent-slug');
        $response->assertNotFound();
    }

    public function test_display_create_post(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('createPost'));

        $response->assertStatus(200);
    }

    public function test_guest_is_redirected_when_accessing_create_post(): void
    {
        $response = $this->get(route('createPost'));

        $response->assertRedirect(route('login'));
    }

    public function test_create_post_successfully_redirects_and_shows_success_message(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $thumbnail = UploadedFile::fake()->create('thumb.jpg', 100, 'image/jpeg');
        $additional = [
            UploadedFile::fake()->create('img1.jpg', 100, 'image/jpeg'),
        ];

        $payload = [
            'title' => 'Test Post',
            'slug' => 'test-post',
            'text' => 'This is the post body.',
            'thumbnail_image' => $thumbnail,
            'alt_text' => 'Thumbnail alt text',
            'additional_images' => $additional,
            'tag_slugs' => [], //  no tags for this test
        ];

        // Mock the PostService
        $mock = Mockery::mock(PostService::class);
        $mock->shouldReceive('createPost')->once();
        $this->app->instance(PostService::class, $mock);

        $response = $this->post(route('createPost.post'), $payload);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Post successfully created.');
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

}
