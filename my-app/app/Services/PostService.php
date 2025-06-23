<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

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

    /**
     * @param string $postSlug
     * @return Post
     */
    public function getPostBySlug(string $postSlug): Post
    {
        $post = $this->postRepository->fetchPostBySlug($postSlug);
        if (is_null($post)) {
            throw new ModelNotFoundException("Post not found with slug: {$postSlug}");
        }

        return $post;
    }

    /**
     * @param array $validated
     * @param string $userId
     * @param string $postId
     * @return void
     * @throws Throwable
     */
    public function createPost(array $validated, string $userId, string $postId): void
    {
        DB::beginTransaction();

        try {

            $post = $this->postRepository->createPost([
                'post_id' => $postId,
                'user_id' => $userId,
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'text' => $validated['text'],
                'thumbnail_image_id' => null, // initially null
            ]);

            // Handle thumbnail upload
            if ($validated['thumbnail_image'] instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $file = $validated['thumbnail_image'];
                $thumbnailPath = $file->store("posts/{$postId}/thumbnail",'public');

                $image = $this->postRepository->saveImage([
                    'post_id' => $postId,
                    'image_path' => $thumbnailPath,
                    'alt_text' => $validated['alt_text'] ?? null,
                ]);
                $thumbnailImageId = $image->image_id;

                // Update post with thumbnail image ID
                $post->update(['thumbnail_image_id' => $thumbnailImageId]);
            }

            // Handle additional images
            if (is_array($validated['additional_images'])) {
                foreach ($validated['additional_images'] as $image) {
                    $path = $image->store("posts/{$postId}/additional",'public');

                    $this->postRepository->saveImage([
                        'post_id' => $postId,
                        'image_path' => $path,
                        'alt_text' => null,
                    ]);
                }
            }

            // Sync tags
            if (count($validated['tag_slugs']) > 0) {
                $tagIds = $this->postRepository->getTagIdsBySlugs($validated['tag_slugs']);
                $this->postRepository->syncPostTags($post, $tagIds);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param string $post_slug
     * @param array $data
     * @return void
     * @throws Throwable
     */
    public function updatePost(string $post_slug, array $data): void
    {
        DB::beginTransaction();

        try {
            $post = self::getPostBySlug($post_slug);

            $this->postRepository->updatePost($post, $data);

            if (isset($data['tags']) && is_array($data['tags'])) {
                $tagIds = $this->postRepository->getTagIdsBySlugs($data['tags']);
                $this->postRepository->syncPostTags($post, $tagIds);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Failed to update post: ' . $e->getMessage(), ['slug' => $post_slug]);
            throw $e;
        }
    }

    /**
     * @param Post $post
     * @return void
     * @throws Throwable
     */
    public function deletePost(Post $post): void
    {
        DB::beginTransaction();

        try {
            $this->postRepository->softDeletePost($post);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Failed to delete post: ' . $e->getMessage(), ['post_id' => $post->post_id]);
            throw $e;
        }
    }

}
