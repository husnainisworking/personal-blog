@extends('layouts.admin')
@section('title', 'Edit Post')
@section('content')
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-800">Edit Post</h2>
        </div>

        <form action="{{route('posts.update', $post)}}" method="POST" class="p-6">
            @csrf
            @method('PUT')

        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
            <input type="text" name="title" id="title" value="{{old('title',$post->title)}}" required
                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
            <textarea name="excerpt" id="excerpt" rows="2"
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{old('excerpt', $post->excerpt)}}</textarea>
        </div>

        <div class="mb-6">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
            <textarea name="content" id="content" rows="15" required
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{old('content', $post->content)}}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category_id" id="category_id"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}" {{old('category_id', $post->category_id) === $category->id ? 'selected' : ''}}>
                            {{$category->name}}
                        </option>
                        @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select name="status" id="status" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <!-- Building a dropdown menu
                    Each choice has a value(draft or published) that gets saved when the form is submitted.
                    -->
                    <option value="draft" {{old('status', $post->status) == 'draft' ? 'selected' : ''}}>Draft</option>
                    <option value="published" {{old('status', $post->status) == 'published' ? 'selected' : ''}}>Published</option>
                </select>
            </div>
        </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    @foreach($tags as $tag)
                        <label class="flex items-center">
                            <input type="checkbox" name="tags[]" value="{{$tag->id}}"
                                   {{in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'checked' : ''}}
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">{{$tag->name}}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                    Update Post
                </button>
                <a href="{{route('posts.index')}}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection























