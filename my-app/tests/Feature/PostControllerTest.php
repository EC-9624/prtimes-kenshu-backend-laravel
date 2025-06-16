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
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test that the index page displays posts correctly.
     *
     * @return void
     */
    public function test_index_displays_posts()
    {

        // Create a user for association with posts
        $user = User::factory()->create();

        $post1 = Post::factory()->for($user)->create([
            'created_at' => now()->subDays(2),
            'deleted_at' => null, // This post should appear
        ]);
        $post2 = Post::factory()->for($user)->create([
            'created_at' => now()->subDay(),
            'deleted_at' => null, // This post should appear (and be first due to orderBy)
        ]);
        $post3 = Post::factory()->for($user)->create([
            'created_at' => now()->subDays(3),
            'deleted_at' => now(), // This post should NOT appear
        ]);

        // GET reqs
        $response = $this->get('/');

        //Assertions

        $response->assertStatus(200);

        $response->assertViewIs('index');

        // Assert that the view received the correct title.
        $response->assertViewHas('title', 'Home Page');

        // Assert that the view received the 'data' variable.
        $response->assertViewHas('data');

        // Get the data passed to the view
        $viewData = $response->original->getData()['data'];

        // Assert that only the non-deleted posts are in the collection
        $this->assertCount(2, $viewData);

        // Assert that the posts are ordered by created_at in descending order
        $this->assertSame($post2->id, $viewData->first()->id);
        $this->assertSame($post1->id, $viewData->last()->id);

        // Assert that the deleted post is not in the collection
        $this->assertFalse($viewData->contains($post3));

         $response->assertSee($post1->title);
         $response->assertSee($post2->title);
         $response->assertDontSee($post3->title); // The deleted post's title should not be seen
    }

    /**
     * Test that the posts by tag page displays correctly.
     *
     * @return void
     */
    public function test_tag_displays_posts(){
        $user = User::factory()->create();

        $tag = Tag::factory()->create(['slug' => 'technology', 'name' => 'Technology']);

        $post1 = Post::factory()->for($user)->create([
            'title' => 'Tech Post One',
            'deleted_at' => null, // not soft-deleted
        ]);
        $post1->tags()->attach($tag); // Attach the tag to post1

        $post2 = Post::factory()->for($user)->create([
            'title' => 'Tech Post Two',
            'deleted_at' => null,
            'created_at' => now()->addHour(),
        ]);
        $post2->tags()->attach($tag);

        $otherTag = Tag::factory()->create(['slug' => 'sports', 'name' => 'Sports']);

        $post3 = Post::factory()->for($user)->create([
            'title' => 'Sports Post',
            'deleted_at' => null,
        ]);
        $post3->tags()->attach($otherTag);

        $deletedPost = Post::factory()->for($user)->create([
            'title' => 'Deleted Tech Post',
            'deleted_at' => now(), // Soft-deleted
        ]);
        $deletedPost->tags()->attach($tag);

        $response = $this->get(route('posts.byTag', ['tagSlug' => $tag->slug]));

        $response->assertOk();
        $response->assertViewIs('index');
        $response->assertViewHas('title', 'Posts Tagged: ' . $tag->slug);
        $response->assertViewHas('data');

        $postsInView = $response->viewData('data');

        $this->assertCount(2, $postsInView);
        $this->assertTrue($postsInView->contains($post2));
        $this->assertTrue($postsInView->contains($post1));
        $this->assertFalse($postsInView->contains($post3)); // Post 3 (wrong tag) should NOT be there
        $this->assertFalse($postsInView->contains($deletedPost));


    }

}
