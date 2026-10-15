{{--
    Layout for signed-out pages (sign in, create account): top header, centered content, footer.

    Sections a page can define:
      title    Browser tab title
      content  The page itself
    Page scripts go in @push('scripts').
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;1,500;1,600&amp;family=Plus+Jakarta+Sans:wght@400;500;600&amp;display=swap" rel="stylesheet">
    @include('partials.head')
</head>
<body class="bg-surface font-body-md text-on-surface antialiased min-h-screen flex flex-col justify-between selection:bg-primary-fixed selection:text-on-primary-fixed">

    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-surface/85 backdrop-blur-xl shadow-[0_1px_8px_rgba(74,72,68,0.04)]">
        <div class="h-16 max-w-7xl mx-auto px-margin-mobile lg:px-margin flex items-center justify-between">
            <div class="flex items-center gap-space-sm">
                <img class="h-8 w-auto object-contain" src="{{ asset('images/logo.jpg') }}" alt="Daily Journal logo">
                <span class="font-headline-md text-headline-md text-on-surface tracking-tight">Daily Journal</span>
            </div>

            <nav class="hidden sm:flex items-center gap-space-lg">
                @foreach (['login' => 'Sign In', 'register' => 'Create Account'] as $routeName => $label)
                    <a href="{{ route($routeName) }}"
                       @if (request()->routeIs($routeName)) aria-current="page" @endif
                       @class([
                           'transition-colors duration-200',
                           'text-primary font-semibold' => request()->routeIs($routeName),
                           'font-label-md text-label-md text-on-surface-variant hover:text-on-surface' => ! request()->routeIs($routeName),
                       ])>{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-space-sm">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full pt-16 flex-1 flex flex-col justify-center bg-surface relative">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="w-full bg-surface-container-low/70 py-space-lg">
        <div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin flex flex-col sm:flex-row items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-xs text-on-surface-variant">
                <span class="material-symbols-outlined text-primary text-[16px]">auto_stories</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">© {{ date('Y') }} Daily Journal. A quiet sanctuary for your thoughts.</span>
            </div>
            <div class="flex items-center gap-space-lg">
                <a class="font-label-md text-label-md text-on-surface-variant hover:text-on-surface transition-colors duration-200" href="#">Privacy Policy</a>
                <a class="font-label-md text-label-md text-on-surface-variant hover:text-on-surface transition-colors duration-200" href="#">Terms of Service</a>
                <a class="font-label-md text-label-md text-on-surface-variant hover:text-on-surface transition-colors duration-200" href="#">Help Sanctuary</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
