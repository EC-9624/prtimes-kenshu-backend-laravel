<x-layout :title="$title">
    <x-header />
    <div class="max-w-3xl mx-auto px-4 py-8 space-y-6">
        <h1 class="text-3xl font-bold text-gray-800">{{ $title }}</h1>

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 border border-red-300 rounded p-4 mb-6">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @forelse ($data as $post)
            <div class="bg-white shadow rounded-lg p-6 space-y-4 border border-gray-200">
                <a href="/posts/{{$post->slug}}">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $post->title }}</h2>
                    <p class="text-sm text-gray-500">By {{ $post->user->user_name }}</p>

                    @if ($post->thumbnail)
                        <img
                            src="{{ asset($post->thumbnail->image_path) }}"
                            alt="Thumbnail"
                            class="w-full h-auto rounded border"
                        />
                    @endif
                </a>

                @if ($post->tags->count() > 0)
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach ($post->tags as $tag)
                            <a href="/categories/{{$tag->slug}}">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                                    {{ $tag->name }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500">No posts found.</p>
        @endforelse
    </div>
</x-layout>
