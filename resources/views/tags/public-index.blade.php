@extends('layouts.public')

@section('title', 'Tags')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl sm:text-4xl font-bold text-gray-900 mb-2">Tags</h1>
        <p class="text-gray-600">Browse posts by tag</p>
<x-back-link />
</div>

@if($tags->count() > 0)
    <div class="flex flex-wrap gap-2">
        @foreach($tags as $tag)
        <a href="{{ route('tags.show', $tag->slug) }}"
            class="bg-white shadow px-3 py-2 rounded-full text-sm text-gray-700 hover:shadow-md transition">
            #{{ $tag->name }} <span class="text-gray-500">({{ $tag-> posts_count }})</span>
</a>
@endforeach
</div>
@else
    <p class="text-gray-500">No tags found.</p>
    @endif
    @endsection