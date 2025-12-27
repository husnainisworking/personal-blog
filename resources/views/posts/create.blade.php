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
        <div class="mb-6">
            <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
            <input 
                type="file"
                id="featured_image"
                name="featured_image"
                accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                class="block w-full text-sm file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200
                @error('featured_image') border border-red-500 rounded-md p-1 @enderror"
               /> 
            
            <p  class="text-sm text-gray-500 mt-1">Max 2MB. Formats: JPEG, PNG, GIF, WebP</p>
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
                     <label class="inline-flex items-center gap-2">
                         <input type="checkbox" name="tags[]" value="{{$tag->id}}"
                                {{in_array($tag->id, old('tags', [])) ? 'checked' : ''}}
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                         <span class="text-sm leading-none text-gray-700">{{$tag->name}}</span>
                     </label>
                     @endforeach
             </div>
         </div>

         <div class="flex space-x-4">
             <button type="submit" class="inline-flex items-center justify-center h-10 min-w-[140px] px-5 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                 Create Post
             </button>
             <a href="{{route('posts.index')}}" class="inline-flex items-center justify-center h-10 min-w-[140px] px-5 rounded bg-gray-300 text-gray-700 hover:bg-gray-400">
                 Cancel
             </a>
         </div>
            </div>


        </form>
    </div>
@endsection






















