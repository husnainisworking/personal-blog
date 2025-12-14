@extends('layouts.admin')

@section('title', 'Manage Categories')

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="p-6 border-b">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Manage Categories</h2>
            <a href="{{route('categories.create')}}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Add Category
            </a>
        </div>
    </div>

    <div class="p-6">
        @if($categories->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <div class="border rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{$category->name}}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{$category->post_count}}
                        @if($category->description)
                            <p class="text-gray-500 text-sm mb-4">{{$category->description}}</p>
                        @endif
                        <div class="flex space-x-3">
                            <a href="{{route('categories.edit', $category)}}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                Edit
                            </a>
                            <form action="{{route('categories.destroy', $category)}}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium" onclick="return confirm('Delete this category?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No categories yet, Create your first category!</p>
        @endif
    </div>
</div>
@endsection



























