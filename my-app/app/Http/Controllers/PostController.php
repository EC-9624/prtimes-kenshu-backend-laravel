<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;

class PostController extends Controller
{
    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        $posts = $this->postService->getAllPosts();
        return view('index', [
            'title' => 'Home Page',
            'data' => $posts,
        ]);
    }

    /**
     * @param string $tagSlug
     * @return View
     */
    public function showPostsByTag(string $tagSlug): View
    {
        $posts = $this->postService->getPostsByTagSlug($tagSlug);

        return view('index', [
            'title' => 'Posts Tagged: ' . $tagSlug,
            'data' => $posts,
        ]);
    }

    /**
     * @param string $postSlug
     * @return View
     */
    public function showPost(string $postSlug): View
    {
        $post = $this->postService->getPostBySlug($postSlug);

        return view('post',['title' => 'Post Detail Page', 'data' => $post]);
    }

    public function showCreatePost(): View{
        return view('createPost');
    }

    public function createPost(Request $request)
    {
        $userId = Auth::id();
        $postId = Str::uuid()->toString();

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug',
            'text' => 'required|string',
            'thumbnail_image' => 'nullable|image|max:5120', // 5MB
            'alt_text' => 'nullable|string|max:255',
            'additional_images' => 'nullable|array',
            'additional_images.*' => 'image|max:5120',
            'tag_slugs' => 'nullable|array',
            'tag_slugs.*' => 'string|exists:tags,slug',
        ]);

        $title = $validatedData['title'];
        $slug = $validatedData['slug'];
        $text = $validatedData['text'];
        $altText = $validatedData['alt_text'] ?? null;
        $tagSlugs = $validatedData['tag_slugs'] ?? [];
        $thumbnailImagePath = null;
        $additionalImagesPath = [];

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail_image')) {
            $thumbnailImage = $request->file('thumbnail_image');
            $thumbnailImagePath = $thumbnailImage->store("posts/{$postId}/thumbnail");
        }


        // Handle additional images
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $image) {
                if ($image->isValid()) {
                    $img_path = $image->store("posts/{$postId}/additional");
                    $additionalImagesPath[] = $img_path;
                }
            }
        }

        dd([
            'post_id' => $postId,
            'user_id' => $userId,
            'title' => $title,
            'slug' => $slug,
            'text' => $text,
            'altText' => $altText,
            'tagSlugs' => $tagSlugs,
            'thumbnailPath' => $thumbnailImagePath,
            'additionalImagePath' => $additionalImagesPath,
        ]);
    }

}
