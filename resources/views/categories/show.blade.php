{{-- resource/views/categories/show.blade.php --}}
@extends('layouts.admin')

@section('title', $category->name)

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="p-6 border-b">
        <h2 class="text-2xl font-bold text-gray-800">{{ $category->name }}</h2>
        @if($category->description)
            <p class="text-gray-600 mt-2">{{$category->description}}</p>
            @endif
</div>

<div class="p-6">
    <h3 class="text-xl font-semibold mb-4">Posts in this category</h3>

    @if($category->posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($category->posts as $post)
                <div class="border rounded-lg p-6 hover:shadow-lg transition">
                    <h4 class="text-lg font-bold text-gray-800 mb-2">
                        <a href="{{ route('posts.public.show', $post->slug)}}" class="hover:underline">
                            {{ $post->title}}
</a>
</h4>
<p class="text-gray-600 text-sm mb-4">
    {{ Str::limit($post->content, 100)}}
</p>
<div class="flex space-x-3">
    @auth
        @can('update', $post)
    <a href="{{ route('admin.posts.edit', $post) }}"
    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
        Edit
</a>
@endcan

@can('delete', $post)
<form action="{{ route('admin.posts.destroy', $post)}}" method="POST" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit"
                        class="text-red-600 hover:text-red-800 text-sm font-medium"
                        onclick="return confirm('Delete this post?')">
                        Delete
</button>
</form>
@endcan
@endauth
</div>
</div>
@endforeach
</div>
@else
    <p class="text-gray-500 text-center py-8">No posts yet in this category.</p>
    @endif
</div>

<div class="p-6 border-t flex space-x-4">
    <a href="{{ route('categories.edit', $category) }}"
    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
    Edit category
</a>
<a href="{{ route('categories.index') }}"
    class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
    Back to Categories
</a>
</div>
</div>
@endsection