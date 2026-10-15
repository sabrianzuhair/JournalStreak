{{-- Top bar: breadcrumb, today's date and quick actions --}}
<header class="fixed top-0 left-72 right-0 h-16 bg-surface/85 backdrop-blur-xl z-40 px-8 flex items-center justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2">
        <span class="font-label-md text-label-md text-on-surface-variant">Journal Space</span>
        <span class="material-symbols-outlined text-[14px] text-outline">chevron_right</span>
        <span class="font-title-md text-title-md text-on-surface">@yield('breadcrumb')</span>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-5">
        <span class="font-body-sm text-body-sm text-on-surface-variant italic font-serif">{{ now()->format('l, F j, Y') }}</span>

        <a class="flex items-center gap-1.5 px-4 py-2 bg-primary text-on-primary rounded-lg font-title-md text-title-md hover:bg-primary-container hover:text-on-primary transition-all shadow-[0_2px_8px_rgba(154,68,45,0.2)]" href="{{ route('write-journal') }}">
            <span class="material-symbols-outlined text-[18px]">stylus_note</span>
            <span>New Entry</span>
        </a>

        {{-- Notifications (not wired up yet) --}}
        <button class="relative p-2 text-on-surface-variant hover:text-on-surface transition-colors" type="button">
            <span class="material-symbols-outlined text-[22px]">notifications</span>
            <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full"></span>
        </button>

        <div class="flex items-center pl-2">
            @include('partials.avatar')
        </div>
    </div>
</header>
