<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Tag;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_belongs_to_user()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $this->assertInstanceOf(User::class, $post->user);
        $this->assertSame($user->user_id, $post->user->user_id);
    }

    public function test_post_has_tags()
    {
        $post = Post::factory()->create();

        $tags = Tag::factory()->count(2)->create();

        $post->tags()->attach($tags->pluck('tag_id'));

        $this->assertCount(2, $post->tags);
    }

    public function test_post_has_images()
    {
        $post = Post::factory()
            ->has(Image::factory()->count(3))
            ->create();

        $this->assertCount(3, $post->images);
        $this->assertInstanceOf(Image::class, $post->images->first());
    }

    public function test_post_has_thumbnail_image()
    {
        $image = Image::factory()->create();
        $post = Post::factory()->create([
            'thumbnail_image_id' => $image->image_id,
        ]);

        $this->assertEquals($image->image_id, $post->thumbnail->image_id);
    }
}
