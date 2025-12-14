@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Posts -->
                <div class="bg-blue-50 p-6 rounded-lg">
                    <div class="text-blue-600 text-sm font-semibold mb-2">Total Posts</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $stats['total_posts'] }}</div>
                </div>

                <!-- Published Posts -->
                <div class="bg-green-50 p-6 rounded-lg">
                    <div class="text-green-600 text-sm font-semibold mb-2">Published</div>
                    <div class="text-3xl font-bold text-gray-800">{{$stats['published_posts']}}</div>
                </div>

                <!-- Draft Posts -->
                <div class="bg-yellow-50 p-6 rounded-lg">
                    <div class="text-yellow-600 text-sm font-semibold mb-2">Drafts</div>
                    <div class="text-3xl font-bold text-gray-800">{{$stats['draft_posts']}}</div>
                </div>

                <!-- Pending Comments -->
                <div class="bg-red-50 p-6 rounded-lg">
                    <div class="text-red-600 text-sm font-semibold mb-2">Pending Comments</div>
                    <div class="text-3xl font-bold text-gray-800">{{$stats['pending_comments']}}</div>
                </div>
            </div>
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Comments -->
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <div class="text-purple-600 text-sm font-semibold mb-2">Total Comments</div>
                        <div class="text-2xl font-bold text-gray-800">{{$stats['total_comments']}}</div>
                    </div>
                    <!-- Categories -->
                    <div class="bg-indigo-50 p-6 rounded-lg">
                        <div class="text-indigo-600 text-sm font-semibold mb-2">Categories</div>
                        <div class="text-2xl font-bold text-gray-800">{{$stats['total_categories']}}</div>
                    </div>
                    <!-- Tags -->
                    <div class="bg-pink-50 p-6 rounded-lg">
                        <div class="text-pink-600 text-sm font-semibold mb-2">Tags</div>
                        <div class="text-2xl font-bold text-gray-800">{{$stats['total_tags']}}</div>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="flex space-x-4">
                        <a href="{{route('posts.create')}}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Create New Post
                        </a>
                        <a href="{{route('categories.create')}}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                            Add Category
                        </a>
                        <a href="{{route('tags.create')}}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                            Add Tag
                        </a>
                    </div>
                </div>
        </div>
    </div>
@endsection

































