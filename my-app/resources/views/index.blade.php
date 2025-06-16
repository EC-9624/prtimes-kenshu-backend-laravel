<x-layout :title="$title">
    <h1>{{ $title }}</h1>

    @if ($errors)
        <div class="text-red-500">{{ $errors }}</div>
    @endif

    @forelse ($data as $post)
        <div class="mb-4">
            <h2>{{ $post->title }}</h2>
            <p>By {{ $post->user->user_name }}</p>
            @if ($post->thumbnail)
                <img src="{{ asset($post->thumbnail->image_path) }}" alt="Thumbnail" />
            @endif

            <ul>
                @foreach ($post->tags as $tag)
                    <li>{{ $tag->name }}</li>
                @endforeach
            </ul>
        </div>
    @empty
        <p>No posts found.</p>
    @endforelse
</x-layout>
