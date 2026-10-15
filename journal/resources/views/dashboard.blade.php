@extends('layouts.journal')

@section('title', 'Dashboard')
@section('breadcrumb', 'Daily Overview')

@section('content')
    <div class="max-w-[1140px] w-full mx-auto flex flex-col gap-10">
        {{-- Top Greeting & Header Bar --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 relative">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-tertiary-fixed text-tertiary">
                        <span class="material-symbols-outlined text-[18px]">{{ $greetingIcon }}</span>
                    </div>
                    <span class="font-label-md text-label-md text-on-surface-variant font-medium tracking-wide">
                        {{ now()->format('l, F j, Y · g:i A') }}
                    </span>
                    <span class="inline-block w-1 h-1 rounded-full bg-outline-variant"></span>
                    <span class="font-label-sm text-label-sm text-tertiary font-serif italic">
                        “One day, one story.”
                    </span>
                </div>
                <h1 class="font-display-lg text-display-lg text-on-surface tracking-tight mt-1">
                    {{ $greeting }}, {{ $user->name }}
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    Take a quiet pause. Chronicle the thoughts, encounters, and small victories of today.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('write-journal') }}" class="group flex items-center gap-2.5 px-6 py-3.5 bg-primary text-on-primary rounded-xl font-title-md text-title-md shadow-[0_4px_20px_rgba(154,68,45,0.24)] hover:bg-primary-container hover:text-on-primary transition-all duration-300 hover:-translate-y-0.5 active:translate-y-0">
                    <span class="material-symbols-outlined text-[20px] transition-transform duration-300 group-hover:rotate-90">edit_note</span>
                    <span>+ Write Today's Journal</span>
                </a>
            </div>
        </div>

        {{-- Streak Hero Banner & Daily Flow Strip --}}
        <div class="relative overflow-hidden rounded-[20px] bg-surface-container-lowest p-7 md:p-8 shadow-[0_4px_24px_-4px_rgba(74,72,68,0.06)] flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-8">
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-gradient-to-br from-primary-fixed/30 to-tertiary-fixed/20 blur-3xl pointer-events-none"></div>
            {{-- Left Column: Flame & Streak Metric --}}
            <div class="flex items-center gap-5 z-10">
                <div class="relative flex items-center justify-center w-16 h-16 rounded-2xl bg-primary-fixed text-primary shrink-0 shadow-[0_6px_16px_rgba(154,68,45,0.15)]">
                    <span class="material-symbols-outlined text-[34px] animate-pulse" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                    @if ($wroteToday)
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-secondary text-on-secondary flex items-center justify-center text-[10px] font-bold">✓</span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <div class="flex items-baseline gap-2">
                        <span class="font-display-lg text-display-lg text-on-surface font-semibold tracking-tight">{{ $currentStreak }}</span>
                        <span class="font-headline-sm text-headline-sm text-primary font-medium">{{ Str::plural('Day', $currentStreak) }} Writing Streak</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        @if ($currentStreak === 0)
                            Start your streak tonight. One entry is all it takes.
                        @elseif ($wroteToday)
                            {{ trans_choice('{1} 1 day|[2,*] :count days', $currentStreak) }} of unbroken writing. Today's page is written. Well done.
                        @else
                            {{ trans_choice('{1} 1 day|[2,*] :count days', $currentStreak) }} of unbroken writing. Keep your personal chronicle growing tonight.
                        @endif
                    </p>
                </div>
            </div>

            {{-- Center & Right: Last 7 Days Habit Matrix --}}
            <div class="flex flex-col gap-3.5 z-10">
                <div class="flex items-center justify-between gap-2 sm:gap-3">
                    @foreach ($lastSevenDays as $day)
                        <div class="flex flex-col items-center gap-2">
                            @if ($day['isToday'])
                                <span class="font-label-sm text-label-sm text-primary font-bold">Today</span>
                                @if ($day['written'])
                                    <div class="relative w-9 h-9 rounded-xl bg-primary text-on-primary flex items-center justify-center shadow-[0_2px_10px_rgba(154,68,45,0.35)]">
                                        <span class="material-symbols-outlined text-[18px] animate-pulse" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                                    </div>
                                @else
                                    <a href="{{ route('write-journal') }}" title="Write today's entry" class="relative w-9 h-9 rounded-xl border-2 border-dashed border-primary/50 text-primary flex items-center justify-center hover:bg-primary/10 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">local_fire_department</span>
                                    </a>
                                @endif
                                <span class="font-label-sm text-[10px] text-primary font-semibold">{{ $day['date']->format('jS') }}</span>
                            @else
                                <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">{{ $day['date']->format('D')[0] }}</span>
                                @if ($day['written'])
                                    <div class="w-9 h-9 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center shadow-sm">
                                        <span class="material-symbols-outlined text-[18px] font-bold">check</span>
                                    </div>
                                @else
                                    <div class="w-9 h-9 rounded-xl bg-surface-container text-outline-variant flex items-center justify-center" title="No entry">
                                        <span class="material-symbols-outlined text-[18px]">remove</span>
                                    </div>
                                @endif
                                <span class="font-label-sm text-[10px] text-on-surface-variant">{{ $day['date']->format('jS') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between gap-3 pt-2">
                    <div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
                        <span class="material-symbols-outlined text-[16px] text-secondary">eco</span>
                        <span>{{ trans_choice('{1} 1 day|[2,*] :count days', $daysToGoal) }} until <strong class="font-title-md text-on-surface">{{ $milestoneName }} Milestone Badge {{ $milestoneEmoji }}</strong></span>
                    </div>
                    <span class="font-label-sm text-label-sm text-primary font-semibold">{{ $milestonePercent }}% achieved</span>
                </div>
            </div>
        </div>

        {{-- Quick Stats Grid (3 Tactile Cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            {{-- Card 1: Longest Streak --}}
            <div class="relative overflow-hidden rounded-[18px] bg-surface-container-lowest p-6 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.04)] hover:-translate-y-0.5 hover:shadow-[0_12px_28px_-4px_rgba(74,72,68,0.07)] transition-all duration-300 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-label-md text-label-md text-on-surface-variant font-medium">Consistency Record</span>
                    <div class="w-10 h-10 rounded-xl bg-tertiary-fixed/60 text-tertiary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">military_tech</span>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="font-label-sm text-label-sm text-tertiary font-semibold uppercase tracking-wider">Personal Best</span>
                    <div class="flex items-baseline gap-2">
                        <span class="font-headline-lg text-headline-lg text-on-surface font-semibold">{{ $longestStreak }}</span>
                        <span class="font-title-md text-title-md text-on-surface-variant">Consecutive {{ Str::plural('Day', $longestStreak) }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 flex items-center gap-2">
                    <div class="w-full bg-surface-container rounded-full h-1.5 overflow-hidden">
                        <div class="bg-tertiary h-1.5 rounded-full" style="width: {{ $longestStreak ? round($currentStreak / $longestStreak * 100) : 0 }}%"></div>
                    </div>
                    <span class="font-label-sm text-[11px] text-on-surface-variant shrink-0">Current: {{ $currentStreak }}d</span>
                </div>
            </div>

            {{-- Card 2: Total Journals --}}
            <div class="relative overflow-hidden rounded-[18px] bg-surface-container-lowest p-6 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.04)] hover:-translate-y-0.5 hover:shadow-[0_12px_28px_-4px_rgba(74,72,68,0.07)] transition-all duration-300 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-label-md text-label-md text-on-surface-variant font-medium">Archive Volume</span>
                    <div class="w-10 h-10 rounded-xl bg-surface-container text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">auto_stories</span>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="font-label-sm text-label-sm text-primary font-semibold uppercase tracking-wider">Total Written</span>
                    <div class="flex items-baseline gap-2">
                        <span class="font-headline-lg text-headline-lg text-on-surface font-semibold">{{ number_format($totalEntries) }}</span>
                        <span class="font-title-md text-title-md text-on-surface-variant">{{ Str::plural('Reflection', $totalEntries) }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 flex items-center justify-between text-on-surface-variant font-body-sm text-body-sm">
                    <span class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px] text-outline">notes</span>
                        {{ number_format($totalWords) }} words authored
                    </span>
                    <span class="font-label-sm text-label-sm text-secondary font-semibold">+{{ $entriesThisWeek }} {{ Str::plural('entry', $entriesThisWeek) }} this wk</span>
                </div>
            </div>

            {{-- Card 3: Today's Mood --}}
            <div class="relative overflow-hidden rounded-[18px] bg-surface-container-lowest p-6 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.04)] hover:-translate-y-0.5 hover:shadow-[0_12px_28px_-4px_rgba(74,72,68,0.07)] transition-all duration-300 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-label-md text-label-md text-on-surface-variant font-medium">Emotional Balance</span>
                    <div class="w-10 h-10 rounded-xl bg-secondary-container/80 text-secondary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">{{ $todayEntry?->mood?->icon() ?? 'sentiment_satisfied' }}</span>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="font-label-sm text-label-sm text-secondary font-semibold uppercase tracking-wider">Logged Mood</span>
                    <div class="flex items-baseline gap-2">
                        <span class="font-headline-lg text-headline-lg text-on-surface font-semibold">
                            @if ($todayEntry?->mood)
                                {{ $todayEntry->mood->label() }} {{ $todayEntry->mood->emoji() }}
                            @else
                                Not logged yet
                            @endif
                        </span>
                    </div>
                </div>
                <div class="mt-4 pt-4 flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm italic">
                    <span class="material-symbols-outlined text-[15px] text-secondary">psychology_alt</span>
                    <span class="truncate">
                        @if ($todayEntry?->mood)
                            “{{ $todayEntry->title ?: $todayEntry->mood->description() }}”
                        @else
                            Pick a mood when you write today's entry
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Mindful Daily Prompt Interactive Card --}}
        <div class="relative rounded-[20px] bg-surface-container p-6 md:p-8 overflow-hidden shadow-[0_4px_20px_-4px_rgba(74,72,68,0.04)]">
            <div class="flex flex-col lg:flex-row gap-6 items-start lg:items-center justify-between">
                <div class="flex flex-col gap-3 max-w-xl">
                    <div class="flex items-center gap-2 text-primary font-label-md text-label-md font-semibold tracking-wider uppercase">
                        <span class="material-symbols-outlined text-[18px]">lightbulb</span>
                        <span>Today's Mindful Spark</span>
                    </div>
                    <blockquote class="font-headline-md text-headline-md text-on-surface font-medium leading-relaxed font-serif italic">
                        “{{ $prompt[0] }}”
                    </blockquote>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        {{ $prompt[1] }}
                    </p>
                </div>

                {{-- Quick Scribble Input & Action --}}
                <form method="POST" action="{{ route('dashboard.quick-note') }}" class="w-full lg:w-96 flex flex-col gap-3 bg-surface-container-lowest p-4 rounded-2xl shadow-sm">
                    @csrf
                    <textarea class="w-full bg-surface-container-low rounded-xl p-3 text-body-sm text-on-surface placeholder:text-outline-variant focus:outline-none focus:bg-surface-container-lowest focus:ring-1 focus:ring-primary resize-none transition-all" id="quick-prompt-input" name="note" maxlength="2000" required placeholder="Jot down a quick instant thought..." rows="2">{{ old('note') }}</textarea>
                    @error('note')
                        <span class="font-label-sm text-label-sm text-error">{{ $message }}</span>
                    @enderror
                    <div class="flex items-center justify-between pt-1">
                        <span class="font-label-sm text-[11px] text-on-surface-variant italic font-serif">{{ $wroteToday ? 'Adds to today\'s entry' : 'Saves to draft' }}</span>
                        <button class="px-4 py-1.5 rounded-lg bg-surface-container-high hover:bg-primary hover:text-on-primary text-on-surface font-title-md text-[13px] transition-colors flex items-center gap-1.5 {{ session('note_saved') ? 'bg-secondary text-on-secondary' : '' }}" id="quick-prompt-save" type="submit">
                            @if (session('note_saved'))
                                <span class="material-symbols-outlined text-[14px]">check</span>
                                <span>Saved</span>
                            @else
                                <span>Save note</span>
                                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Recent Stories Section Header & 3 Realistic Cards --}}
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="font-headline-md text-headline-md text-on-surface font-serif">Recent Stories</h2>
                    <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">Latest {{ $recentEntries->count() ?: 3 }}</span>
                </div>
                <a class="group flex items-center gap-1.5 font-title-md text-title-md text-primary hover:text-on-primary-container transition-colors" href="{{ route('journal-history') }}">
                    <span>View all in History</span>
                    <span class="material-symbols-outlined text-[18px] transition-transform duration-200 group-hover:translate-x-1">arrow_right_alt</span>
                </a>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @forelse ($recentEntries as $entry)
                    <a href="{{ route('write-journal', ['date' => $entry->entry_date->toDateString()]) }}" class="group rounded-[18px] bg-surface-container-lowest overflow-hidden shadow-[0_4px_20px_-2px_rgba(74,72,68,0.04)] hover:-translate-y-1 hover:shadow-[0_12px_32px_-4px_rgba(74,72,68,0.08)] transition-all duration-300 flex flex-col justify-between">
                        <div class="relative h-44 w-full overflow-hidden bg-surface-container">
                            <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="" src="{{ $entry->images->first()?->url() ?? $storyCovers[$loop->index % count($storyCovers)] }}">
                            @if ($entry->mood)
                                <div class="absolute top-3 left-3 flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface/90 backdrop-blur-md text-on-surface shadow-sm">
                                    <span class="material-symbols-outlined text-[15px] text-tertiary" style="font-variation-settings: 'FILL' 1;">{{ $entry->mood->icon() }}</span>
                                    <span class="font-label-sm text-label-sm font-semibold">{{ $entry->mood->label() }}</span>
                                </div>
                            @endif
                            <div class="absolute bottom-3 right-3 px-2.5 py-1 rounded-lg bg-inverse-surface/80 backdrop-blur-sm text-inverse-on-surface font-label-sm text-label-sm">
                                {{ $entry->dayLabel() }}
                            </div>
                        </div>
                        <div class="p-6 flex flex-col gap-3 flex-1 justify-between">
                            <div class="flex flex-col gap-2">
                                @if ($entry->tags)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach ($entry->tags as $tag)
                                            <span class="px-2 py-0.5 rounded-md bg-surface-container text-on-surface-variant font-label-sm text-[11px] font-medium">#{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <h3 class="font-title-lg text-title-lg text-on-surface group-hover:text-primary transition-colors line-clamp-1 font-serif">
                                    {{ $entry->title ?: 'Untitled reflection' }}
                                </h3>
                                <p class="font-body-md text-body-md text-on-surface-variant line-clamp-3 leading-relaxed">
                                    {{ $entry->excerpt(220) }}
                                </p>
                            </div>
                            <div class="pt-4 flex items-center justify-between text-on-surface-variant font-body-sm text-body-sm">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">schedule</span>
                                    {{ $entry->readingMinutes() }} min read · {{ number_format($entry->word_count) }} words
                                </span>
                                <span class="material-symbols-outlined text-[18px] text-outline group-hover:text-primary group-hover:translate-x-0.5 transition-all">chevron_right</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="lg:col-span-3 flex flex-col items-center justify-center gap-3 rounded-[18px] bg-surface-container-lowest p-10 text-center shadow-[0_4px_20px_-2px_rgba(74,72,68,0.04)]">
                        <div class="w-12 h-12 rounded-full bg-primary-fixed text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[24px]">auto_stories</span>
                        </div>
                        <h3 class="font-title-lg text-title-lg text-on-surface font-serif">Your first story is waiting</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant max-w-md">Published entries will appear here. Write tonight's reflection to start your chronicle.</p>
                        <a href="{{ route('write-journal') }}" class="mt-2 flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl font-title-md text-title-md hover:bg-primary-container transition-colors">
                            <span class="material-symbols-outlined text-[18px]">edit_note</span>
                            <span>Write Today's Journal</span>
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quiet Footnote & Affirmation Banner --}}
        <div class="flex items-center justify-between p-5 rounded-2xl bg-surface-container-low text-on-surface-variant text-body-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-[20px]">bookmark_heart</span>
                <span>
                    @if ($totalEntries)
                        You have chronicled
                        <strong>{{ number_format($totalEntries) }} {{ Str::plural('reflection', $totalEntries) }}</strong>
                        and <strong>{{ number_format($totalWords) }} words</strong> of personal growth.
                    @else
                        Every chronicle starts with a single page.
                    @endif
                    Your future self will cherish these pages.
                </span>
            </div>
            <span class="font-label-sm text-label-sm text-outline-variant font-serif italic hidden sm:inline">Cloth-bound volume #{{ sprintf('%02d', $volumeNumber) }}</span>
        </div>
    </div>
@endsection
