@extends('layouts.journal')

@section('title', 'Journal History')
@section('breadcrumb', 'Journal History')

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
                <p class="font-body-md text-body-md text-on-surface-variant font-serif italic">83 stories written since August 2023 · A mindful sanctuary of thoughts, milestones, and quiet days.</p>
            </div>

            {{-- Quick Summary Metric Chips --}}
            <div class="flex items-center gap-3 bg-surface-container-low p-2 rounded-2xl shadow-sm self-start md:self-auto">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-surface-container-lowest shadow-sm">
                    <span class="material-symbols-outlined text-primary text-[18px]">menu_book</span>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-on-surface-variant leading-none">Total Words</span>
                        <span class="font-title-md text-title-md text-on-surface leading-tight font-serif font-bold">28,450</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-surface-container-lowest shadow-sm">
                    <span class="material-symbols-outlined text-secondary text-[18px]">calendar_month</span>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-on-surface-variant leading-none">October Logs</span>
                        <span class="font-title-md text-title-md text-on-surface leading-tight font-serif font-bold">24 entries</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Command Filtering Bar --}}
        <div class="flex flex-col gap-5 bg-surface-container-lowest p-6 rounded-2xl shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05),0_1px_3px_0_rgba(74,72,68,0.03)]">
            {{-- Search Row --}}
            <div class="flex flex-col lg:flex-row gap-4 items-stretch lg:items-center">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
                    <input class="w-full pl-12 pr-4 py-3 bg-surface-container-low text-on-surface rounded-xl placeholder:text-on-surface-variant/60 font-body-md text-body-md focus:outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Search entries by title, keyword, or reflections..." type="text">
                </div>
                <div class="flex items-center gap-3 overflow-x-auto pb-1 lg:pb-0">
                    {{-- Date Filter Dropdown --}}
                    <button class="flex items-center gap-2 px-4 py-2.5 bg-surface-container-low hover:bg-surface-container text-on-surface rounded-xl font-title-md text-title-md whitespace-nowrap transition-colors">
                        <span class="material-symbols-outlined text-outline text-[18px]">calendar_today</span>
                        <span>This Month (October 2023)</span>
                        <span class="material-symbols-outlined text-outline text-[18px]">expand_more</span>
                    </button>

                    {{-- Sort Order Dropdown --}}
                    <button class="flex items-center gap-2 px-4 py-2.5 bg-surface-container-low hover:bg-surface-container text-on-surface rounded-xl font-title-md text-title-md whitespace-nowrap transition-colors">
                        <span class="material-symbols-outlined text-outline text-[18px]">sort</span>
                        <span>Sort: Newest First</span>
                        <span class="material-symbols-outlined text-outline text-[18px]">expand_more</span>
                    </button>

                    {{-- View Toggle --}}
                    <div class="flex items-center bg-surface-container-low p-1 rounded-xl">
                        <button class="p-1.5 rounded-lg bg-surface-container-lowest text-primary shadow-sm hover:text-on-surface transition-all" id="grid-view-btn" title="Grid View">
                            <span class="material-symbols-outlined text-[20px]">grid_view</span>
                        </button>
                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-on-surface transition-all" id="list-view-btn" title="List View">
                            <span class="material-symbols-outlined text-[20px]">view_agenda</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Mood Pill Selectors --}}
            <div class="flex items-center gap-2 overflow-x-auto pt-1 no-scrollbar">
                <span class="font-label-md text-label-md text-on-surface-variant mr-1 uppercase tracking-wider text-[11px] font-semibold">Moods:</span>
                <button class="px-3.5 py-1.5 rounded-full font-label-md text-label-md bg-primary text-on-primary shadow-sm transition-all flex items-center gap-1.5">
                    <span>All</span>
                    <span class="bg-surface-container-lowest/20 text-on-primary px-1.5 py-0.5 rounded-full text-[10px]">83</span>
                </button>
                <button class="px-3.5 py-1.5 rounded-full font-label-md text-label-md bg-secondary-container/40 text-on-secondary-container hover:bg-secondary-container transition-all flex items-center gap-1.5">
                    <span>😄 Great</span>
                    <span class="bg-secondary/10 px-1.5 py-0.5 rounded-full text-[10px] font-bold">18</span>
                </button>
                <button class="px-3.5 py-1.5 rounded-full font-label-md text-label-md bg-tertiary-fixed/40 text-tertiary hover:bg-tertiary-fixed transition-all flex items-center gap-1.5">
                    <span>😊 Happy</span>
                    <span class="bg-tertiary/10 px-1.5 py-0.5 rounded-full text-[10px] font-bold">34</span>
                </button>
                <button class="px-3.5 py-1.5 rounded-full font-label-md text-label-md bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest transition-all flex items-center gap-1.5">
                    <span>😐 Neutral</span>
                    <span class="bg-surface-variant px-1.5 py-0.5 rounded-full text-[10px] font-bold">19</span>
                </button>
                <button class="px-3.5 py-1.5 rounded-full font-label-md text-label-md bg-primary-fixed/50 text-on-primary-fixed-variant hover:bg-primary-fixed transition-all flex items-center gap-1.5">
                    <span>😔 Sad</span>
                    <span class="bg-primary/10 px-1.5 py-0.5 rounded-full text-[10px] font-bold">8</span>
                </button>
                <button class="px-3.5 py-1.5 rounded-full font-label-md text-label-md bg-surface-variant text-on-surface-variant hover:bg-surface-dim transition-all flex items-center gap-1.5">
                    <span>😴 Tired</span>
                    <span class="bg-surface-container-high px-1.5 py-0.5 rounded-full text-[10px] font-bold">4</span>
                </button>
            </div>
        </div>

        {{-- Journal Entries Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="journal-grid">

            {{-- Card 1 --}}
            <article class="group relative flex flex-col justify-between bg-surface-container-lowest rounded-[18px] p-6 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05),0_1px_3px_0_rgba(74,72,68,0.03)] hover:shadow-[0_12px_32px_-4px_rgba(74,72,68,0.08),0_2px_6px_0_rgba(74,72,68,0.04)] hover:-translate-y-1 transition-all duration-300">
                <div class="flex flex-col gap-4">

                    {{-- Meta Header --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Oct 24, 2023 · Evening</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-secondary-container/50 text-on-secondary-container">
                            <span>😄</span>
                            <span class="font-medium">Great</span>
                        </span>
                    </div>

                    {{-- Title & Teaser --}}
                    <div class="flex flex-col gap-2">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors leading-snug">The Quiet Magic of Tuesday Evenings</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant line-clamp-3 leading-relaxed">
                            Today felt noticeably lighter. After turning in our semester project draft at 3 PM, I walked through the maple trees near the campus library. The autumn light slanted golden against the weathered sandstone brick, and for the first time in weeks, my shoulders dropped completely.
                        </p>
                    </div>

                    {{-- Tags --}}
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#EveningThoughts</span>
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Autumn</span>
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Gratitude</span>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-between pt-5 mt-4 bg-surface-container-lowest">
                    <div class="flex items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px] text-outline">description</span>
                        <span class="font-label-sm text-label-sm">420 words</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 text-primary hover:bg-primary-fixed/40 rounded-lg transition-colors" style="font-variation-settings: 'FILL' 1;" title="Favorited">
                            <span class="material-symbols-outlined text-[18px]">star</span>
                        </button>
                        <button class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-lg transition-colors" title="Share / Export">
                            <span class="material-symbols-outlined text-[18px]">ios_share</span>
                        </button>
                        <a href="{{ route('write-journal') }}" class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-lg transition-colors flex items-center justify-center" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                        <button class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/40 rounded-lg transition-colors" title="Delete">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>
            </article>

            {{-- Card 2 --}}
            <article class="group relative flex flex-col justify-between bg-surface-container-lowest rounded-[18px] p-6 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05),0_1px_3px_0_rgba(74,72,68,0.03)] hover:shadow-[0_12px_32px_-4px_rgba(74,72,68,0.08),0_2px_6px_0_rgba(74,72,68,0.04)] hover:-translate-y-1 transition-all duration-300">
                <div class="flex flex-col gap-4">

                    {{-- Meta Header --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Oct 23, 2023 · Afternoon</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-tertiary-fixed/60 text-tertiary">
                            <span>😊</span>
                            <span class="font-medium">Happy</span>
                        </span>
                    </div>

                    {{-- Title & Teaser --}}
                    <div class="flex flex-col gap-2">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors leading-snug">Coffee at the Corner Library &amp; Algorithms</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant line-clamp-3 leading-relaxed">
                            Finally understood Dijkstra's algorithm after struggling for three days in the quiet corner of the botanical cafe. Celebrated with an oat latte and fresh cinnamon bun while listening to the rain tap gently on the glass atrium.
                        </p>
                    </div>

                    {{-- Tags --}}
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#CampusLife</span>
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#CSProject</span>
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Milestone</span>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-between pt-5 mt-4 bg-surface-container-lowest">
                    <div class="flex items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px] text-outline">description</span>
                        <span class="font-label-sm text-label-sm">350 words</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container rounded-lg transition-colors" title="Bookmark">
                            <span class="material-symbols-outlined text-[18px]">star</span>
                        </button>
                        <a href="{{ route('write-journal') }}" class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-lg transition-colors flex items-center justify-center" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                        <button class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/40 rounded-lg transition-colors" title="Delete">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>
            </article>

            {{-- Card 3 --}}
            <article class="group relative flex flex-col justify-between bg-surface-container-lowest rounded-[18px] p-6 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05),0_1px_3px_0_rgba(74,72,68,0.03)] hover:shadow-[0_12px_32px_-4px_rgba(74,72,68,0.08),0_2px_6px_0_rgba(74,72,68,0.04)] hover:-translate-y-1 transition-all duration-300">
                <div class="flex flex-col gap-4">

                    {{-- Meta Header --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Oct 22, 2023 · Night</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-surface-container-high text-on-surface-variant">
                            <span>😐</span>
                            <span class="font-medium">Neutral</span>
                        </span>
                    </div>

                    {{-- Title & Teaser --}}
                    <div class="flex flex-col gap-2">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors leading-snug">Autumn Rain and Window Reflections</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant line-clamp-3 leading-relaxed">
                            Walking home through the campus quad in the twilight rain felt nostalgic. Sometimes doing nothing is its own quiet victory. Read twenty pages of Calvino and watched raindrops chase each other down the bedroom pane.
                        </p>
                    </div>

                    {{-- Tags --}}
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Reflections</span>
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Autumn</span>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-between pt-5 mt-4 bg-surface-container-lowest">
                    <div class="flex items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px] text-outline">description</span>
                        <span class="font-label-sm text-label-sm">290 words</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container rounded-lg transition-colors" title="Bookmark">
                            <span class="material-symbols-outlined text-[18px]">star</span>
                        </button>
                        <a href="{{ route('write-journal') }}" class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-lg transition-colors flex items-center justify-center" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                        <button class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/40 rounded-lg transition-colors" title="Delete">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>
            </article>

            {{-- Card 4 --}}
            <article class="group relative flex flex-col justify-between bg-surface-container-lowest rounded-[18px] p-6 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05),0_1px_3px_0_rgba(74,72,68,0.03)] hover:shadow-[0_12px_32px_-4px_rgba(74,72,68,0.08),0_2px_6px_0_rgba(74,72,68,0.04)] hover:-translate-y-1 transition-all duration-300">
                <div class="flex flex-col gap-4">

                    {{-- Meta Header --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Oct 21, 2023 · Morning</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-tertiary-fixed/60 text-tertiary">
                            <span>😊</span>
                            <span class="font-medium">Happy</span>
                        </span>
                    </div>

                    {{-- Title & Teaser --}}
                    <div class="flex flex-col gap-2">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors leading-snug">Morning Run Along the River Trail</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant line-clamp-3 leading-relaxed">
                            Crisp air at 7 AM. The river was completely still, mist floating over the water like woven silk. My lungs burned at first, but mile three was pure clarity. The sun broke through right at the suspension bridge.
                        </p>
                    </div>

                    {{-- Tags --}}
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Habits</span>
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Fitness</span>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-between pt-5 mt-4 bg-surface-container-lowest">
                    <div class="flex items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px] text-outline">description</span>
                        <span class="font-label-sm text-label-sm">310 words</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 text-primary hover:bg-primary-fixed/40 rounded-lg transition-colors" style="font-variation-settings: 'FILL' 1;" title="Favorited">
                            <span class="material-symbols-outlined text-[18px]">star</span>
                        </button>
                        <a href="{{ route('write-journal') }}" class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-lg transition-colors flex items-center justify-center" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                        <button class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/40 rounded-lg transition-colors" title="Delete">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>
            </article>

            {{-- Card 5 --}}
            <article class="group relative flex flex-col justify-between bg-surface-container-lowest rounded-[18px] p-6 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05),0_1px_3px_0_rgba(74,72,68,0.03)] hover:shadow-[0_12px_32px_-4px_rgba(74,72,68,0.08),0_2px_6px_0_rgba(74,72,68,0.04)] hover:-translate-y-1 transition-all duration-300">
                <div class="flex flex-col gap-4">

                    {{-- Meta Header --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Oct 20, 2023 · Evening</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-surface-variant text-on-surface-variant">
                            <span>😴</span>
                            <span class="font-medium">Tired</span>
                        </span>
                    </div>

                    {{-- Title & Teaser --}}
                    <div class="flex flex-col gap-2">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors leading-snug">Late Night at the Architecture Studio</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant line-clamp-3 leading-relaxed">
                            Cardboard cutouts, exacto knives, and third cup of dark roast. Exhausted but there is an undeniable camaraderie in working late together with low jazz music humming in the studio background.
                        </p>
                    </div>

                    {{-- Tags --}}
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Studio</span>
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#LateNight</span>
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Design</span>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-between pt-5 mt-4 bg-surface-container-lowest">
                    <div class="flex items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px] text-outline">description</span>
                        <span class="font-label-sm text-label-sm">215 words</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container rounded-lg transition-colors" title="Bookmark">
                            <span class="material-symbols-outlined text-[18px]">star</span>
                        </button>
                        <a href="{{ route('write-journal') }}" class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-lg transition-colors flex items-center justify-center" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                        <button class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/40 rounded-lg transition-colors" title="Delete">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>
            </article>

            {{-- Card 6 --}}
            <article class="group relative flex flex-col justify-between bg-surface-container-lowest rounded-[18px] p-6 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05),0_1px_3px_0_rgba(74,72,68,0.03)] hover:shadow-[0_12px_32px_-4px_rgba(74,72,68,0.08),0_2px_6px_0_rgba(74,72,68,0.04)] hover:-translate-y-1 transition-all duration-300">
                <div class="flex flex-col gap-4">

                    {{-- Meta Header --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Oct 19, 2023 · Afternoon</span>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-label-sm font-label-sm bg-primary-fixed/50 text-on-primary-fixed-variant">
                            <span>😔</span>
                            <span class="font-medium">Sad</span>
                        </span>
                    </div>

                    {{-- Title & Teaser --}}
                    <div class="flex flex-col gap-2">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface group-hover:text-primary transition-colors leading-snug">Missing Home and Mom's Vegetable Soup</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant line-clamp-3 leading-relaxed">
                            A wave of homesickness hit out of nowhere while grocery shopping. Stood in the produce aisle just looking at root vegetables. Called home for 20 minutes and felt grounded again hearing the clinking tea glasses in our kitchen.
                        </p>
                    </div>

                    {{-- Tags --}}
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Family</span>
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Homesick</span>
                        <span class="px-2.5 py-0.5 rounded-md font-label-sm text-label-sm bg-surface-container text-on-surface-variant">#Grounded</span>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="flex items-center justify-between pt-5 mt-4 bg-surface-container-lowest">
                    <div class="flex items-center gap-1.5 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[16px] text-outline">description</span>
                        <span class="font-label-sm text-label-sm">380 words</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 text-outline hover:text-primary hover:bg-surface-container rounded-lg transition-colors" title="Bookmark">
                            <span class="material-symbols-outlined text-[18px]">star</span>
                        </button>
                        <a href="{{ route('write-journal') }}" class="p-1.5 text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high rounded-lg transition-colors flex items-center justify-center" title="Edit"><span class="material-symbols-outlined text-[18px]">edit</span></a>
                        <button class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/40 rounded-lg transition-colors" title="Delete">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                </div>
            </article>
        </div>

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
            <button class="shrink-0 px-4 py-2 bg-surface-container-lowest hover:bg-surface-container text-primary font-title-md text-title-md rounded-xl shadow-sm transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">history_edu</span>
                <span>Review October Insights</span>
            </button>
        </div>

        {{-- Pagination & Progress Bottom Bar --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t-0">
            <div class="flex items-center gap-3">
                <span class="font-body-sm text-body-sm text-on-surface-variant">Showing <strong class="text-on-surface font-semibold">6</strong> of <strong class="text-on-surface font-semibold">83</strong> stories</span>
                <div class="w-32 h-1.5 bg-surface-container-high rounded-full overflow-hidden hidden sm:block">
                    <div class="h-full bg-primary/70 rounded-full" style="width: 7.2%;"></div>
                </div>
            </div>

            {{-- Pagination Buttons --}}
            <nav aria-label="Pagination" class="flex items-center gap-1.5">
                <button class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container font-title-md text-title-md transition-colors disabled:opacity-40" disabled="">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Previous</span>
                </button>
                <button class="w-9 h-9 rounded-lg bg-primary text-on-primary font-title-md text-title-md flex items-center justify-center shadow-sm">
                    1
                </button>
                <button class="w-9 h-9 rounded-lg text-on-surface-variant hover:bg-surface-container font-title-md text-title-md flex items-center justify-center transition-colors">
                    2
                </button>
                <button class="w-9 h-9 rounded-lg text-on-surface-variant hover:bg-surface-container font-title-md text-title-md flex items-center justify-center transition-colors">
                    3
                </button>
                <span class="px-1 text-on-surface-variant font-label-md">...</span>
                <button class="w-9 h-9 rounded-lg text-on-surface-variant hover:bg-surface-container font-title-md text-title-md flex items-center justify-center transition-colors">
                    14
                </button>
                <button class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-on-surface hover:bg-surface-container font-title-md text-title-md transition-colors">
                    <span>Next</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </button>
            </nav>
        </div>
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
