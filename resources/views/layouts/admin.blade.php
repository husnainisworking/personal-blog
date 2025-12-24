<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="{{asset('build/assets/123.jpg')}}">
    <title>@yield('title', 'Admin') - My Personal Blog </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!--
    This is admin layout template. All admin pages extend this so they
    share the same header,nav and styling.
    @yld('title', 'Admin') -> Placeholder for a page title. If a child view
    defines @sectn('title'), it will override; otherwise defaults to "Admin".
-->
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{route('dashboard')}}" class="text-2xl font-bold text-indigo-600">
                            My Personal Blog
                        </a>
                        <!-- Generates the URL for the dashboard route , so in this file, its just HTML + Blade + Tailwind CSS working together to build your admin navbar.
                        Tailwind CSS uses utility classes directly in your HTML. Browser dont understand
                        Tailwind thats why vite includes app.css so these utilities of the tailwind can get converted to css.
                        -->
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-2 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                        <a href="{{ route('posts.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-2 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Posts
                        </a>
                        <a href="{{ route('categories.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-2 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Categories
                        </a>
                        <a href="{{ route('tags.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-2 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Tags
                        </a>
                        <a href="{{ route('comments.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-2 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Comments
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" target="_blank" class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                        View Site
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm font-medium">
                        Logout
                    </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <!--
    This is a flash message block, when you redirect back after an action (like submitting a comment or approving one), laravel stores a message in the session (with('success', 'Comment submitted!')).
    On the next page load, this block checks for that message and displays it in a styled alert box.
    After it's shown once the session clears it.
    -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
                </div>
            @endif
            <!--
            if($errors->any()) Blade conditional: checks if there are any validation errors in the current request.
            Example: if you submit a form without a required field, Laravel's validator will populate $errors.
            @yld('content') is placeholder for child views. whatever is being put in @sectn('content') in a child Blade file will be injected here.
            @forech($errors->all() as $error) , loops through all error messages, example: "title is required", "content must be at least 10 characters".
            -->
    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul>
        @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
        @endforeach
        </ul>
        </div>
   @endif

    @yield('content')
        </div>
    </main>
</body>
</html>
































