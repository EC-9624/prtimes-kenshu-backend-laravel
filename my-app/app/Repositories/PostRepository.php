<?php

namespace App\Repositories;

use App\Models\Image;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Collection;

class PostRepository
{
    /**
     * @return Collection
     */
    public function fetchAllPosts(): Collection
    {
        return Post::with(['user', 'thumbnail', 'tags'])
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @param string $slug
     * @return Collection
     */
    public function fetchPostsByTagSlug(string $slug): Collection
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        return $tag->posts()
            ->with(['user', 'thumbnail', 'tags'])
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @param string $postSlug
     * @return Post|null
     */
    public function fetchPostBySlug(string $postSlug): ?Post
    {
        return Post::with(['user', 'thumbnail', 'tags','images'])
            ->whereNull('deleted_at')
            ->where('slug', $postSlug)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * @param array $data
     * @return Post
     */
    public function createPost(array $data): Post
    {
        return Post::create($data);
    }

    /**
     * @param array $data
     * @return Image
     */
    public function saveImage(array $data): Image
    {
        return Image::create($data);
    }

    /**
     * @param array $slugs
     * @return array
     */
    public function getTagIdsBySlugs(array $slugs): array
    {
        return Tag::whereIn('slug', $slugs)->pluck('tag_id')->toArray();
    }

    /**
     * @param Post $post
     * @param array $tagIds
     * @return void
     */
    public function syncPostTags(Post $post, array $tagIds): void
    {
        $post->tags()->sync($tagIds);
    }

}


