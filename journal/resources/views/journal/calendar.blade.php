@extends('layouts.journal')

@section('title', 'Calendar')
@section('breadcrumb', 'Calendar View')

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
                        <span class="font-title-md text-title-md text-on-surface font-sans">12 Consecutive Days</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
                        <span class="font-label-md text-label-md text-secondary font-medium">October Milestone</span>
                    </div>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">24 total entries penned this month</span>
                </div>
            </div>
        </div>

        {{-- Main Split Calendar & Journal Viewer Workspace --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Left: Interactive Monthly Calendar (approx 60% = 7 cols on 12-col grid) --}}
            <div class="lg:col-span-7 flex flex-col gap-6">
                <div class="bg-surface-container-lowest rounded-xl p-7 shadow-[0_4px_20px_-2px_rgba(74,72,68,0.05)] flex flex-col gap-6 relative overflow-hidden">

                    {{-- Month Control Bar --}}
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h2 class="font-headline-md text-headline-md text-on-surface font-serif">October 2023</h2>
                            <div class="flex items-center bg-surface-container rounded-lg p-0.5">
                                <button aria-label="Previous Month" class="p-1.5 hover:bg-surface-container-lowest rounded text-on-surface-variant hover:text-on-surface transition-all" id="prev-month-btn">
                                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                                </button>
                                <button aria-label="Next Month" class="p-1.5 hover:bg-surface-container-lowest rounded text-on-surface-variant hover:text-on-surface transition-all" id="next-month-btn">
                                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <button class="px-3.5 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-title-md text-title-md transition-all text-xs" id="today-btn">
                                Today
                            </button>
                            <div class="relative">
                                <select class="appearance-none bg-surface-container-low hover:bg-surface-container text-on-surface font-label-md text-label-md pl-3 pr-8 py-2 rounded-lg cursor-pointer outline-none transition-colors">
                                    <option selected="" value="10-2023">Oct 2023</option>
                                    <option value="09-2023">Sep 2023</option>
                                    <option value="08-2023">Aug 2023</option>
                                    <option value="07-2023">Jul 2023</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-2 top-2.5 text-[16px] text-on-surface-variant pointer-events-none">expand_more</span>
                            </div>
                        </div>
                    </div>

                    {{-- Days of Week Header --}}
                    <div class="grid grid-cols-7 gap-2 text-center text-on-surface-variant font-label-md text-label-md uppercase tracking-wider py-1">
                        <span>Sun</span>
                        <span>Mon</span>
                        <span>Tue</span>
                        <span>Wed</span>
                        <span>Thu</span>
                        <span>Fri</span>
                        <span>Sat</span>
                    </div>

                    {{-- Days Grid (35 cells) --}}
                    <div class="grid grid-cols-7 gap-2.5" id="calendar-grid">
                        {{-- Previous month tail: Sep 24 to 30 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-09-24" disabled="">
                            <span class="font-label-md text-label-md">24</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-09-25" disabled="">
                            <span class="font-label-md text-label-md">25</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-09-26" disabled="">
                            <span class="font-label-md text-label-md">26</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-09-27" disabled="">
                            <span class="font-label-md text-label-md">27</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-09-28" disabled="">
                            <span class="font-label-md text-label-md">28</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-09-29" disabled="">
                            <span class="font-label-md text-label-md">29</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-09-30" disabled="">
                            <span class="font-label-md text-label-md">30</span>
                        </button>

                        {{-- Oct 1 - Oct 31 --}}
                        {{-- 1: has journal --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low hover:bg-surface-container transition-all group relative text-left" data-date-str="October 1, 2023" data-day="1" data-has-entry="true" data-mood="🌿 Grounded" data-streak="false" data-time="8:20 PM" data-title="New Month, Blank Page" data-words="340">
                            <span class="font-title-md text-title-md text-on-surface group-hover:text-primary">01</span>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🌿</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            </div>
                        </button>

                        {{-- 2: empty --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 2, 2023" data-day="2" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">02</span>
                            <span class="text-[10px] text-outline opacity-40 font-serif italic">Rest</span>
                        </button>

                        {{-- 3: has journal --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low hover:bg-surface-container transition-all group relative text-left" data-date-str="October 3, 2023" data-day="3" data-has-entry="true" data-mood="☕ Inspired" data-streak="false" data-time="4:15 PM" data-title="Coffee Tasting with Elena" data-words="290">
                            <span class="font-title-md text-title-md text-on-surface group-hover:text-primary">03</span>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">☕</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            </div>
                        </button>

                        {{-- 4: empty --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 4, 2023" data-day="4" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">04</span>
                            <span class="text-[10px] text-outline opacity-40 font-serif italic">Empty</span>
                        </button>

                        {{-- 5: has journal --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low hover:bg-surface-container transition-all group relative text-left" data-date-str="October 5, 2023" data-day="5" data-has-entry="true" data-mood="✨ Reflective" data-streak="false" data-time="11:45 PM" data-title="Library Midnight Run" data-words="410">
                            <span class="font-title-md text-title-md text-on-surface group-hover:text-primary">05</span>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">✨</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            </div>
                        </button>

                        {{-- 6: has journal --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low hover:bg-surface-container transition-all group relative text-left" data-date-str="October 6, 2023" data-day="6" data-has-entry="true" data-mood="🏛️ Engaged" data-streak="false" data-time="6:00 PM" data-title="Friday Architecture Tour" data-words="380">
                            <span class="font-title-md text-title-md text-on-surface group-hover:text-primary">06</span>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🏛️</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            </div>
                        </button>

                        {{-- 7: empty --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 7, 2023" data-day="7" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">07</span>
                        </button>

                        {{-- 8: has journal --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low hover:bg-surface-container transition-all group relative text-left" data-date-str="October 8, 2023" data-day="8" data-has-entry="true" data-mood="🥞 Content" data-streak="false" data-time="10:30 AM" data-title="Slow Sunday Pancake Recipe" data-words="220">
                            <span class="font-title-md text-title-md text-on-surface group-hover:text-primary">08</span>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🥞</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            </div>
                        </button>

                        {{-- 9: empty --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 9, 2023" data-day="9" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">09</span>
                        </button>

                        {{-- 10: has journal --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low hover:bg-surface-container transition-all group relative text-left" data-date-str="October 10, 2023" data-day="10" data-has-entry="true" data-mood="💡 Inspired" data-streak="false" data-time="7:10 PM" data-title="Studio Critiques and Coffee" data-words="510">
                            <span class="font-title-md text-title-md text-on-surface group-hover:text-primary">10</span>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">💡</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            </div>
                        </button>

                        {{-- 11: empty --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 11, 2023" data-day="11" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">11</span>
                        </button>

                        {{-- 12: empty --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 12, 2023" data-day="12" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">12</span>
                        </button>

                        {{-- 13 to 24: STREAK DAYS (12 CONSECUTIVE DAYS) --}}
                        {{-- 13: Streak day 1 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 13, 2023" data-day="13" data-has-entry="true" data-mood="🎯 Focused" data-streak="true" data-streak-idx="1" data-time="9:00 PM" data-title="Committing to the Ink" data-words="350">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">13</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🎯</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 14: Streak day 2 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 14, 2023" data-day="14" data-has-entry="true" data-mood="🌧️ Cozy" data-streak="true" data-streak-idx="2" data-time="10:15 PM" data-title="Rain on Stained Glass" data-words="410">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">14</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🌧️</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 15: Streak day 3 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 15, 2023" data-day="15" data-has-entry="true" data-mood="🌿 Peaceful" data-streak="true" data-streak-idx="3" data-time="5:30 PM" data-title="Botanical Garden Sketches" data-words="320">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">15</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🌿</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 16: Streak day 4 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 16, 2023" data-day="16" data-has-entry="true" data-mood="📚 Steady" data-streak="true" data-streak-idx="4" data-time="8:45 PM" data-title="Mid-Term Syllabus Check" data-words="280">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">16</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">📚</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 17: Streak day 5 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 17, 2023" data-day="17" data-has-entry="true" data-mood="🍵 Grateful" data-streak="true" data-streak-idx="5" data-time="9:30 PM" data-title="Conversations Over Mint Tea" data-words="470">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">17</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🍵</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 18: Streak day 6 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 18, 2023" data-day="18" data-has-entry="true" data-mood="✍️ Nostalgic" data-streak="true" data-streak-idx="6" data-time="11:10 PM" data-title="Drafting in Analog" data-words="390">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">18</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">✍️</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 19: Streak day 7 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 19, 2023" data-day="19" data-has-entry="true" data-mood="🍂 Mindful" data-streak="true" data-streak-idx="7" data-time="7:40 PM" data-title="Crisp Winds Across Quad" data-words="440">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">19</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🍂</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 20: Streak day 8 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 20, 2023" data-day="20" data-has-entry="true" data-mood="🌅 Blissful" data-streak="true" data-streak-idx="8" data-time="6:20 PM" data-title="Friday Sunset from 5th Floor" data-words="310">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">20</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🌅</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 21: Streak day 9 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 21, 2023" data-day="21" data-has-entry="true" data-mood="📖 Delighted" data-streak="true" data-streak-idx="9" data-time="8:05 PM" data-title="Flea Market Antique Books" data-words="490">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">21</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">📖</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 22: Streak day 10 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 22, 2023" data-day="22" data-has-entry="true" data-mood="🕯️ Serene" data-streak="true" data-streak-idx="10" data-time="9:50 PM" data-title="Sunday Reset &amp; Scented Candles" data-words="360">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">22</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🕯️</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 23: Streak day 11 --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary/5 hover:bg-primary/10 transition-all group relative text-left" data-date-str="October 23, 2023" data-day="23" data-has-entry="true" data-mood="🖋️ Creative" data-streak="true" data-streak-idx="11" data-time="7:15 PM" data-title="Typography Class Observations" data-words="380">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">23</span>
                                <span class="text-xs">🔥</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">🖋️</span>
                                <span class="w-2 h-2 rounded-full bg-primary shadow-sm"></span>
                            </div>
                        </button>

                        {{-- 24: TODAY & SELECTED (12-day milestone) --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-primary-container/20 shadow-[0_0_0_2px_rgba(154,68,45,0.8)] scale-[1.02] transition-all group relative text-left z-10" data-date-str="October 24, 2023" data-day="24" data-has-entry="true" data-mood="😄 Great mood" data-selected="true" data-streak="true" data-streak-idx="12" data-time="9:15 PM" data-title="The Quiet Magic of Tuesday Evenings" data-words="420">
                            <div class="flex items-center justify-between w-full">
                                <span class="font-title-md text-title-md text-primary font-bold">24</span>
                                <span class="px-1.5 py-0.5 rounded bg-primary text-on-primary font-label-sm text-[10px] uppercase font-sans tracking-wide">Today</span>
                            </div>
                            <div class="w-full flex items-center justify-between">
                                <span class="text-xs">😄</span>
                                <span class="flex h-2 w-2 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                                </span>
                            </div>
                        </button>

                        {{-- 25: Future days --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 25, 2023" data-day="25" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">25</span>
                            <span class="material-symbols-outlined text-[14px] text-outline-variant group-hover:text-primary transition-colors">edit_calendar</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 26, 2023" data-day="26" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">26</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 27, 2023" data-day="27" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">27</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 28, 2023" data-day="28" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">28</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 29, 2023" data-day="29" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">29</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 30, 2023" data-day="30" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">30</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-lowest hover:bg-surface-container-low transition-all text-left group" data-date-str="October 31, 2023" data-day="31" data-has-entry="false">
                            <span class="font-label-md text-label-md text-outline">31</span>
                        </button>

                        {{-- Nov 1 to 4 filler --}}
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-11-01" disabled="">
                            <span class="font-label-md text-label-md">01</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-11-02" disabled="">
                            <span class="font-label-md text-label-md">02</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-11-03" disabled="">
                            <span class="font-label-md text-label-md">03</span>
                        </button>
                        <button class="calendar-day-btn h-20 rounded-xl p-2 flex flex-col justify-between items-start bg-surface-container-low/40 text-outline cursor-not-allowed opacity-50 text-left" data-date="2023-11-04" disabled="">
                            <span class="font-label-md text-label-md">04</span>
                        </button>
                    </div>

                    {{-- Calendar Footer Legend --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 pt-4 mt-2 bg-surface-container-low/50 -mx-7 -mb-7 px-7 py-4">
                        <div class="flex items-center gap-6 text-on-surface-variant font-label-md text-label-md">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-secondary"></span>
                                <span>Journal written</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="flex items-center text-xs">🔥</span>
                                <span class="text-primary font-medium">Active Streak (Oct 13–24)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-surface-container-high"></span>
                                <span>No Entry</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-on-surface-variant text-xs">
                            <span class="material-symbols-outlined text-[15px]">auto_stories</span>
                            <span class="font-serif italic">October Writing Rate: 77%</span>
                        </div>
                    </div>
                </div>

                {{-- Weekly Rhythm Micro-Insight --}}
                <div class="bg-surface-container-low rounded-xl p-5 flex items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-surface-container-highest flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[20px]">insights</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-title-md text-title-md text-on-surface">Evening Writing Ritual</span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">Your favorite time to record reflections is Tuesdays at 9:00 PM.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 text-primary hover:text-on-primary-fixed-variant cursor-pointer font-title-md text-title-md text-sm">
                        <span>Explore Trends</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </div>
                </div>
            </div>

            {{-- Right: Selected Date Journal Detail Panel (approx 40% = 5 cols on 12-col grid) --}}
            <div class="lg:col-span-5 flex flex-col gap-6 sticky top-20">

                {{-- Active Entry State Card --}}
                <div class="bg-surface-container-lowest rounded-xl p-7 shadow-[0_4px_24px_-2px_rgba(74,72,68,0.08)] flex flex-col gap-5 transition-all" id="active-entry-panel">

                    {{-- Top Metadata Header --}}
                    <div class="flex items-center justify-between pb-3">
                        <div class="flex flex-col">
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Viewing Reflection</span>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-serif" id="panel-date">October 24, 2023</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary font-title-md text-xs" id="panel-mood-pill">
                                <span>😄</span>
                                <span id="panel-mood-text">Great mood</span>
                            </span>
                        </div>
                    </div>

                    {{-- Time & Streak Info Banner --}}
                    <div class="flex items-center justify-between bg-surface-container-low px-4 py-2.5 rounded-lg">
                        <div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
                            <span class="material-symbols-outlined text-[16px]">schedule</span>
                            <span id="panel-time">9:15 PM</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-primary font-label-md text-label-md">
                            <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                            <span id="panel-streak-tag">12th Streak Day</span>
                        </div>
                    </div>

                    {{-- Title & Editorial Story Body --}}
                    <div class="flex flex-col gap-3">
                        <h4 class="font-headline-md text-headline-md text-on-surface font-serif leading-snug" id="panel-title">
                            The Quiet Magic of Tuesday Evenings
                        </h4>
                        <div class="font-body-md text-body-md text-on-surface-variant leading-relaxed font-serif flex flex-col gap-3" id="panel-body">
                            <p>
                                Today felt noticeably lighter. After turning in our semester project draft at 3 PM, I walked through the maple trees near the campus library. The autumn light was turning everything a rich, golden amber.
                            </p>
                            <p>
                                I sat down with a hot chamomile tea and sketched out ideas for the weekend. Writing here every day has genuinely changed how I process the noise of university life...
                            </p>
                        </div>
                    </div>

                    {{-- Visual Attachment Inset --}}
                    <div class="relative rounded-lg overflow-hidden bg-surface-container h-44 group" id="panel-attachment">
                        <img alt="Autumn Walk Reflection" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBus9fJEyyf9nRgs0reIAI35aSHMb1xlxsE31ZipKhnL9EcfJNlckpcxVPaNrd_T8t4pX3xq8ensDh2IEY_QqQFrTKQMHWs13HxajRgSjJ041mmMyQfB4WrxTJp1QVi-gVNv5eAiP348hhi7QP8nlmIbUqLG44mUgqnp6LLs7HMu9ypsdI2gqbjYsoVLI7R55cirIKZ5jobSVGvEU1hzyMdVlqQMCQ72X3KaGHeNkMKvsnmt1xwjpvo">
                        <div class="absolute inset-0 bg-gradient-to-t from-on-surface/60 via-transparent to-transparent flex items-end p-3">
                            <span class="text-surface font-label-md text-xs flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">photo_camera</span>
                                <span>Campus Maple Path · 3:42 PM</span>
                            </span>
                        </div>
                    </div>

                    {{-- Tags Pill Strip --}}
                    <div class="flex flex-wrap items-center gap-2 pt-1" id="panel-tags">
                        <span class="px-3 py-1 rounded-full bg-surface-container font-label-md text-label-md text-on-surface-variant hover:bg-surface-container-high transition-colors cursor-pointer">
                            #EveningThoughts
                        </span>
                        <span class="px-3 py-1 rounded-full bg-surface-container font-label-md text-label-md text-on-surface-variant hover:bg-surface-container-high transition-colors cursor-pointer">
                            #Autumn
                        </span>
                        <span class="px-3 py-1 rounded-full bg-surface-container font-label-md text-label-md text-on-surface-variant hover:bg-surface-container-high transition-colors cursor-pointer">
                            #Gratitude
                        </span>
                    </div>

                    {{-- Entry Stats Metadata Bar --}}
                    <div class="flex items-center justify-between py-2 text-on-surface-variant font-label-md text-label-md">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">notes</span>
                                <span id="panel-word-count">420 words</span>
                            </span>
                            <span>·</span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">image</span>
                                <span>1 photo</span>
                            </span>
                        </div>
                        <span class="italic font-serif text-secondary text-xs">Saved to Archival Vault</span>
                    </div>

                    {{-- Action Buttons Bar --}}
                    <div class="grid grid-cols-3 gap-2.5 pt-2">
                        <a href="{{ route('write-journal') }}" class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-lg bg-primary text-on-primary font-title-md text-title-md text-xs hover:bg-primary-container transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                            <span>Edit Entry</span>
                        </a>
                        <button class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-title-md text-title-md text-xs transition-all">
                            <span class="material-symbols-outlined text-[16px]">fullscreen</span>
                            <span>Full Screen</span>
                        </button>
                        <button class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-title-md text-title-md text-xs transition-all">
                            <span class="material-symbols-outlined text-[16px]">ios_share</span>
                            <span>Share</span>
                        </button>
                    </div>
                </div>

                {{-- Empty State Card (Hidden by default, shown when clicking dates without entry) --}}
                <div class="hidden bg-surface-container-lowest rounded-xl p-8 shadow-[0_4px_24px_-2px_rgba(74,72,68,0.06)] flex flex-col items-center text-center gap-5" id="empty-entry-panel">
                    <div class="w-16 h-16 rounded-full bg-surface-container-low flex items-center justify-center text-outline">
                        <span class="material-symbols-outlined text-[32px]">menu_book</span>
                    </div>
                    <div class="flex flex-col gap-2 max-w-sm">
                        <span class="font-label-md text-label-md text-primary uppercase tracking-widest font-sans font-semibold" id="empty-panel-date">October 11, 2023</span>
                        <h3 class="font-headline-md text-headline-md text-on-surface font-serif">A Blank Page</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant font-serif italic">
                            No journal entry recorded for this date. Every day holds quiet moments worth remembering. Would you like to backfill a memory?
                        </p>
                    </div>
                    <div class="flex flex-col w-full gap-2.5 pt-2">
                        <a href="{{ route('write-journal') }}" class="w-full py-3 px-4 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-title-md text-title-md flex items-center justify-center gap-2 transition-all shadow-[0_2px_10px_rgba(154,68,45,0.2)]">
                            <span class="material-symbols-outlined text-[18px]">stylus_note</span>
                            <span>+ Write for this Date</span>
                        </a>
                        <button class="w-full py-2.5 px-4 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface-variant font-title-md text-title-md text-xs transition-colors" id="return-today-btn">
                            Return to Today's Journal (Oct 24)
                        </button>
                    </div>
                </div>

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

@push('scripts')
    <script>
        (function initCalendarLogic() {
            const dayButtons = document.querySelectorAll('.calendar-day-btn');
            const activePanel = document.getElementById('active-entry-panel');
            const emptyPanel = document.getElementById('empty-entry-panel');

            // Panel Elements
            const panelDate = document.getElementById('panel-date');
            const panelMoodPill = document.getElementById('panel-mood-pill');
            const panelMoodText = document.getElementById('panel-mood-text');
            const panelTime = document.getElementById('panel-time');
            const panelStreakTag = document.getElementById('panel-streak-tag');
            const panelTitle = document.getElementById('panel-title');
            const panelWordCount = document.getElementById('panel-word-count');
            const emptyPanelDate = document.getElementById('empty-panel-date');
            const returnTodayBtn = document.getElementById('return-today-btn');
            const todayBtn = document.getElementById('today-btn');

            function clearDaySelections() {
                dayButtons.forEach(btn => {
                    btn.classList.remove('shadow-[0_0_0_2px_rgba(154,68,45,0.8)]', 'scale-[1.02]', 'z-10');
                });
            }

            function selectDate(btn) {
                if (btn.disabled) return;
                clearDaySelections();
                btn.classList.add('shadow-[0_0_0_2px_rgba(154,68,45,0.8)]', 'scale-[1.02]', 'z-10');
                const hasEntry = btn.dataset.hasEntry === 'true';
                const dateStr = btn.dataset.dateStr || 'October 24, 2023';

                if (hasEntry) {
                    emptyPanel.classList.add('hidden');
                    activePanel.classList.remove('hidden');
                    panelDate.textContent = dateStr;
                    panelTitle.textContent = btn.dataset.title || 'The Quiet Magic of Tuesday Evenings';
                    panelTime.textContent = btn.dataset.time || '9:15 PM';
                    panelWordCount.textContent = (btn.dataset.words || '420') + ' words';

                    if (btn.dataset.mood) {
                        const parts = btn.dataset.mood.split(' ');
                        panelMoodPill.querySelector('span:first-child').textContent = parts[0];
                        panelMoodText.textContent = parts.slice(1).join(' ');
                    }

                    if (btn.dataset.streak === 'true') {
                        panelStreakTag.textContent = `${btn.dataset.streakIdx || '12'}th Streak Day`;
                        panelStreakTag.parentElement.classList.remove('hidden');
                    } else {
                        panelStreakTag.parentElement.classList.add('hidden');
                    }
                } else {
                    activePanel.classList.add('hidden');
                    emptyPanel.classList.remove('hidden');
                    emptyPanelDate.textContent = dateStr;
                }
            }
            dayButtons.forEach(btn => {
                btn.addEventListener('click', () => selectDate(btn));
            });
            const day24 = document.querySelector('[data-day="24"]');

            if (returnTodayBtn && day24) {
                returnTodayBtn.addEventListener('click', () => selectDate(day24));
            }

            if (todayBtn && day24) {
                todayBtn.addEventListener('click', () => selectDate(day24));
            }
        })();
    </script>
@endpush
