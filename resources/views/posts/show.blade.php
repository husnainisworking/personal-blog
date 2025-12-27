@extends('layouts.public')
@section('title', $post->title)
@section('content')
    <!-- Single Post View (Public) -->
<article class="max-w-4xl mx-auto">
    <!--Post Header-->
    <header class="mb-8">
        <h1 class="text-2xl sm:text-4xl font-bold text-gray-900 mb-4">{{$post->title}}</h1>

        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-600 mb-4">
            <span>By {{$post->user?->name ?? "Unknown"}}</span>
            <span class="mx-2">•</span>
            <span>{{ optional($post->published_at)->format('F d, Y')}}</span>
            @if($post->category)
                <span class="mx-2">•</span>
                <a href="{{route('categories.show', $post->category->slug)}}" class="text-indigo-600 hover:text-indigo-800">
                    {{$post->category->name}}
                </a>
                @endif
        </div>

        @if($post->tags->count() > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($post->tags as $tag)
                <a href="{{route('tags.show', $tag->slug)}}" class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-300">
                    <!-- Displays clickable tags (like #Laravel, #PHP)-->
                    #{{$tag->name}}
                </a>
                @endforeach
            </div>
            @endif
    </header>

    <!-- Featured Image -->
     @if($post->featured_image)
        <div class="mb-8">
            <img src="{{ asset('storage/' . $post->featured_image) }}"
            alt="{{ $post->title }}"
            class="w-full h-auto rounded-lg shadow-lg">
    </div>
    @endif

    <!-- Post Content -->
    <div class="prose prose-lg max-w-none mb-12 break-words">
        {!! clean(\Illuminate\Support\Str::markdown($post->content)) !!}
        <!-- Converts the post's content(written in Markdown) into HTMl.
        Problem Solved: Authors can write in simple Markdown, but readers can see nicely formatted text.
-->
    </div>

    <hr class="my-12">

    <!-- Comments Section -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Comments ({{$post->approvedComments->count()}})
        </h2>

    <!-- Comment Form Component -->
     <x-comment-form :post="$post" class="mb-8"/>

    <!-- Display Comments -->
        @if($post->approvedComments->count() > 0)
            <div class="space-y-6">
                @foreach($post->approvedComments as $comment)
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center mb-2">
                            <div class="font-semibold text-gray-900">{{$comment->name}}</div>
                            <span class="mx-2 text-gray-400">•</span>
                            <div class="text-sm text-gray-600">{{$comment->created_at->diffForHumans()}}</div>
                            <!--diffforhmns() is a Laravel Carbon Method(Carbon is the date/time library Laravel uses), it takes a date/time and converts into human-friendly string -->
                        </div>
                        <p class="text-gray-700">{{$comment->content}}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No comments yet. Be the first to comment!</p>
        @endif
    </section>
</article>
    @endsection






























