{{-- Shared <head> contents. Each layout adds its own font stylesheet before including this. --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title') · Daily Journal</title>

{{-- Icons --}}
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">

{{-- Base styles --}}
<style>
    @layer base {
        html, body { margin: 0; padding: 0; }
        body { overscroll-behavior: none; }
        main > :first-child { margin-top: 0 !important; }
        main > :last-child { margin-bottom: 0 !important; }
    }

    ::-webkit-scrollbar { display: none; }
</style>

@include('partials.tailwind-config')
