<header class="flex justify-between bg-gray-100 px-10 py-4 shadow-md">
    <a href="/">
        <img class="mx-auto h-10 w-auto" src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=blue&shade=600" alt=" Your Company">
    </a>
    <div class="flex items-center justify-end gap-4">
        @if (session()->has('user_name') && session()->has('email'))
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-700">
                <div><span class="font-semibold">Name:</span> {{ session('user_name') }}</div>
                <div><span class="font-semibold">Email:</span> {{ session('email') }}</div>
            </div>
        </div>
        <div class="flex gap-2">
            <a class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-md" href="/create-post">
                Post
            </a>
            <form action="/logout" method="GET">
                <button class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-md">
                    Logout
                </button>
            </form>
        </div>
        @else
        <a class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md" href="/login">
            Login
        </a>
        @endif
    </div>
</header>
