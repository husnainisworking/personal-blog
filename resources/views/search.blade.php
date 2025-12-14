@extends('layouts.public')
@section('title', 'Search Results')
@section('content')
    <!-- Search results page-->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Search Results</h1>
        <p class="text-gray-600">Showing results for: <strong>"{{$query}}</strong></p>
        <p class="text-gray-500 text-sm">Found {{$posts->total()}} post(s)</p>
    </div>

    @if($posts->count() > 0)
        <div class="grid gap-8">
            @foreach($posts as $post)
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">
                            <a href="{{route('posts.public.show', $post->slug)}}" class="hover:text-indigo-600">
                                {{$post->title}}
                            </a>
                        </h2>

                        <div class="flex items-center text-sm text-gray-600 mb-4">
                            <span>{{$post->user->name}}</span>
                            <span class="mx-2">•</span>
                            <span>{{$post->published_at->format('F d, Y')}}</span>
                            @if($post->category)
                                <span class="mx-2">•</span>
                                <a href="{{route('categories.show', $post->category->slug)}}" class="text-indigo-600 hover:text-indigo-800">
                                    {{$post->category->name}}
                                </a>
                                @endif
                        </div>

                        @if($post->excerpt)
                            <p class="text-gray-700 mb-4">{{$post->excerpt}}</p>
                            @else
                            <p class="text-gray-700 mb-4">{{Str::limit(strip_tags($post->content), 200)}}</p>
                            @endif

                        @if($post->tags->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($post->tags as $tag)
                                    <a href="{{route('tags.show', $tag->slug)}}" class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-300">
                                        #{{$tag->name}}
                                    </a>
                                    @endforeach
                            </div>
                            @endif

                        <a href="{{route('posts.public.show', $post->slug)}}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            Read more →
                        </a>
                    </div>
                </article>
                @endforeach
        </div>

        <div class="mt-8">
            {{$posts->links()}}
        </div>
        @else
        <div class="text-center py-20">
            <p class="text-gray-500 text-xl">No posts found matching your search.</p>
            <a href="{{route('home')}}" class="text-indigo-600 hover:text-indigo-800 mt-4 inline-block">← Back to home</a>
        </div>
        @endif
    @endsection

























