@extends('layouts.journal')

@section('title', 'Journal History')
@section('breadcrumb', 'Journal History')

@php
    // Pill and badge colours per mood, from the original design
    $moodStyles = [
        'great' => ['bg-secondary-container/40 text-on-secondary-container hover:bg-secondary-container', 'bg-secondary/10', 'bg-secondary-container/50 text-on-secondary-container'],
        'happy' => ['bg-tertiary-fixed/40 text-tertiary hover:bg-tertiary-fixed', 'bg-tertiary/10', 'bg-tertiary-fixed/60 text-tertiary'],
        'neutral' => ['bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest', 'bg-surface-variant', 'bg-surface-container-high text-on-surface-variant'],
        'sad' => ['bg-primary-fixed/50 text-on-primary-fixed-variant hover:bg-primary-fixed', 'bg-primary/10', 'bg-primary-fixed/50 text-on-primary-fixed-variant'],
        'angry' => ['bg-error-container/40 text-on-error-container hover:bg-error-container', 'bg-error/10', 'bg-error-container/50 text-on-error-container'],
        'tired' => ['bg-surface-variant text-on-surface-variant hover:bg-surface-dim', 'bg-surface-container-high', 'bg-surface-variant text-on-surface-variant'],
    ];
    $activeMood = $filters['mood'] ?? null;
    $filterUrl = fn (array $changes) => route('journal-history', array_filter(array_merge($filters, $changes)));
@endphp

