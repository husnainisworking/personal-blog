@extends('layouts.public')
@section('title', 'Home')

@section('content')
    <!-- Homepage (Welcome Page) of blog. -->
    <div class="mb-12 text-center">
        <h1 class="text-5xl font-bold text-gray-900 mb-4">Welcome to My Blog</h1>
        <p class="text-xl text-gray-600">Sharing thoughts, ideas, and stories</p>
    </div>

    @if($posts->count() > 0)
        <div class="grid gap-8">
            @foreach($posts as $post)
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-3">
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
                            <p class="text-gray-700 text-lg mb-4">{{$post->excerpt}}</p>
                            @else
                            <p class="text-gray-700 text-lg mb-4">{{Str::limit(strip_tags($post->content), 200)}}</p>
                        @endif

                        @if($post->tags->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($post->tags as $tag)
                                    <a href="{{route('tags.show', $tag->slug)}}" class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-300">
                                        #{{$tag->name}}
                                    </a>
                                    @endforeach
                            </div>
                            @endif
                        <a href="{{route('posts.public.show', $post->slug)}}" class="text-indigo-600 hover:text-indigo-800 font-medium text-lg">
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
            <p class="text-gray-500 text-xl">No posts yet. Check back soon!</p>
        </div>
    @endif
    @endsection
























