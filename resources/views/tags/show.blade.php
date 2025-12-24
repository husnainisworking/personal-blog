@extends('layouts.public')
@section('title' , '#', $tag->name)
@section('content')
    <!-- Public-facing page for a tag -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">#{{$tag->name}}</h1>
        <p class="text-gray-600 mt-2">{{$posts->total()}} posts tagged with this</p>
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

                        <div class="flex-items-center text-sm text-gray-600 mb-4">
                            <span>{{$post->user?->name ?? "Unknown"}}</span>
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
    <p class="text-gray-500 text-center py-12">No posts with this tag yet.</p>
    @endif
    @endsection




























