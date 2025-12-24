<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{__('Trashed Comments')}} <!-- __() means translate -->
            </h2>
            <a href="{{ route('comments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Comments
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
            <!--Session('success') means the user has successfully restored a comment -->
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
            <!-- The session('success') is a Laravel session variable that holds a success message after an action like restoring a comment.
              It's displayed in the view using {{ session('success') }} -->
                </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900"> 
           
                        @if($comments->count() > 0)
                        @foreach($comments as $comment)
                        <div class="border-b pb-4 mb-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg">{{ $comment->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $comment->email }}</p>
                                    <p class="text-sm text-gray-500">
                                        @if($comment->post)
                                        Post: <a href="{{ route('posts.public.show', $comment->post) }}" class="text-blue-600 hover:underline">
                                            {{ $comment->post->title }}
                                    </a>
                                    @else
                                    Post: <span class="text-red-500">Deleted</span>
                                    @endif
                                </p>
                                <p class="mt-2">{{ $comment->content }}</p>
                                <p class="text-xs text-gray-400 mt-2">
                                    Deleted at: {{ $comment->deleted_at->diffForHumans() }}
                                    <!-- $comment here is instance of the Comment model representing a trashed comment.-->
                                </p>
                            </div>
                            <div class="flex gap-2 ml-4">
                                <form action="{{ route('comments.restore', $comment->id) }}" method="POST">
                                    @csrf
                                    @honeypot
                                    <!-- route() here generates the URL for restoring the comment. The 'comments.restore' route is defined in the web routes file 
                                     and points to the controller method that handles the restoration. -->
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Restore
                                    </button>
                                </form>
                                <form action="{{ route('comments.force-delete', $comment->id) }}" method="POST" onsubmit="return confirm('Permanently delete this comment? This cannot be undone.');">
                                    @csrf
                                    @honeypot
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Delete Forever
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                        @endforeach

                        <div class="mt-4">
                            {{ $comments->links() }}
                        </div>
                            @else
                            <p>No trashed comments found.</p>
                            @endif
        
            <!-- $comments->links() generates pagination links for the trashed comments. -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <!-- x-app-layout component wraps the entire view, providing a consistent layout across the application.
            It includes common elements like the header and footer, ensuring a uniform look and feel. 
            It comes from the resources/views/layouts directory
            --> 
</x-app-layout>































































