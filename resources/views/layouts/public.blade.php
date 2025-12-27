<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--
    charset="UTF-8" -> Ensures the page supports all characters(Eng, Urdu, emojis, etc.).
    viewport -> Makes the page responsive on mobile devices (scales properly).
    -->
    <title>@yield('title', 'Welcome') - My Personal Blog </title>
    <link rel="alternate" type="application/rss+xml" title="My Personal Blog" href="{{ url('/feed.xml') }}">
    <script>
        (() => {
            // Prevents a flash of light mode before JS loads
            const stored = localStorage.getItem('theme'); // 'dark' | 'light' | null
            const prefersDark = 
                window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const useDark = stored ? stored === 'dark' : prefersDark;
            document.documentElement.classList.toggle('dark', useDark);
        }) ();
        </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 py-3 sm:flex-row sm:justify-between sm:items-center sm:h-16">
                <div class="flex min-w-0 justify-center sm:justify-start">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="text-lg sm:text-2xl font-bold text-indigo-600 truncate max-w-[11rem] sm:max-w-none text-center">
                            My Personal Blog
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{route('home')}}" class="border-transparent text-gray-500 hover:text-gray-700 inline-flex items-center px-1 pt-1 text-sm font-medium">
                            Home
                        </a>
                        <a href="{{ route('public.categories.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 inline-flex items-center px-1 pt-1 text-sm font-medium">
                            Categories
                        </a>
                        <a href="{{ route('public.tags.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 inline-flex items-center px-1 pt-1 text-sm font-medium">
                            Tags
                        </a>
                    </div>
                </div>
                <div class="flex flex-col gap-2 w-full sm:w-auto sm:flex-row sm:items-center">
                    <form action="{{route ('search')}}" method="GET" class="flex w-full sm:w-auto">
                        <input type="text" name="q" placeholder="Search..." class="border rounded-l px-4 py-2 text-sm w-full sm:w-64" value="{{ request('q') }}">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-r text-sm hover:bg-indigo-700 shrink-0">
                            Search
                        </button>
                    </form>
                    <button
                        type="button"
                         class="inline-flex min-w-[6rem] items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        x-data
                        @click="$store.theme.toggle()" 
                    >

                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                         <path d="M21.64 13a1 1 0 0 0-1.05-.14A8 8 0 0 1 11.14 3.4a1 1 0 0 0-1.19-1.19A10 10 0 1 0 22 14.05a1 1 0 0 0-.36-1.05Z"/>
    </svg>
                    
                    <span class="theme-label-dark">Dark</span>
                    <span class="theme-label-light">Light</span>
                    </button>    
                    @auth
                        <a href="{{route('dashboard')}}" class="sm:ml-4 text-gray-500 hover:text-gray-700 text-sm font-medium">
                            Admin
                        </a>
                    @endauth
                    <!--
                    Blade directive:only shows this link if the user is logged in.
                    Displays an Admin link to the dashboard.
                    -->
                </div>
            </div>
        </div>
    </nav>
    <main class="pt-8 pb-6 sm:pt-10 sm:pb-10 flex-1">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
    <footer class="bg-gray-50 border-t mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-5 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-gray-500">
        <p>© {{ date('Y') }} Personal Blog. All rights reserved.</p>

            <a href="{{ url('/feed.xml') }}" class="inline-flex items-center gap-2 hover:text-gray-700" target="_blank" rel="noopener">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 fill-current">
            <path d="M6.18 17.82a2.18 2.18 0 1 1 0-4.36 2.18 2.18 0 0 1 0 4.36Zm-2.18-10v3.27a9.91 9.91 0 0 1 9.91 9.91h3.27C17.18 14.3 10.7 7.82 4 7.82Zm0-5v3.27c9.18 0 16.64 7.46 16.64 16.64H24C24 11.74 14.26 2 4 2Z"/>
        </svg>
        <span>RSS</span>
        </a>
        </div>
    </div>
    </footer>
</body>
</html>




















































