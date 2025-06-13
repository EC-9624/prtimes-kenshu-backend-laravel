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
    private const TECHNOLOGY_TAG_FALLBACK_ID = 2;
    private const APPS_TAG_FALLBACK_ID = 4;
    private const MOBILE_TAG_FALLBACK_ID = 3;
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
                    $tags['technology'] ?? self::TECHNOLOGY_TAG_FALLBACK_ID,
                    $tags['apps'] ?? self::APPS_TAG_FALLBACK_ID,
                    $tags['mobile'] ?? self::MOBILE_TAG_FALLBACK_ID,
                ]);
            });
    }
}
