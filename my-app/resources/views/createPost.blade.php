<x-layout>
    <x-header/>
    <div class="max-w-3xl my-4 mx-auto p-4 sm:p-6 lg:p-8 bg-white shadow-md rounded-lg mt-2">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-400 text-red-700 rounded-lg" role="alert">
                <p class="font-semibold mb-2">Please correct the following errors:</p>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{route('createPost.post')}}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Post Title <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    placeholder="Enter your post title here"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Post URL Slug <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    id="slug"
                    name="slug"
                    value="{{ old('slug') }}"
                    required
                    placeholder="e.g., my-first-post-title"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                <p class="text-xs text-gray-500 mt-1">This will be used in the URL (e.g., /blog/your-slug).</p>
            </div>

            <div>
                <label for="text" class="block text-sm font-medium text-gray-700 mb-1">Post Content <span class="text-red-500">*</span></label>
                <textarea
                    id="text"
                    name="text"
                    rows="15"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                    placeholder="Write your amazing post content here...">{{ old('text') }}</textarea>
            </div>

            <div>
                <label for="thumbnail_image" class="block text-sm font-medium text-gray-700 mb-1">Thumbnail Image</label>
                <input
                    type="file"
                    id="thumbnail_image"
                    name="thumbnail_image"
                    accept="image/jpeg, image/png, image/gif, image/webp"
                    class="w-full  file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                <p class="text-xs text-gray-500 mt-1">Accepted formats: JPG, PNG, GIF, WebP. Max file size: 5MB.</p>
            </div>

            <div>
                <label for="alt_text" class="block text-sm font-medium text-gray-700 mb-1">Image Alt Text (for accessibility)</label>
                <input
                    type="text"
                    id="alt_text"
                    name="alt_text"
                    value="{{ old('alt_text') }}"
                    placeholder="A concise description of the thumbnail image for screen readers"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                <p class="text-xs text-gray-500 mt-1">Important for SEO and accessibility.</p>
            </div>

            <div>
                <label for="additional_images" class="block text-sm font-medium text-gray-700 mb-1">Additional Images</label>
                <input
                    type="file"
                    id="additional_images"
                    name="additional_images[]"
                    multiple
                    accept="image/jpeg, image/png, image/gif, image/webp"
                    class="w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                <p class="text-xs text-gray-500 mt-1">Select multiple images (JPG, PNG, GIF, WebP).</p>
            </div>

            @php
                $availableTags = [
                    'technology' => 'テクノロジー',
                    'mobile' => 'モバイル',
                    'apps' => 'アプリ',
                    'entertainment' => 'エンタメ',
                    'beauty' => 'ビューティー',
                    'fashion' => 'ファッション',
                    'lifestyle' => 'ライフスタイル',
                    'business' => 'ビジネス',
                    'gourmet' => 'グルメ',
                    'sports' => 'スポーツ',
                ];
                $oldTags = old('tag_slugs', []);
            @endphp

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Tags:</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-y-2 gap-x-4">
                    @foreach ($availableTags as $slug => $label)
                        <div class="flex items-center">
                            <input
                                type="checkbox"
                                id="tag_{{ $slug }}"
                                name="tag_slugs[]"
                                value="{{ $slug }}"
                                {{ in_array($slug, $oldTags) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                            <label for="tag_{{ $slug }}" class="ml-2 block text-sm text-gray-900 cursor-pointer">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end pt-4 ">
                <button
                    type="submit"
                    class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out cursor-pointer">
                    Create Post
                </button>
            </div>
        </form>
    </div>
</x-layout>
