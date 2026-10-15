{{--
    Left sidebar: logo, navigation, streak progress and the signed-in user.
    $sidebarStreak and $sidebarGoal are provided by the view composer in AppServiceProvider.
--}}
@php
    $navLinks = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'grid_view'],
        ['route' => 'write-journal', 'label' => 'Write Journal', 'icon' => 'edit_note', 'iconClass' => 'text-primary'],
        ['route' => 'journal-history', 'label' => 'Journal History', 'icon' => 'auto_stories'],
        ['route' => 'calendar', 'label' => 'Calendar', 'icon' => 'calendar_today'],
        ['route' => 'profile.edit', 'label' => 'Profile', 'icon' => 'person'],
    ];

    $logoUrl = 'https://lh3.googleusercontent.com/aida-public/AB6AXuAmBu8B6_zROwjgxi8qiwN_y4XceDX9A6iO5bZBw6o5qfiPIqxcAjYT6Qk0rE4z2UqSAgSv5R_rMQ-qr4XKeilIf918gid4xms8EDstbXJyRiOhxyPrPe7GDd0XEeZtZCgB5TDjp8NZNkLCNTsgQTCdS3HgSR8nAia8LzFVY2MUqINoQWmRnT5HxVCIGG6cfPELuN8OtwWXXcT6gjLBXEml_qC08FmQNaYwQUcpbclrSm0krQvncLDL';

    $daysToGoal = $sidebarGoal - $sidebarStreak;
    $goalPercent = round($sidebarStreak / $sidebarGoal * 100);
@endphp

<aside class="fixed left-0 top-0 h-full w-72 bg-surface-container-low z-50 flex flex-col justify-between py-6 px-5 shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="flex flex-col gap-6">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-2">
            <img class="h-8 w-auto object-contain" src="{{ $logoUrl }}" alt="Daily Journal Logo">
            <div class="flex flex-col">
                <span class="font-headline-sm text-headline-sm text-on-surface tracking-tight">Daily Journal</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant italic font-serif">One day, one story.</span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex flex-col gap-1">
            @foreach ($navLinks as $link)
                @php($isActive = request()->routeIs($link['route']))
                <a href="{{ route($link['route']) }}"
                   @if ($isActive) aria-current="page" @endif
                   @class([
                       'flex items-center gap-3 px-3 py-2.5 rounded-lg font-title-md transition-all',
                       'bg-surface-container-highest text-primary' => $isActive,
                       'text-title-md text-on-surface-variant hover:bg-surface-container hover:text-on-surface' => ! $isActive,
                   ])>
                    <span @class(['material-symbols-outlined text-[20px]', $link['iconClass'] ?? null])>{{ $link['icon'] }}</span>
                    <span>{{ $link['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <div class="flex flex-col gap-4">

        {{-- Streak progress --}}
        <div class="bg-surface-container rounded-xl p-3.5 flex flex-col gap-2.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-primary text-[18px]">local_fire_department</span>
                    <span class="font-title-md text-title-md text-primary">{{ $sidebarStreak }}-day streak</span>
                </div>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Goal: {{ $sidebarGoal }}d</span>
            </div>
            <div class="w-full h-1.5 bg-surface-container-high rounded-full overflow-hidden">
                <div class="h-full bg-primary rounded-full" style="width: {{ $goalPercent }}%"></div>
            </div>
            <span class="font-label-sm text-label-sm text-on-surface-variant italic">
                {{ trans_choice('{1} 1 day left|[2,*] :count days left', $daysToGoal) }} to hit your milestone
            </span>
        </div>

        {{-- Signed-in user --}}
        <div class="flex items-center gap-3 p-2 bg-surface-container-lowest rounded-xl">
            @include('partials.avatar')
            <div class="flex flex-col min-w-0">
                <span class="font-title-md text-title-md text-on-surface truncate">{{ auth()->user()->name }}</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ auth()->user()->email }}</span>
            </div>
            <form class="ml-auto" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors" type="submit" title="Log out" aria-label="Log out">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
