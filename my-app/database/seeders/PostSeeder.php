<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Image;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $tags = Tag::pluck('tag_id', 'slug'); // ['technology' => 2, 'apps' => 4, ...]

        Post::factory()
            ->count(5)
            ->create()
            ->each(function ($post) use ($tags) {
                $image = Image::create([
                    'image_id' => (string) Str::uuid(),
                    'post_id' => $post->post_id,
                    'image_path' => '/img/image-placeholder.svg',
                    'alt_text' => fake()->words(3, true),
                ]);

                // Set image as thumbnail
                $post->thumbnail_image_id = $image->image_id;
                $post->save();

                // Attach tags
                $post->tags()->attach([
                    $tags['technology'] ?? 2,
                    $tags['apps'] ?? 4,
                    $tags['mobile'] ?? 3,
                ]);
            });
    }
}
