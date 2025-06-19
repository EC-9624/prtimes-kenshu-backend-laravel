<x-layout :title="$title">
    <x-header />
    <div class="flex items-center justify-center">
        <div class="space-y-6 m-4 flex flex-col items-center w-full max-w-2xl">
            <!-- Back button -->
            <div class="self-start">
                <a href="{{ url('/') }}" class="text-blue-500 hover:underline text-sm flex items-center">
                    ← Top
                </a>
            </div>

            <!-- Post content -->
            <div class="space-y-4 w-full">
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
{{--                    @if (!empty($data->additionalImages))--}}
{{--                        <div class="mt-4 grid grid-cols-2 max-w-xl w-full items-center justify-center gap-2">--}}
{{--                            @foreach ($data->additionalImages as $img)--}}
{{--                                <img--}}
{{--                                    src="{{ $img['image_path'] }}"--}}
{{--                                    width="280"--}}
{{--                                    height="280"--}}
{{--                                    class="mt-2 object-cover rounded max-w-xs">--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
{{--                    @endif--}}
                </div>
            </div>
        </div>
    </div>


</x-layout>
