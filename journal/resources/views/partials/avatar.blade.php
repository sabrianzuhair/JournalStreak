{{--
    The signed-in user's portrait, or their initials when no portrait is uploaded.
    Optional: $size (Tailwind size classes) and $rounded (Tailwind radius class).
--}}
@php
    $user = auth()->user();
    $size ??= 'w-8 h-8 text-[12px]';
    $rounded ??= 'rounded-full';
@endphp

@if ($user->avatarUrl())
    <img class="{{ $size }} {{ $rounded }} object-cover" src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}">
@else
    <span class="{{ $size }} {{ $rounded }} shrink-0 bg-primary-fixed text-primary font-title-md flex items-center justify-center" aria-label="{{ $user->name }}">
        {{ $user->initials() }}
    </span>
@endif
