<?php

namespace App\Repositories;

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
}


