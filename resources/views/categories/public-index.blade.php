@extends('layouts.public')

@section('title', 'Categories')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl sm:text-4xl font-bold text-gray-900 mb-2">Categories</h1>
        <p class="text-gray-600">Browse posts by category</p>
     <x-back-link />
</div>

@if($categories->count() > 0)
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($categories as $category)
            <a href="{{ route('categories.show', $category->slug) }}"
                class="bg-white rounded-lg shadow p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $category->name }}</h2>
                            <span class="text-sm text-gray-500">{{ $category->posts_count }}</span>
</div>
@if($category->description)
    <p class="text-sm text-gray-600 mt-2">{{ $category->description }}</p>
    @endif 
</a>
@endforeach
</div>
@else
    <p class="text-gray-500">No categories found.</p>
    @endif
    @endsection



    