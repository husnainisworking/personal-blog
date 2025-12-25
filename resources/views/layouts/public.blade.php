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
                    </div>
                </div>
                <div class="flex flex-col gap-2 w-full sm:w-auto sm:flex-row sm:items-center">
                    <form action="{{route ('search')}}" method="GET" class="flex w-full sm:w-auto">
                        <input type="text" name="q" placeholder="Search..." class="border rounded-l px-4 py-2 text-sm w-full sm:w-64" value="{{ request('q') }}">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-r text-sm hover:bg-indigo-700 shrink-0">
                            Search
                        </button>
                    </form>
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
    <main class="py-10 flex-1">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-5 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                © {{date('Y')}} Personal Blog. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>




















































