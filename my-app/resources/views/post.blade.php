<x-layout :title="$title">
    <x-header />
    <div class="flex items-center justify-center">
        <div class="space-y-6 m-4 flex flex-col items-center w-full max-w-2xl">
            <div class="flex justify-between max-w-2xl w-full justify-items-center">
                <!-- Back button -->
                <div class="self-start">
                    <a href="{{ url('/') }}" class="text-blue-500 hover:underline text-sm flex items-center">
                        ← Top
                    </a>
                </div>
                <div >
                    <div>
                        <a href="{{ route('editPost', $data->slug) }}" class="text-blue-500 hover:underline text-sm flex items-center">
                            edit post
                        </a>
                        <form method="POST" action="{{ route('deletePost', $data->slug) }}" class="inline ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-500 hover:underline text-sm"
                                    onclick="return confirm('Are you sure you want to delete this post?');">
                                delete post
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            <!-- Post content -->
            <div class="space-y-4 w-full">
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
                <div class="pb-4 w-full">
                    <!-- Title -->
                    <h1 class="text-2xl font-semibold text-gray-900">
                        {{ $data->title }}
                    </h1>

                    <!-- Thumbnail -->
                    @if ($data->thumbnail)
                        <img
                            src="{{ Storage::disk('public')->url($data->thumbnail->image_path) }}"
                            alt="{{ $data->title }}"
                            class="mt-2 w-full rounded">
                    @else
                        <img
                            src="{{ Storage::disk('public')->url('image-placeholder.svg') }}"
                            alt="No thumbnail"
                            class="mt-2 w-full rounded">
                    @endif

                    <!-- Author & Date -->
                    <div class="text-sm text-gray-500 mt-1">
                        <p>
                            Created by
                            <a
                                href="{{ url('/users/' . $data->user->user_name) }}"
                                class="text-blue-400 hover:cursor-pointer hover:border-b">
                                {{ $data->user->user_name }}
                            </a>
                        </p>
                        <div>
                            Published on
                            <time>{{ $data->created_at->format('F j, Y, g:i a') }}</time>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if (count($data->tags) > 0)
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($data->tags as $tag)
                                <a href="{{ url('/categories/' . $tag['slug']) }}">
                                <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                    {{ $tag['name'] }}
                                </span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Post Text -->
                    <div class="mt-4 text-gray-800 leading-relaxed whitespace-pre-line">
                        {{ $data->text }}
                    </div>
                    <!-- Additional Images -->
                    @if (!is_null($data->images) && $data->images->isNotEmpty())
                        <div class="mt-4 grid grid-cols-2 max-w-xl w-full items-center justify-center gap-2">
                            @foreach ($data->images as $img)
                                <img
                                    src="{{ Storage::disk('public')->url($img->image_path) }}"
                                    width="280"
                                    height="280"
                                    class="mt-2 object-cover rounded max-w-xs"
                                    alt="{{ $data->title . 'Additional image' }}"
                                >
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>


</x-layout>
