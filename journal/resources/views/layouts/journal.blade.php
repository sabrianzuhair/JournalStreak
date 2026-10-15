{{--
    Layout for signed-in pages: fixed sidebar on the left, top bar, and page content.

    Sections a page can define:
      title       Browser tab title
      breadcrumb  Current page name shown in the top bar
      content     The page itself
    Page scripts go in @push('scripts').
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..700;1,400..700&amp;family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&amp;display=swap" rel="stylesheet">
    @include('partials.head')
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface">
    @include('partials.sidebar')

    {{-- Everything right of the sidebar --}}
    <div class="pl-72">
        @include('partials.topbar')

        <main class="relative pt-16 w-full min-h-screen bg-surface px-8 pb-12">
            <div class="flex flex-col w-full">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
