@extends('layouts.admin')
@section('title', 'Create Post')

@section('content')
    <!-- Create Post view form in Admin panel -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-800">Create New Post</h2>
        </div>

        <form action="{{route('posts.store')}}" method="POST" class="p-6" enctype="multipart/form-data">
            @csrf
        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
            <input type="text" name="title" value="{{old('title')}}" required
                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="mb-6">
            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
            <textarea name="excerpt" id="excerpt" rows="2"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{old('excerpt')}}</textarea>
            <p class="text-sm text-gray-500 mt-1">Short description (optional)</p>
        </div>
        <div class="mb-3">
            <label for="featured_image" class="form-label">Featured Image</label>
            <input type="file"
                class="form-control @error('featured_image') is-invalid @enderror"
                id="featured_image"
                name="featured_image"
                accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">

            @error('featured_image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            
            <small class="text-muted">Max 2MB. Formats: JPEG, PNG, GIF, WebP</small>
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
            <textarea name="content" id="content" rows="15" required
                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{old('content')}}</textarea>
            <p class="text-sm text-gray-500 mt-1">Supports Markdown</p>
        </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category_id" id="category_id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{$category->id}}" {{old('category_id') == $category->id ? 'selected' : ''}}>
                                {{$category->name}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="draft" {{old('status') == 'draft' ? 'selected' : ''}}>Draft</option>
                        <option value="published" {{old('status') == 'published' ? 'selected' : ''}}>Published</option>
                    </select>
                </div>


         <div class="mb-6">
             <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
             <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                 @foreach($tags as $tag)
                     <label class="flex items-center">
                         <input type="checkbox" name="tags[]" value="{{$tag->id}}"
                                {{in_array($tag->id, old('tags', [])) ? 'checked' : ''}}
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                         <span class="ml-2 text-sm text-gray-700">{{$tag->name}}</span>
                     </label>
                     @endforeach
             </div>
         </div>

         <div class="flex space-x-4">
             <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
                 Create Post
             </button>
             <a href="{{route('posts.index')}}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                 Cancel
             </a>
         </div>
            </div>


        </form>
    </div>
@endsection






















