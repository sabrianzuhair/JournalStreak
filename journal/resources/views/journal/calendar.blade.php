@extends('layouts.journal')

@section('title', 'Calendar')
@section('breadcrumb', 'Calendar View')

@php
    $dayUrl = fn ($date) => route('calendar', ['month' => $date->format('Y-m'), 'date' => $date->toDateString()]);
    $ordinal = fn (int $n) => $n.(in_array($n % 100, [11, 12, 13]) ? 'th' : (['th', 'st', 'nd', 'rd'][$n % 10] ?? 'th'));
@endphp

@section('content')
    <div class="max-w-[1240px] w-full mx-auto flex flex-col gap-8 pb-16">

        {{-- Top Editorial Header & Streak Bar --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pt-4">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2 text-primary font-label-md text-label-md uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[16px]">history_edu</span>
                    <span>Archival Footprints</span>
                </div>
                <h1 class="font-display-lg text-display-lg text-on-surface tracking-tight font-serif">Calendar View</h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl">
                    Trace your daily writing journey, quiet moments of reflection, and unbroken consistency across time.
                </p>
            </div>

            {{-- Streak Indicator Pill Banner --}}
            <div class="flex items-center gap-4 bg-surface-container-lowest p-2 pr-5 rounded-full shadow-[0_4px_20px_-2px_rgba(74,72,68,0.06)]">
                <div class="flex items-center justify-center w-11 h-11 rounded-full bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-[24px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-title-md text-title-md text-on-surface font-sans">{{ $currentStreak }} Consecutive {{ Str::plural('Day', $currentStreak) }}</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
                        <span class="font-label-md text-label-md text-secondary font-medium">Next: {{ $milestoneName }}</span>
                    </div>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $publishedThisMonth }} total {{ Str::plural('entry', $publishedThisMonth) }} penned in {{ $month->format('F') }}</span>
                </div>
            </div>
        </div>

        {{-- Main Split Calendar & Journal Viewer Workspace --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Left: Monthly Calendar --}}
            <div class="lg:col-span-7 flex flex-col gap-6">
                <div class="bg-surface-container-lowest rounded-xl p-7 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05)] flex flex-col gap-6 relative overflow-hidden">

                    {{-- Month Control Bar --}}
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h2 class="font-headline-md text-headline-md text-on-surface font-serif">{{ $month->format('F Y') }}</h2>
                            <div class="flex items-center bg-surface-container rounded-lg p-0.5">
                                <a href="{{ route('calendar', ['month' => $previousMonth->format('Y-m')]) }}" aria-label="Previous Month" class="p-1.5 hover:bg-surface-container-lowest rounded text-on-surface-variant hover:text-on-surface transition-all">
                                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                                </a>
                                @if ($canGoForward)
                                    <a href="{{ route('calendar', ['month' => $nextMonth->format('Y-m')]) }}" aria-label="Next Month" class="p-1.5 hover:bg-surface-container-lowest rounded text-on-surface-variant hover:text-on-surface transition-all">
                                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                                    </a>
                                @else
                                    <span aria-hidden="true" class="p-1.5 rounded text-outline opacity-40">
                                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('calendar') }}" class="px-3.5 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-title-md text-title-md transition-all text-xs">
                            Today
                        </a>
                    </div>

                    {{-- Days of Week Header --}}
                    <div class="grid grid-cols-7 gap-2 text-center text-on-surface-variant font-label-md text-label-md uppercase tracking-wider py-1">
                        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                            <span>{{ $weekday }}</span>
                        @endforeach
                    </div>

                    {{-- Days Grid --}}
                    <div class="grid grid-cols-7 gap-2.5" id="calendar-grid">
                        @foreach ($days as $day)
                            @php($entry = $day['entry'])
                            @if (! $day['inMonth'])
                                <span class="h-20 rounded-xl p-2 flex flex-col items-start bg-surface-container-low/40 text-outline opacity-50">
                                    <span class="font-label-md text-label-md">{{ $day['date']->format('d') }}</span>
                                </span>
                            @else
                                <a href="{{ $dayUrl($day['date']) }}" data-date="{{ $day['date']->toDateString() }}" @if ($day['isSelected']) aria-current="date" @endif @class([
                                    'h-20 rounded-xl p-2 flex flex-col justify-between items-start transition-all group relative text-left',
                                    'shadow-[0_0_0_2px_rgba(154,68,45,0.8)] scale-[1.02] z-10' => $day['isSelected'],
                                    'bg-primary/5 hover:bg-primary/10' => $day['streakDay'],
                                    'bg-surface-container-low hover:bg-surface-container' => $entry && ! $day['streakDay'],
                                    'bg-surface-container-lowest hover:bg-surface-container-low' => ! $entry,
                                ])>
                                    <div class="flex items-center justify-between w-full">
                                        <span @class([
                                            'font-title-md text-title-md',
                                            'text-primary font-bold' => $day['streakDay'],
                                            'text-on-surface group-hover:text-primary' => $entry && ! $day['streakDay'],
                                            'font-label-md text-label-md text-outline' => ! $entry,
                                        ])>{{ $day['date']->format('d') }}</span>
                                        @if ($day['isToday'])
                                            <span class="px-1.5 py-0.5 rounded bg-primary text-on-primary font-label-sm text-[10px] uppercase font-sans tracking-wide">Today</span>
                                        @elseif ($day['streakDay'])
                                            <span class="text-xs">🔥</span>
                                        @endif
                                    </div>
                                    @if ($entry)
                                        <div class="w-full flex items-center justify-between">
                                            <span class="text-xs">{{ $entry->mood?->emoji() }}</span>
                                            @if ($entry->isDraft())
                                                <span class="w-1.5 h-1.5 rounded-full border border-outline" title="Draft"></span>
                                            @else
                                                <span @class(['rounded-full', 'w-2 h-2 bg-primary shadow-sm' => $day['streakDay'], 'w-1.5 h-1.5 bg-secondary' => ! $day['streakDay']])></span>
                                            @endif
                                        </div>
                                    @elseif (! $day['isFuture'])
                                        <span class="material-symbols-outlined text-[14px] text-outline-variant opacity-0 group-hover:opacity-100 group-hover:text-primary transition-all">edit_calendar</span>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Calendar Footer Legend --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 pt-4 mt-2 bg-surface-container-low/50 -mx-7 -mb-7 px-7 py-4">
                        <div class="flex flex-wrap items-center gap-6 text-on-surface-variant font-label-md text-label-md">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-secondary"></span>
                                <span>Journal written</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full border border-outline"></span>
                                <span>Draft</span>
                            </div>
                            @if ($streakRange)
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center text-xs">🔥</span>
                                    <span class="text-primary font-medium">Active Streak ({{ $streakRange[0]->format('M j') }}–{{ $streakRange[1]->format('M j') }})</span>
                                </div>
                            @endif
                        </div>
                        @if ($writingRate !== null)
                            <div class="flex items-center gap-2 text-on-surface-variant text-xs">
                                <span class="material-symbols-outlined text-[15px]">auto_stories</span>
                                <span class="font-serif italic">{{ $month->format('F') }} Writing Rate: {{ $writingRate }}%</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Weekly Rhythm Micro-Insight --}}
                @if ($favoriteWeekday)
                    <div class="bg-surface-container-low rounded-xl p-5 flex items-center justify-between gap-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-highest flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[20px]">insights</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-title-md text-title-md text-on-surface">Your Writing Rhythm</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">You write most often on {{ Str::plural($favoriteWeekday) }}.</span>
                            </div>
                        </div>
                        <a href="{{ route('journal-history') }}" class="flex items-center gap-1 text-primary hover:text-on-primary-fixed-variant font-title-md text-title-md text-sm">
                            <span>View History</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Right: Selected Date Journal Detail Panel --}}
            <div class="lg:col-span-5 flex flex-col gap-6 sticky top-20">
                @if ($selectedEntry)
                    <div class="bg-surface-container-lowest rounded-xl p-7 shadow-[0_4px_24px_-2px_rgba(74,72,68,0.08)] flex flex-col gap-5 transition-all" id="active-entry-panel">

                        {{-- Top Metadata Header --}}
                        <div class="flex items-center justify-between pb-3">
                            <div class="flex flex-col">
                                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Viewing Reflection</span>
                                <h3 class="font-headline-sm text-headline-sm text-on-surface font-serif">{{ $selected->format('F j, Y') }}</h3>
                            </div>
                            @if ($selectedEntry->mood)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary font-title-md text-xs">
                                    <span>{{ $selectedEntry->mood->emoji() }}</span>
                                    <span>{{ $selectedEntry->mood->label() }} mood</span>
                                </span>
                            @endif
                        </div>

                        {{-- Day & Streak Info Banner --}}
                        <div class="flex items-center justify-between bg-surface-container-low px-4 py-2.5 rounded-lg">
                            <div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                <span>{{ $selected->format('l') }} · {{ $selectedEntry->readingMinutes() }} min read</span>
                            </div>
                            @if ($selectedStreakDay)
                                <div class="flex items-center gap-1.5 text-primary font-label-md text-label-md">
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                                    <span>{{ $ordinal($selectedStreakDay) }} Streak Day</span>
                                </div>
                            @endif
                        </div>

                        {{-- Title & Story Body --}}
                        <div class="flex flex-col gap-3">
                            <h4 class="font-headline-md text-headline-md text-on-surface font-serif leading-snug">{{ $selectedEntry->title ?: 'Untitled entry' }}</h4>
                            <div class="font-body-md text-body-md text-on-surface-variant leading-relaxed font-serif flex flex-col gap-3 max-h-80 overflow-y-auto">
                                {!! \App\Support\RichText::toEditorHtml($selectedEntry->content) !!}
                            </div>
                        </div>

                        {{-- Visual Attachment Inset --}}
                        @if ($photo = $selectedEntry->images->first())
                            <div class="relative rounded-lg overflow-hidden bg-surface-container h-44 group">
                                <img alt="Photo from {{ $selected->format('F j') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $photo->url() }}">
                            </div>
                        @endif

                        {{-- Tags Pill Strip --}}
                        @if ($selectedEntry->tags)
                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                @foreach ($selectedEntry->tags as $tag)
                                    <span class="px-3 py-1 rounded-full bg-surface-container font-label-md text-label-md text-on-surface-variant">#{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Entry Stats Metadata Bar --}}
                        <div class="flex items-center justify-between py-2 text-on-surface-variant font-label-md text-label-md">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">notes</span>
                                    <span>{{ number_format($selectedEntry->word_count) }} {{ Str::plural('word', $selectedEntry->word_count) }}</span>
                                </span>
                                <span>·</span>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">image</span>
                                    <span>{{ $selectedEntry->images->count() }} {{ Str::plural('photo', $selectedEntry->images->count()) }}</span>
                                </span>
                            </div>
                            <span class="italic font-serif text-secondary text-xs">{{ $selectedEntry->isDraft() ? 'Draft · publish to count' : 'Saved to Archival Vault' }}</span>
                        </div>

                        <a href="{{ route('write-journal', ['date' => $selected->toDateString()]) }}" class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-lg bg-primary text-on-primary font-title-md text-title-md text-xs hover:bg-primary-container transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                            <span>Edit Entry</span>
                        </a>
                    </div>
                @else
                    <div class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_4px_24px_-2px_rgba(74,72,68,0.06)] flex flex-col items-center text-center gap-5" id="empty-entry-panel">
                        <div class="w-16 h-16 rounded-full bg-surface-container-low flex items-center justify-center text-outline">
                            <span class="material-symbols-outlined text-[32px]">menu_book</span>
                        </div>
                        <div class="flex flex-col gap-2 max-w-sm">
                            <span class="font-label-md text-label-md text-primary uppercase tracking-widest font-sans font-semibold">{{ $selected->format('F j, Y') }}</span>
                            @if ($selected->isFuture())
                                <h3 class="font-headline-md text-headline-md text-on-surface font-serif">Not Yet Written</h3>
                                <p class="font-body-md text-body-md text-on-surface-variant font-serif italic">This day is still ahead of you. Come back when it arrives.</p>
                            @else
                                <h3 class="font-headline-md text-headline-md text-on-surface font-serif">A Blank Page</h3>
                                <p class="font-body-md text-body-md text-on-surface-variant font-serif italic">
                                    No journal entry recorded for this date. Every day holds quiet moments worth remembering. Would you like to backfill a memory?
                                </p>
                            @endif
                        </div>
                        <div class="flex flex-col w-full gap-2.5 pt-2">
                            @unless ($selected->isFuture())
                                <a href="{{ route('write-journal', ['date' => $selected->toDateString()]) }}" class="w-full py-3 px-4 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-title-md text-title-md flex items-center justify-center gap-2 transition-all shadow-[0_2px_10px_rgba(154,68,45,0.2)]">
                                    <span class="material-symbols-outlined text-[18px]">stylus_note</span>
                                    <span>+ Write for this Date</span>
                                </a>
                            @endunless
                            @unless ($selected->isToday())
                                <a href="{{ route('calendar') }}" class="w-full py-2.5 px-4 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-title-md text-title-md text-xs transition-colors">
                                    Return to Today ({{ today()->format('M j') }})
                                </a>
                            @endunless
                        </div>
                    </div>
                @endif

                {{-- Quote of the Day Micro-Card --}}
                <div class="bg-surface-container-low/70 rounded-xl p-5 flex flex-col gap-2">
                    <div class="flex items-center gap-2 text-tertiary">
                        <span class="material-symbols-outlined text-[18px]">format_quote</span>
                        <span class="font-label-sm text-label-sm uppercase tracking-wider font-sans font-semibold">Prompt Reflection</span>
                    </div>
                    <p class="font-headline-sm text-headline-sm text-on-surface font-serif italic text-sm leading-relaxed">
                        "What quiet ordinary gesture from another soul brought you comfort during today's routine?"
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
