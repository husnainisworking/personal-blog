@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
    <div class="bg-white shadow rounded-lg max-w-2xl">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-800">Create Category</h2>
        </div>

        <form action="{{route('categories.store')}}" method="POST" class="p-6">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                <input type="text" name="name" id="name" value="{{old('name')}}" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500">{{old('description')}}</textarea>
                @error('description')
                <p class="text-red-500 text-sm mt-m1">{{$message}}</p>
                @enderror
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                    Create Category
                </button>
                <a href="{{route('categories.index')}}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection



























