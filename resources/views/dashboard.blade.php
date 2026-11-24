<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard — {{ config('app.name', 'Laravel') }}</title>
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body { font-family: ui-sans-serif, system-ui, sans-serif; }
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] antialiased">
        <div class="min-h-screen">
            <header class="border-b border-gray-200 dark:border-[#3E3E3A]">
                <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                    <a href="{{ url('/') }}" class="text-lg font-medium">{{ config('app.name', 'Laravel') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-sm text-gray-600 dark:text-[#A1A09A] hover:text-gray-900 dark:hover:text-white transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </header>

            <main class="max-w-6xl mx-auto px-6 py-10">
                @if (session('status'))
                    <div class="mb-8 px-4 py-3 bg-green-50 dark:bg-[#0F1D10] border border-green-200 dark:border-[#3E3E3A] rounded-md text-sm text-green-700 dark:text-[#7ED98A]">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="flex items-center gap-4 mb-8">
                    @if (auth()->user()->avatar)
                        <img src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="w-16 h-16 rounded-full border border-gray-200 dark:border-[#3E3E3A]">
                    @else
                        <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-[#3E3E3A] flex items-center justify-center text-xl font-medium">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-medium">Halo, {{ auth()->user()->name }}!</h1>
                        <p class="text-sm text-gray-500 dark:text-[#A1A09A]">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-gray-200 dark:border-[#3E3E3A] p-8">
                    <h2 class="text-lg font-medium mb-6">Informasi Akun</h2>

                    <dl class="divide-y divide-gray-100 dark:divide-[#3E3E3A]">
                        <div class="py-4 flex items-center justify-between gap-4">
                            <dt class="text-sm text-gray-500 dark:text-[#A1A09A]">Nama</dt>
                            <dd class="text-sm font-medium text-right break-all">{{ auth()->user()->name }}</dd>
                        </div>
                        <div class="py-4 flex items-center justify-between gap-4">
                            <dt class="text-sm text-gray-500 dark:text-[#A1A09A]">Email</dt>
                            <dd class="text-sm font-medium text-right break-all">{{ auth()->user()->email }}</dd>
                        </div>
                        <div class="py-4 flex items-center justify-between gap-4">
                            <dt class="text-sm text-gray-500 dark:text-[#A1A09A]">Bergabung</dt>
                            <dd class="text-sm font-medium text-right">{{ auth()->user()->created_at->format('d M Y') }}</dd>
                        </div>
                        <div class="py-4 flex items-center justify-between gap-4">
                            <dt class="text-sm text-gray-500 dark:text-[#A1A09A]">Metode Masuk</dt>
                            <dd class="text-sm font-medium text-right flex items-center gap-2">
                                <span class="w-4 h-4 inline-block align-middle">
                                    <svg viewBox="0 0 48 48" width="100%" height="100%" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M43.611 20.083H42V20H24v8h11.303C33.164 32.516 29.103 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" fill="#FFC107"/>
                                        <path d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z" fill="#FF3D00"/>
                                        <path d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" fill="#4CAF50"/>
                                        <path d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" fill="#1976D2"/>
                                    </svg>
                                </span>
                                Google
                            </dd>
                        </div>
                    </dl>
                </div>
            </main>
        </div>
    </body>
</html>
