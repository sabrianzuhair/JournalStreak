@extends('layouts.journal')

@section('title', 'Write Journal')
@section('breadcrumb', 'Write Journal')

@section('content')
    <form class="w-full max-w-4xl mx-auto px-4 py-8 flex flex-col gap-8" id="journal-form" method="POST" action="{{ route('journal.store') }}" enctype="multipart/form-data">
        @csrf
        <input id="mood-input" name="mood" type="hidden" value="{{ old('mood', $entry->mood?->value) }}">

        {{-- Meta Strip: Date/Time + Streak Pill + Metrics --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pb-2">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container-low text-on-surface">
                    <span class="material-symbols-outlined text-[16px] text-tertiary">calendar_today</span>
                    <input class="bg-transparent text-label-md font-label-md text-on-surface focus:outline-none w-36 cursor-pointer" id="entry-date" name="entry_date" type="date" value="{{ $date->toDateString() }}" max="{{ today()->toDateString() }}">
                    <span class="text-outline-variant text-[12px]">/</span>
                    <span class="text-label-md font-label-md text-on-surface-variant">{{ ($entry->updated_at ?? now())->format('h:i A') }}</span>
                </div>
                <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-primary-fixed/40 text-on-primary-fixed-variant shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-primary" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                    <span class="font-label-sm text-label-sm">
                        @if (! $date->isToday())
                            Writing for {{ $date->format('M j') }} · Past entry
                        @elseif ($entry->exists && ! $entry->isDraft())
                            Day {{ $currentStreak }} streak secured
                        @else
                            Day {{ $currentStreak + 1 }} streak will be secured upon saving
                        @endif
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3 text-on-surface-variant">
                <div class="flex items-center gap-1.5 text-label-md font-label-md bg-surface-container-low px-3 py-1.5 rounded-full">
                    <span class="material-symbols-outlined text-[15px] text-secondary">notes</span>
                    <span id="word-count-badge">{{ $entry->word_count }} words</span>
                    <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                    <span id="read-time-badge">~{{ $entry->readingMinutes() }} min read</span>
                </div>
                <button class="p-1.5 rounded-full hover:bg-surface-container-high transition-colors" title="Zen Mode" type="button">
                    <span class="material-symbols-outlined text-[18px]">fullscreen</span>
                </button>
            </div>
        </div>

        {{-- Main Journal Card --}}
        <div class="relative bg-surface-container-lowest rounded-3xl p-8 md:p-12 shadow-[0_4px_24px_-4px_rgba(74,72,68,0.06),0_1px_4px_0_rgba(74,72,68,0.03)] flex flex-col gap-8 transition-all">

            {{-- Title Input --}}
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">
                    <span>Journal Title</span>
                    <span class="font-serif italic capitalize lowercase text-tertiary">
                        @if (! $entry->exists)
                            New entry
                        @elseif ($entry->isDraft())
                            Reflective draft
                        @else
                            Published
                        @endif
                    </span>
                </div>
                <input class="w-full font-headline-lg text-headline-lg text-on-surface bg-transparent focus:outline-none placeholder:text-outline-variant/60 tracking-tight" id="journal-title" name="title" placeholder="Give today a title..." type="text" value="{{ old('title', $entry->title) }}" maxlength="255">
            </div>

            {{-- Mood Picker Strip --}}
            <div class="flex flex-col gap-3 py-3 px-5 rounded-2xl bg-surface-container-low">
                <div class="flex items-center justify-between">
                    <span class="font-label-md text-label-md text-on-surface-variant">How are you feeling today?</span>
                    <span class="font-label-sm text-label-sm text-secondary font-serif italic" id="selected-mood-desc">{{ $selectedMood?->description() ?? 'Pick the mood that fits today' }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-2" id="mood-picker">
                    @foreach ($moods as $mood)
                        <button type="button" data-mood="{{ $mood->value }}" data-description="{{ $mood->description() }}"
                                @class([
                                    'mood-btn flex items-center gap-2 px-3.5 py-1.5 rounded-full transition-all',
                                    'bg-primary-container/20 text-primary font-title-md text-title-md shadow-sm scale-105' => $selectedMood === $mood,
                                    'bg-surface-container-lowest text-on-surface-variant font-label-md text-label-md hover:bg-surface-container hover:text-on-surface' => $selectedMood !== $mood,
                                ])>
                            <span>{{ $mood->emoji() }}</span>
                            <span>{{ $mood->label() }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Editor toolbar --}}
            <div class="flex items-center justify-between gap-2 px-4 py-2 rounded-xl bg-surface-container-low text-on-surface-variant">
                <div class="flex items-center gap-1 sm:gap-2">
                    <button class="format-btn p-1.5 rounded-lg hover:bg-surface-container-high hover:text-on-surface transition-colors" data-format="bold" title="Bold (Ctrl+B)" type="button" aria-pressed="false">
                        <span class="material-symbols-outlined text-[18px]">format_bold</span>
                    </button>
                    <button class="format-btn p-1.5 rounded-lg hover:bg-surface-container-high hover:text-on-surface transition-colors" data-format="italic" title="Italic (Ctrl+I)" type="button" aria-pressed="false">
                        <span class="material-symbols-outlined text-[18px]">format_italic</span>
                    </button>
                </div>
                <div class="flex items-center gap-1 sm:gap-2">
                    <label class="cursor-pointer flex items-center gap-1 px-2.5 py-1 rounded-lg text-label-md font-label-md hover:bg-surface-container-high hover:text-on-surface transition-colors" for="image-input">
                        <span class="material-symbols-outlined text-[16px] text-tertiary">add_photo_alternate</span>
                        <span class="hidden sm:inline">Add Image</span>
                    </label>
                </div>
            </div>

            {{-- Writing canvas: a rich-text editor; its HTML is copied into the hidden "content" field on save --}}
            <div class="relative flex flex-col">
                <div class="w-full min-h-[330px] bg-transparent font-body-lg text-body-lg text-on-surface focus:outline-none leading-relaxed tracking-normal break-words empty:before:content-[attr(data-placeholder)] empty:before:text-outline-variant/60"
                     id="journal-editor" contenteditable="true" role="textbox" aria-multiline="true" aria-label="Journal entry"
                     data-placeholder="Unfurl your thoughts here without reservation...">{!! $editorHtml !!}</div>
                <input id="content-input" name="content" type="hidden">
            </div>

            {{-- Image moments: saved photos, photos waiting to be uploaded, and the "Attach Moment" tile --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3" id="image-grid" data-max-images="{{ $maxImages }}">
                @foreach ($entry->images as $image)
                    <div class="image-tile relative rounded-2xl overflow-hidden shadow-sm h-32 group" data-saved-image>
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $image->url() }}" alt="Attached moment">
                        <button class="absolute top-2 right-2 w-7 h-7 rounded-full bg-inverse-surface/70 text-inverse-on-surface flex items-center justify-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity"
                                data-remove-saved="{{ $image->id }}" type="button" title="Remove image" aria-label="Remove image">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </button>
                    </div>
                @endforeach

                <label class="flex flex-col justify-center items-center gap-2 rounded-2xl bg-surface-container-low p-4 h-32 text-center cursor-pointer hover:bg-surface-container transition-colors group" id="attach-tile" for="image-input">
                    <div class="w-9 h-9 rounded-full bg-surface-container-lowest flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                    </div>
                    <span class="font-label-md text-label-md text-on-surface">Attach Moment</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Up to {{ $maxImages }} photos · 5 MB each</span>
                </label>
            </div>
            <input class="sr-only" id="image-input" name="images[]" type="file" accept="image/*" multiple>

            {{-- Entry Meta Info (placeholder until weather is supported) --}}
            <div class="pt-2 flex items-center justify-end">
                <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-low text-on-surface-variant font-label-md text-label-md">
                    <span class="material-symbols-outlined text-[16px] text-secondary">partly_cloudy_day</span>
                    <span>Weather: Crisp 16°C</span>
                </div>
            </div>
        </div>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="flex items-center gap-2 px-4 py-3 rounded-xl bg-error-container text-on-error-container font-label-md text-label-md">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Bottom Floating Sticky Save/Publish Bar --}}
        <div class="sticky bottom-4 z-30 bg-surface-container-lowest/90 backdrop-blur-md rounded-2xl p-4 shadow-[0_8px_32px_rgba(35,34,32,0.08)] flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-on-surface-variant font-label-sm text-label-sm">
                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                <span>
                    @if ($entry->updated_at)
                        Last saved {{ $entry->updated_at->diffForHumans() }} · Stored in your vault
                    @else
                        Not saved yet
                    @endif
                </span>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg font-title-md text-title-md text-on-surface-variant hover:bg-surface-container transition-colors flex items-center justify-center">
                    Discard
                </a>
                <button class="px-4 py-2 rounded-lg font-title-md text-title-md bg-surface-container-low text-on-surface hover:bg-surface-container transition-colors" name="action" type="submit" value="draft">
                    Save Draft
                </button>
                <button class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-title-md text-title-md bg-primary text-on-primary hover:bg-primary-container shadow-md shadow-primary/20 transition-all hover:-translate-y-0.5 active:translate-y-0" id="publish-entry-btn" name="action" type="submit" value="publish">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>Save Journal Entry</span>
                </button>
            </div>
        </div>

        {{-- Save Toast Notification --}}
        <div id="save-toast"
             @class([
                 'fixed bottom-24 right-8 z-50 bg-inverse-surface text-inverse-on-surface px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 transition-opacity duration-300',
                 'opacity-100' => session('status'),
                 'opacity-0 pointer-events-none' => ! session('status'),
             ])>
            <span class="material-symbols-outlined text-secondary-fixed text-[20px]">task_alt</span>
            <span class="font-label-md text-label-md">{{ session('status') }}</span>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('journal-form');
            const editor = document.getElementById('journal-editor');
            const contentInput = document.getElementById('content-input');
            const wordCountBadge = document.getElementById('word-count-badge');
            const readTimeBadge = document.getElementById('read-time-badge');
            const toast = document.getElementById('save-toast');

            // ---------- Editor: bold / italic ----------
            const formatButtons = document.querySelectorAll('.format-btn');

            function refreshFormatButtons() {
                formatButtons.forEach(button => {
                    const active = document.queryCommandState(button.dataset.format);
                    button.setAttribute('aria-pressed', active);
                    button.classList.toggle('bg-surface-container-high', active);
                    button.classList.toggle('text-primary', active);
                });
            }

            formatButtons.forEach(button => {
                // mousedown + preventDefault keeps the text selection inside the editor
                button.addEventListener('mousedown', event => event.preventDefault());
                button.addEventListener('click', () => {
                    editor.focus();
                    document.execCommand(button.dataset.format);
                    refreshFormatButtons();
                });
            });

            document.addEventListener('selectionchange', () => {
                if (editor.contains(document.getSelection().anchorNode)) {
                    refreshFormatButtons();
                }
            });

            // Paste as plain text so styles from other websites don't come along
            editor.addEventListener('paste', event => {
                event.preventDefault();
                document.execCommand('insertText', false, event.clipboardData.getData('text/plain'));
            });

            // ---------- Word count ----------
            function updateMetrics() {
                const text = editor.innerText.trim();
                const words = text ? text.split(/\s+/).length : 0;
                wordCountBadge.textContent = `${words} words`;
                readTimeBadge.textContent = `~${Math.max(1, Math.ceil(words / 180))} min read`;
            }

            editor.addEventListener('input', () => {
                // Clear leftover <br>/<div> so the placeholder shows again when the editor is empty
                if (editor.innerText.trim() === '') {
                    editor.innerHTML = '';
                }
                updateMetrics();
            });
            updateMetrics();

            // ---------- Images ----------
            const imageInput = document.getElementById('image-input');
            const imageGrid = document.getElementById('image-grid');
            const attachTile = document.getElementById('attach-tile');
            const maxImages = Number(imageGrid.dataset.maxImages);
            let pendingFiles = new DataTransfer(); // photos chosen but not uploaded yet

            function imageCount() {
                return imageGrid.querySelectorAll('.image-tile').length;
            }

            function refreshAttachTile() {
                attachTile.classList.toggle('hidden', imageCount() >= maxImages);
            }

            function addPendingTile(file) {
                const tile = document.createElement('div');
                tile.className = 'image-tile relative rounded-2xl overflow-hidden shadow-sm h-32 group';
                tile.innerHTML = `
                    <img class="w-full h-full object-cover" alt="New photo">
                    <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-full bg-inverse-surface/70 text-inverse-on-surface font-label-sm text-label-sm">Uploads on save</span>
                    <button class="absolute top-2 right-2 w-7 h-7 rounded-full bg-inverse-surface/70 text-inverse-on-surface flex items-center justify-center" type="button" title="Remove image" aria-label="Remove image">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>`;
                tile.querySelector('img').src = URL.createObjectURL(file);
                tile.querySelector('button').addEventListener('click', () => {
                    const remaining = new DataTransfer();
                    Array.from(pendingFiles.files).filter(f => f !== file).forEach(f => remaining.items.add(f));
                    pendingFiles = remaining;
                    imageInput.files = pendingFiles.files;
                    tile.remove();
                    refreshAttachTile();
                });
                imageGrid.insertBefore(tile, attachTile);
            }

            imageInput.addEventListener('change', () => {
                // Each pick replaces the input's files, so collect them across picks
                const chosen = Array.from(imageInput.files);
                const room = maxImages - imageCount();
                chosen.slice(0, room).forEach(file => {
                    pendingFiles.items.add(file);
                    addPendingTile(file);
                });
                if (chosen.length > room) {
                    alert(`You can attach up to ${maxImages} photos to an entry.`);
                }
                imageInput.files = pendingFiles.files;
                refreshAttachTile();
            });

            // Removing a saved photo marks it for deletion; it is removed when the entry is saved
            imageGrid.querySelectorAll('[data-remove-saved]').forEach(button => {
                button.addEventListener('click', () => {
                    const marker = document.createElement('input');
                    marker.type = 'hidden';
                    marker.name = 'remove_images[]';
                    marker.value = button.dataset.removeSaved;
                    form.appendChild(marker);
                    button.closest('.image-tile').remove();
                    refreshAttachTile();
                });
            });
            refreshAttachTile();

            // ---------- Saving ----------
            form.addEventListener('submit', () => {
                contentInput.value = editor.innerHTML;
            });

            // ---------- Mood picker ----------
            const moodButtons = document.querySelectorAll('.mood-btn');
            const moodInput = document.getElementById('mood-input');
            const moodDesc = document.getElementById('selected-mood-desc');
            const selectedClasses = 'bg-primary-container/20 text-primary font-title-md text-title-md shadow-sm scale-105';
            const idleClasses = 'bg-surface-container-lowest text-on-surface-variant font-label-md text-label-md hover:bg-surface-container hover:text-on-surface';

            moodButtons.forEach(button => {
                button.addEventListener('click', () => {
                    moodButtons.forEach(other => {
                        other.className = 'mood-btn flex items-center gap-2 px-3.5 py-1.5 rounded-full transition-all ' + idleClasses;
                    });
                    button.className = 'mood-btn flex items-center gap-2 px-3.5 py-1.5 rounded-full transition-all ' + selectedClasses;
                    moodInput.value = button.dataset.mood;
                    moodDesc.textContent = button.dataset.description;
                });
            });

            // ---------- Date: switching opens that day's entry ----------
            const dateInput = document.getElementById('entry-date');
            dateInput.addEventListener('change', () => {
                if (dateInput.value) {
                    window.location.href = `{{ route('write-journal') }}?date=${dateInput.value}`;
                }
            });

            // ---------- "Saved" toast ----------
            if (!toast.classList.contains('opacity-0')) {
                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0', 'pointer-events-none');
                }, 3500);
            }
        })();
    </script>
@endpush
