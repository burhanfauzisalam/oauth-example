<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Masuk — {{ config('app.name', 'Laravel') }}</title>
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                body { font-family: ui-sans-serif, system-ui, sans-serif; }
            </style>
        @endif
    </head>
    <body class="min-h-screen bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-2">
            {{-- Panel branding --}}
            <div class="relative hidden lg:flex lg:flex-col justify-between overflow-hidden bg-gradient-to-br from-[#1b1b18] via-[#2a1e14] to-[#4a2410] p-12 text-white">
                <div class="pointer-events-none absolute -top-32 -right-32 h-96 w-96 rounded-full bg-[#F53003]/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-40 -left-24 h-96 w-96 rounded-full bg-[#F8B803]/10 blur-3xl"></div>

                <div class="relative flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#F53003] to-[#F8B803] font-bold text-lg">S</div>
                    <span class="text-lg font-semibold tracking-tight">{{ config('app.name', 'Laravel') }}</span>
                </div>

                <div class="relative">
                    <h2 class="text-4xl font-semibold leading-tight tracking-tight">
                        Kelola akunmu<br>
                        dengan <span class="text-[#F8B803]">lebih sederhana</span>.
                    </h2>
                    <p class="mt-4 max-w-md text-white/70 leading-relaxed">
                        Masuk dengan satu klik menggunakan akun Google. Data kamu tersimpan aman, tanpa perlu mengingat kata sandi.
                    </p>

                    <ul class="mt-10 space-y-5">
                        <li class="flex items-start gap-4">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-medium">Login instan</p>
                                <p class="text-sm text-white/60">Tanpa form pendaftaran yang panjang.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-medium">Aman & terpercaya</p>
                                <p class="text-sm text-white/60">Autentikasi dijamin oleh Google.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/15">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </span>
                            <div>
                                <p class="font-medium">Cepat & ringan</p>
                                <p class="text-sm text-white/60">Akses langsung ke dashboard kamu.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <p class="relative text-sm text-white/40">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Seluruh hak cipta dilindungi.
                </p>
            </div>

            {{-- Form login --}}
            <div class="flex min-h-screen items-center justify-center px-6 py-12">
                <div class="w-full max-w-md">
                    <div class="mb-8 flex items-center gap-3 lg:hidden">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-[#F53003] to-[#F8B803] font-bold text-lg text-white">S</div>
                        <span class="text-lg font-semibold tracking-tight">{{ config('app.name', 'Laravel') }}</span>
                    </div>

                    <div class="rounded-2xl border border-gray-200 dark:border-[#3E3E3A] bg-white dark:bg-[#161615] p-8 shadow-sm sm:p-10">
                        <h1 class="text-2xl font-semibold tracking-tight">Selamat datang 👋</h1>
                        <p class="mt-2 text-sm text-gray-500 dark:text-[#A1A09A]">
                            Masuk untuk melanjutkan ke akun Anda.
                        </p>

                        @if (session('error'))
                            <div class="mt-6 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 dark:border-[#3E3E3A] dark:bg-[#1D0002] px-4 py-3 text-sm text-red-700 dark:text-[#FF4433]">
                                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        <a
                            href="{{ route('auth.google.redirect') }}"
                            class="mt-8 flex w-full items-center justify-center gap-3 rounded-xl border border-gray-300 bg-white px-5 py-3.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:shadow focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F53003]/40 dark:border-[#3E3E3A] dark:bg-[#1C1C1A] dark:text-[#EDEDEC] dark:hover:bg-[#242424]"
                        >
                            <svg width="20" height="20" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M43.611 20.083H42V20H24v8h11.303C33.164 32.516 29.103 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z" fill="#FFC107" />
                                <path d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z" fill="#FF3D00" />
                                <path d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z" fill="#4CAF50" />
                                <path d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z" fill="#1976D2" />
                            </svg>
                            Lanjutkan dengan Google
                        </a>

                        <div class="my-6 flex items-center gap-4">
                            <span class="h-px flex-1 bg-gray-200 dark:bg-[#3E3E3A]"></span>
                            <span class="text-xs text-gray-400 dark:text-[#A1A09A]">atau</span>
                            <span class="h-px flex-1 bg-gray-200 dark:bg-[#3E3E3A]"></span>
                        </div>

                        <p class="text-center text-xs leading-relaxed text-gray-400 dark:text-[#A1A09A]">
                            Dengan masuk, Anda menyetujui
                            <a href="#" class="font-medium text-gray-600 underline underline-offset-4 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Ketentuan Layanan</a>
                            dan
                            <a href="#" class="font-medium text-gray-600 underline underline-offset-4 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Kebijakan Privasi</a>.
                        </p>
                    </div>

                    <p class="mt-6 text-center text-xs text-gray-400 dark:text-[#A1A09A] lg:hidden">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
