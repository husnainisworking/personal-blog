<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{asset('favicon.ico')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <title>@yield('title', 'Admin') - My Personal Blog </title>
    <script>
    (() => {
        const stored = localStorage.getItem('theme');
        const prefersDark = 
            window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const useDark = stored ? stored === 'dark' : prefersDark;
        document.documentElement.classList.toggle('dark', useDark);
    }) ();
    </script>
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
                        <a href="{{ route('dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-2 py-2 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                        <a href="{{ route('posts.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-2 py-2 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Posts
                        </a>
                        <a href="{{ route('categories.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-2 py-2 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Categories
                        </a>
                        <a href="{{ route('tags.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-2 py-2 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Tags
                        </a>
                        <a href="{{ route('comments.index') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-2 py-2 border-b-2 text-sm font-medium transition duration-150 ease-in-out">
                            Comments
                        </a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <button 
                    type="button"
                    class="inline-flex items-center gap-2 px-2 py-2 text-sm font-medium text-gray-500 hover:text-gray-700"
                    x-data
                    @click="$store.theme.toggle()"
                >
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M21.64 13a1 1 0 0 0-1.05-.14A8 8 0 0 1 11.14 3.4a1 1 0 0 0-1.19-1.19A10 10 0 1 0 22 14.05a1 1 0 0 0-.36-1.05Z"/>
            </svg>
                <span class="theme-label-dark">Dark</span>
                <span class="theme-label-light">Light</span>
            </button>
                    <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        View Site
                    </a>
                    <form class="inline-flex" method="POST" action="{{ route('logout') }}">
                        @csrf
                    <button type="submit" class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
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
































