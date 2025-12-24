@props([
    'post',
    'parentId' => null,
    'buttonText' => 'Post Comment',
    'title' => 'Leave a Comment'
    ])

    @php($errors = $errors ?? new \Illuminate\Support\ViewErrorBag)

    <div {{$attributes->merge(['class' => 'bg-gray-50 p-6 rounded-lg'])}}>
        <h3 class="text-lg font-semibold mb-4">{{$title}}</h3>


            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
    </div>
    @endif 

    <form action="{{ route('comments.store', $post) }}" method="POST">
        @csrf
        @honeypot

        @if($parentId)
            <input type="hidden" name="parent_id" value="{{$parentId}}">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Name <span class="text-red-500">*</span>
</label>

            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
                required
                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name') 
                    <p class="text-red-500 text-sm mt-1">{{$message}}</p>
                    @enderror
</div>

<div> 
    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
        Email <span class="text-red-500">*</span>
</label>
<input 
    type="email"
    name="email"
    id="email"
    value="{{ old('email') }}"
    required
    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
    @error('email')
        <p class="text-red-500 text-sm mt-1">{{$message}}</p>
        @enderror
    </div>
</div>

<div class="mb-4">
    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
        Comment <span class="text-red-500">*</span>
</label>
    <textarea
        name="content"
        id="content"
        rows="4"
        required
        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('content') border-red-500 @enderror"
>{{ old('content') }}</textarea>
    @error('content')
        <p class="text-red-500 text-sm mt-1">{{$message}}</p>
        @enderror
</div>

<button 
    type="submit"
    class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition-colors duration-200">
    {{$buttonText}}
</button>
</form>
</div>


































































































