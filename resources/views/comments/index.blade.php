@extends('layouts.admin')
@section('title', 'Manage Comments')
@section('content')
                        <!-- Admin dashboard page for managing comments -->
    <div class="bg-white shadow rounded-lg">
       <div class="p-6 border-b flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ __('Manage Comments')}}
        </h2>
   <a href="{{ route('comments.trashed') }}"
    class="inline-flex items-center px-4 py-2 rounded-md bg-amber-500 text-white font-medium shadow hover:bg-amber-600">
    View Trashed Comments
</a>



    </div>
            <!-- Comments List -->
        <div class="p-6">
            @if($comments->count() > 0)
                <div class="space-y-4">
                    @foreach($comments as $comment)
                        <div class="border rounded-lg p-4 {{$comment->approved ? 'bg-white' : 'bg-yellow-50'}}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="font-semibold text-gray-900">{{$comment->name}}</span>
                                    <span class="text-gray-600 text-sm ml-2">({{ $comment->email }})</span>
                                    @if(!$comment->approved)
                                        <span class="ml-2 bg-yellow-200 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                                    @endif
                                </div>
                                <span class="text-sm text-gray-500">{{$comment->created_at->diffForHumans()}}</span>
                            </div>

                            <p class="text-gray-700 mb-2">{{$comment->content}}</p>

                            <div class="text-sm text-gray-600 mb-3">
                                On post: <a href="{{route('posts.public.show', $comment->post->slug)}}" class="text-indigo-600 hover:underline" target="_blank">{{$comment->post->title}}</a>
                            </div>
                        <!-- Action Buttons -->
                            <div class="flex space-x-3">
                                @if(!$comment->approved)
                                    <form action="{{route('comments.approve', $comment)}}" method="POST" class="inline">
                                        @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Approve
                                    </button>
                                    </form>
                                    @endif
                            <form action="{{route('comments.destroy', $comment)}}" method="POST" class="inline">
                                @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium" onclick="return confirm('Delete this comment?')">
                                Delete
                            </button>
                            </form>
                            </div>
                        </div>
                        @endforeach
                </div>

                <div class="mt-6">
                    {{$comments->links()}}
                </div>
                @else
                    <p class="text-gray-500 text-center py-8">No comments yet.</p>
                @endif
        </div>
    </div>
    @endsection






















