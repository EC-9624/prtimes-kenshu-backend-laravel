<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Support\Collection;

class PostService
{
    protected PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @return Collection
     */
    public function getAllPosts(): Collection
    {
        return $this->postRepository->fetchAllPosts();
    }

    /**
     * @param string $slug
     * @return Collection
     */
    public function getPostsByTagSlug(string $slug): Collection
    {
        return $this->postRepository->fetchPostsByTagSlug($slug);
    }

    public function getPostBySlug(string $postSlug): Post
    {
        return $this->postRepository->fetchPostBySlug($postSlug);
    }
}