@section('content')
    <div class="max-w-[1180px] w-full mx-auto flex flex-col gap-8 pb-12">

        {{-- Top Editorial Header & Narrative Metrics --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pt-2">
            <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="font-label-sm text-label-sm uppercase tracking-widest text-primary font-semibold">Personal Archive</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Journal History</h1>
                <p class="font-body-md text-body-md text-on-surface-variant font-serif italic">
                    @if ($firstDate)
                        {{ $totalStories }} {{ Str::plural('story', $totalStories) }} written since {{ $firstDate->format('F Y') }} · A mindful sanctuary of thoughts, milestones, and quiet days.
                    @else
                        Your archive is empty for now · Every story you publish will be kept here.
                    @endif
                </p>
            </div>

            {{-- Quick Summary Metric Chips --}}
            <div class="flex items-center gap-3 bg-surface-container-low p-2 rounded-2xl shadow-sm self-start md:self-auto">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-surface-container-lowest shadow-sm">
                    <span class="material-symbols-outlined text-primary text-[18px]">menu_book</span>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-on-surface-variant leading-none">Total Words</span>
                        <span class="font-title-md text-title-md text-on-surface leading-tight font-serif font-bold">{{ number_format($totalWords) }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-surface-container-lowest shadow-sm">
                    <span class="material-symbols-outlined text-secondary text-[18px]">calendar_month</span>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-on-surface-variant leading-none">{{ today()->format('F') }} Logs</span>
                        <span class="font-title-md text-title-md text-on-surface leading-tight font-serif font-bold">{{ $entriesThisMonth }} {{ Str::plural('entry', $entriesThisMonth) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="px-4 py-3 rounded-xl bg-secondary-container/50 text-on-secondary-container font-body-md text-body-md" role="status">{{ session('status') }}</div>
        @endif

        {{-- Search & Command Filtering Bar --}}
        <form method="GET" action="{{ route('journal-history') }}" class="flex flex-col gap-5 bg-surface-container-lowest p-6 rounded-2xl shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05),0_1px_3px_0_rgba(74,72,68,0.03)]">
            @if ($activeMood)
                <input type="hidden" name="mood" value="{{ $activeMood }}">
            @endif

            {{-- Search Row --}}
            <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                    <input name="q" value="{{ $filters['q'] ?? '' }}" class="w-full pl-12 pr-4 py-3 bg-surface-container-low text-on-surface rounded-xl placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Search entries by title, keyword, or reflections..." type="search" aria-label="Search entries">
                </div>
                <div class="flex items-center gap-3 overflow-x-auto pb-1 lg:pb-0">
                    {{-- Month Filter --}}
                    <label class="flex items-center gap-2 px-4 py-2.5 bg-surface-container-low hover:bg-surface-container text-on-surface rounded-xl font-title-md text-title-md whitespace-nowrap transition-colors">
                        <span class="material-symbols-outlined text-outline text-[18px]">calendar_today</span>
                        <select name="month" onchange="this.form.submit()" class="bg-transparent border-0 p-0 pr-6 focus:ring-0 font-title-md text-title-md" aria-label="Filter by month">
                            <option value="">All Months</option>
                            @foreach ($months as $month)
                                <option value="{{ $month }}" @selected(($filters['month'] ?? null) === $month)>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</option>
                            @endforeach
                        </select>
                    </label>

                    {{-- Sort Order --}}
                    <label class="flex items-center gap-2 px-4 py-2.5 bg-surface-container-low hover:bg-surface-container text-on-surface rounded-xl font-title-md text-title-md whitespace-nowrap transition-colors">
                        <span class="material-symbols-outlined text-outline text-[18px]">sort</span>
                        <select name="sort" onchange="this.form.submit()" class="bg-transparent border-0 p-0 pr-6 focus:ring-0 font-title-md text-title-md" aria-label="Sort order">
                            <option value="newest">Sort: Newest First</option>
                            <option value="oldest" @selected(($filters['sort'] ?? null) === 'oldest')>Sort: Oldest First</option>
                        </select>
                    </label>

                    {{-- View Toggle --}}
                    <div class="flex items-center bg-surface-container-low p-1 rounded-xl">
                        <button type="button" class="p-1.5 rounded-lg bg-surface-container-lowest text-primary shadow-sm hover:text-on-surface transition-all" id="grid-view-btn" title="Grid View">
                            <span class="material-symbols-outlined text-[20px]">grid_view</span>
                        </button>
                        <button type="button" class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface transition-all" id="list-view-btn" title="List View">
                            <span class="material-symbols-outlined text-[20px]">view_agenda</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Mood Pill Selectors --}}
            <div class="flex items-center gap-2 overflow-x-auto pt-1 no-scrollbar">
                <span class="font-label-md text-label-md text-on-surface-variant mr-1 uppercase tracking-wider text-[11px] font-semibold">Moods:</span>
                <a href="{{ $filterUrl(['mood' => null]) }}" @class([
                    'px-3.5 py-1.5 rounded-full font-label-md text-label-md transition-all flex items-center gap-1.5',
                    'bg-primary text-on-primary shadow-sm' => ! $activeMood,
                    'bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest' => $activeMood,
                ])>
                    <span>All</span>
                    <span class="bg-surface-container-lowest/20 px-1.5 py-0.5 rounded-full text-[10px]">{{ $allCount }}</span>
                </a>
                @foreach ($moods as $mood)
                    <a href="{{ $filterUrl(['mood' => $mood->value]) }}" @class([
                        'px-3.5 py-1.5 rounded-full font-label-md text-label-md transition-all flex items-center gap-1.5',
                        'bg-primary text-on-primary shadow-sm' => $activeMood === $mood->value,
                        $moodStyles[$mood->value][0] => $activeMood !== $mood->value,
                    ])>
                        <span>{{ $mood->emoji() }} {{ $mood->label() }}</span>
                        <span class="{{ $moodStyles[$mood->value][1] }} px-1.5 py-0.5 rounded-full text-[10px] font-bold">{{ $moodCounts[$mood->value] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>
        </form>

        {{-- Journal Entries Grid --}}
        @if ($entries->isEmpty())
            <div class="flex flex-col items-center gap-3 py-16 text-center bg-surface-container-lowest rounded-2xl">
                <span class="material-symbols-outlined text-outline text-[40px]">auto_stories</span>
                <p class="font-headline-sm text-headline-sm text-on-surface">No entries found</p>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    @if (array_filter($filters))
                        Try a different search, mood or month. <a href="{{ route('journal-history') }}" class="text-primary underline">Clear filters</a>
                    @else
                        <a href="{{ route('write-journal') }}" class="text-primary underline">Write your first story</a> and it will appear here.
                    @endif
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="journal-grid">
                @foreach ($entries as $entry)
                    <article class="group relative flex flex-col justify-between bg-surface-container-lowest rounded-[18px] p-6 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05),0_1px_3px_0_rgba(74,72,68,0.03)] hover:shadow-[0_12px_32px_-4px_rgba(74,72,68,0.08),0_2px_6px_0_rgba(74,72,68,0.04)] hover:-translate-y-1 transition-all duration-300">
                        <div class="flex flex-col gap-4">

                            {{-- Meta Header --}}
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $entry->entry_date->format('M j, Y') }}</span>
                                    @if ($entry->isDraft())
                                        <span class="px-2 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container-high text-on-surface-variant">Draft</span>
                                    @endif
                                </div>
                                @if ($entry->mood)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-label-sm font-label-sm {{ $moodStyles[$entry->mood->value][2] }}">
                                        <span>{{ $entry->mood->emoji() }}</span>
                                        <span class="font-medium">{{ $entry->mood->label() }}</span>
                                    </span>
                                @endif
                            </div>

                            {{-- Title & Teaser --}}
                            <div class="flex flex-col gap-2">
                                <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors leading-snug">{{ $entry->title ?: 'Untitled entry' }}</h3>
                                <p class="font-body-md text-body-md text-on-surface-variant line-clamp-3 leading-relaxed">{{ $entry->excerpt(220) }}</p>
                            </div>

                            {{-- Tags --}}
                            @if ($entry->tags)
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    @foreach ($entry->tags as $tag)
                                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex items-center justify-between pt-5 mt-4 bg-surface-container-lowest">
                            <div class="flex items-center gap-1.5 text-on-surface-variant">
                                <span class="material-symbols-outlined text-[16px] text-outline">description</span>
                                <span class="font-label-sm text-label-sm">{{ number_format($entry->word_count) }} {{ Str::plural('word', $entry->word_count) }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('write-journal', ['date' => $entry->entry_date->toDateString()]) }}" class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-lg transition-colors flex items-center justify-center" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                                <form method="POST" action="{{ route('journal.destroy', $entry->id) }}" onsubmit="return confirm('Delete this entry and its photos? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/40 rounded-lg transition-colors" title="Delete">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        {{-- Editorial Quote & Inspiration Interstitial --}}
        <div class="my-3 p-6 rounded-2xl bg-surface-container-low flex flex-col sm:flex-row items-center justify-between gap-6 shadow-sm">
            <div class="flex items-start gap-4">
                <span class="material-symbols-outlined text-primary text-[28px] mt-0.5">auto_stories</span>
                <div class="flex flex-col">
                    <p class="font-headline-sm text-headline-sm text-on-surface italic font-serif leading-relaxed">
                        “In the journal, I do not just express myself more openly than I could to any person; I create myself.”
                    </p>
                    <span class="font-label-sm text-label-sm text-on-surface-variant mt-1">— Susan Sontag, Reborn: Journals and Notebooks</span>
                </div>
            </div>
        </div>

        {{-- Pagination & Progress Bottom Bar --}}
        @if ($entries->total() > 0)
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t-0">
                <div class="flex items-center gap-3">
                    <span class="font-body-sm text-body-sm text-on-surface-variant">Showing <strong class="text-on-surface font-semibold">{{ $entries->lastItem() }}</strong> of <strong class="text-on-surface font-semibold">{{ $entries->total() }}</strong> {{ Str::plural('entry', $entries->total()) }}</span>
                    <div class="w-32 h-1.5 bg-surface-container-high rounded-full overflow-hidden hidden sm:block">
                        <div class="h-full bg-primary/70 rounded-full" style="width: {{ round($entries->lastItem() / $entries->total() * 100, 1) }}%;"></div>
                    </div>
                </div>

                @if ($entries->hasPages())
                    <nav aria-label="Pagination" class="flex items-center gap-1.5">
                        @if ($entries->onFirstPage())
                            <span class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-on-surface-variant font-title-md text-title-md opacity-40">
                                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                                <span>Previous</span>
                            </span>
                        @else
                            <a href="{{ $entries->previousPageUrl() }}" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-on-surface hover:bg-surface-container font-title-md text-title-md transition-colors">
                                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                                <span>Previous</span>
                            </a>
                        @endif

                        <span class="px-3 font-title-md text-title-md text-on-surface-variant">Page {{ $entries->currentPage() }} of {{ $entries->lastPage() }}</span>

                        @if ($entries->hasMorePages())
                            <a href="{{ $entries->nextPageUrl() }}" class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-on-surface hover:bg-surface-container font-title-md text-title-md transition-colors">
                                <span>Next</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        @else
                            <span class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-on-surface-variant font-title-md text-title-md opacity-40">
                                <span>Next</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Simple view toggle interaction
        const gridBtn = document.getElementById('grid-view-btn');
        const listBtn = document.getElementById('list-view-btn');
        const container = document.getElementById('journal-grid');

        if (gridBtn && listBtn && container) {
            listBtn.addEventListener('click', () => {
                container.classList.remove('md:grid-cols-2', 'lg:grid-cols-3');
                container.classList.add('grid-cols-1');
                listBtn.classList.add('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
                listBtn.classList.remove('text-on-surface-variant');
                gridBtn.classList.remove('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
                gridBtn.classList.add('text-on-surface-variant');
            });

            gridBtn.addEventListener('click', () => {
                container.classList.add('md:grid-cols-2', 'lg:grid-cols-3');
                container.classList.remove('grid-cols-1');
                gridBtn.classList.add('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
                gridBtn.classList.remove('text-on-surface-variant');
                listBtn.classList.remove('bg-surface-container-lowest', 'text-primary', 'shadow-sm');
                listBtn.classList.add('text-on-surface-variant');
            });
        }
    </script>
@endpush
