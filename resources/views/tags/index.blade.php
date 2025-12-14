@extends('layouts.admin')
@section('title', 'Manage Tags')

@section('content')

    <div class="bg-white shadow rounded-lg">
    <div class="p-6 border-b">
        <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Manage Tags</h2>
            <a href="{{route('tags.create')}}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Add Tag
            </a>
        </div>
    </div>

    <div class="p-6">
        @if($tags->count() > 0)
            <div class="flex flex-wrap gap-4">
                @foreach($tags as $tag)
                    <div class="bg-gray rounded-lg px-4 py-3 flex items-center space-x-3">
                        <div>
                            <span class="font-semibold text-gray-800">#{{$tag->name}}</span>
                            <span class="text-gray-600 text-sm ml-2">({{$tag->posts_count}})</span>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{route('tags.edit', $tag)}}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                                Edit
                            </a>
                            <form action="{{route('tags.destroy', $tag)}}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Delete this tag?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
            </div>
            @else
                <p class="text-gray-500 text-center py-8">No tags yet. Create your first tag!</p>
            @endif
    </div>
    </div>
    @endsection























