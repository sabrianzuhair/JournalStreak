@extends('layouts.journal')

@section('title', 'Profile')
@section('breadcrumb', 'Profile')

@section('content')
    <div class="w-full max-w-[1140px] mx-auto flex flex-col gap-8 pb-16">

        {{-- Page header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pt-2">
            <div class="flex flex-col gap-1.5 max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="font-label-sm text-label-sm tracking-widest uppercase text-tertiary">Archival Chamber</span>
                    <span class="text-outline-variant font-label-sm">•</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Profile &amp; Vault Dossier</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Personal Sanctuary &amp; Profile</h1>
                <p class="font-body-md text-body-md text-on-surface-variant font-serif italic">
                    Manage your chronicler identity, vault privacy credentials, dusk reflection rituals, and tactile archival exports.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface-container-lowest text-on-surface font-title-md text-title-md hover:bg-surface-container-high transition-all shadow-sm" type="button" data-open-persona>
                    <span class="material-symbols-outlined text-[19px] text-on-surface-variant">edit</span>
                    <span>Edit Persona</span>
                </button>
                <a class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-on-primary font-title-md text-title-md hover:bg-primary-container transition-all shadow-[0_4px_16px_rgba(154,68,45,0.22)]" href="{{ route('profile.export') }}">
                    <span class="material-symbols-outlined text-[19px]">archive</span>
                    <span>Export Entire Volume</span>
                </a>
            </div>
        </div>

        {{-- Identity hero --}}
        <section class="bg-surface-container-lowest rounded-2xl p-7 shadow-[0_4px_24px_-2px_rgba(74,72,68,0.06)] relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-gradient-to-br from-primary-fixed/30 to-tertiary-fixed/20 blur-3xl pointer-events-none"></div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 relative z-10">
                {{-- Portrait --}}
                <div class="relative shrink-0">
                    <div class="w-28 h-28 rounded-2xl p-1 bg-surface-container-low shadow-sm">
                        @include('partials.avatar', ['size' => 'w-full h-full text-[32px]', 'rounded' => 'rounded-xl'])
                    </div>
                    @if ($writingPersona)
                        <span class="absolute -bottom-2 -right-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed text-label-sm font-label-sm font-semibold shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
                            Active {{ $writingPersona }}
                        </span>
                    @endif
                </div>

                {{-- Name, handle, affiliation and bio --}}
                <div class="flex flex-col gap-1.5">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h2 class="font-headline-md text-headline-md text-on-surface">{{ $user->name }}</h2>
                        @if ($user->handle)
                            <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high font-label-sm text-label-sm text-on-surface-variant">{{ '@'.$user->handle }}</span>
                        @endif
                        <span class="px-2.5 py-0.5 rounded-full bg-surface-container font-label-sm text-label-sm text-tertiary font-medium">Vol. {{ $volumeNumeral }} Chronicler</span>
                    </div>

                    @if ($user->affiliation)
                        <p class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-tertiary">school</span>
                            {{ $user->affiliation }}
                        </p>
                    @endif

                    <div class="mt-2 pl-3 py-1 bg-surface-container-low/80 rounded-r-xl max-w-xl">
                        @if ($user->bio)
                            <p class="font-body-md text-body-md text-on-surface italic font-serif">“{{ $user->bio }}”</p>
                        @else
                            <button class="font-body-md text-body-md text-on-surface-variant italic font-serif hover:text-primary transition-colors" type="button" data-open-persona>
                                Add a line about yourself: what do you write about?
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- Stat cards --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Total volume --}}
            <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-[0_2px_12px_rgba(74,72,68,0.04)] flex flex-col justify-between hover:-translate-y-0.5 transition-transform duration-200">
                <div class="flex items-center justify-between text-on-surface-variant">
                    <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Total Volume</span>
                    <span class="material-symbols-outlined text-[20px] text-tertiary">menu_book</span>
                </div>
                <div class="mt-4 flex flex-col gap-1">
                    <span class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ number_format($totalEntries) }} {{ Str::plural('Story', $totalEntries) }}</span>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        {{ number_format($totalWords) }} words penned across {{ $weeksWriting }} {{ Str::plural('week', $weeksWriting) }}
                    </p>
                </div>
                <div class="mt-4 pt-3 flex items-center justify-between text-label-sm font-label-sm text-secondary">
                    <span class="inline-flex items-center gap-1 font-semibold">
                        <span class="material-symbols-outlined text-[15px]">trending_up</span>
                        +{{ $entriesThisWeek }} this week
                    </span>
                    <span class="text-on-surface-variant font-serif italic">Avg {{ number_format($averageWords) }} w/entry</span>
                </div>
            </div>

            {{-- Current streak --}}
            <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-[0_2px_12px_rgba(74,72,68,0.04)] flex flex-col justify-between hover:-translate-y-0.5 transition-transform duration-200">
                <div class="flex items-center justify-between text-on-surface-variant">
                    <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Current Streak</span>
                    <span class="material-symbols-outlined text-[20px] text-primary">local_fire_department</span>
                </div>
                <div class="mt-4 flex flex-col gap-1">
                    <span class="font-headline-lg text-headline-lg text-primary tracking-tight">{{ $currentStreak }} {{ Str::plural('Day', $currentStreak) }}</span>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        {{ $milestoneName }} badge unlocks in {{ $daysToGoal }} {{ Str::plural('day', $daysToGoal) }}
                    </p>
                </div>
                <div class="mt-4 pt-3">
                    <div class="w-full bg-surface-container h-1.5 rounded-full overflow-hidden">
                        <div class="bg-primary h-full rounded-full transition-all duration-500" style="width: {{ $milestonePercent }}%;"></div>
                    </div>
                    <span class="font-label-sm text-label-sm text-on-surface-variant mt-1.5 block">{{ $milestonePercent }}% to {{ $streakGoal }}-day milestone</span>
                </div>
            </div>

            {{-- Personal best --}}
            <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-[0_2px_12px_rgba(74,72,68,0.04)] flex flex-col justify-between hover:-translate-y-0.5 transition-transform duration-200">
                <div class="flex items-center justify-between text-on-surface-variant">
                    <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Personal Best</span>
                    <span class="material-symbols-outlined text-[20px] text-tertiary">military_tech</span>
                </div>
                @if ($longestRun)
                    <div class="mt-4 flex flex-col gap-1">
                        <span class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ $longestRun['days'] }} {{ Str::plural('Day', $longestRun['days']) }}</span>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Achieved {{ $longestRunDates }}</p>
                    </div>
                    <div class="mt-4 pt-3 flex items-center justify-between text-label-sm font-label-sm text-on-surface-variant">
                        <span>{{ $longestRun['start']->format('F') }} Run</span>
                        <span class="text-tertiary font-serif italic">Archive verified</span>
                    </div>
                @else
                    <div class="mt-4 flex flex-col gap-1">
                        <span class="font-headline-lg text-headline-lg text-on-surface tracking-tight">0 Days</span>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Your first run starts with one entry.</p>
                    </div>
                @endif
            </div>

            {{-- Rhythm consistency --}}
            <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-[0_2px_12px_rgba(74,72,68,0.04)] flex flex-col justify-between hover:-translate-y-0.5 transition-transform duration-200">
                <div class="flex items-center justify-between text-on-surface-variant">
                    <span class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Rhythm Consistency</span>
                    <span class="material-symbols-outlined text-[20px] text-secondary">bedtime</span>
                </div>
                <div class="mt-4 flex flex-col gap-1">
                    <span class="font-headline-lg text-headline-lg text-secondary tracking-tight">{{ $consistency }}%</span>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        {{ $writingWindow ? 'Preferred: '.$writingWindow : 'Write a few entries to find your rhythm.' }}
                    </p>
                </div>
                @if ($writingPersona)
                    <div class="mt-4 pt-3 flex items-center justify-between text-label-sm font-label-sm text-on-surface-variant">
                        <span class="inline-flex items-center gap-1 text-secondary">
                            <span class="material-symbols-outlined text-[14px]">file_map_stack</span>
                            {{ $writingPersona }} Chronicler
                        </span>
                        <span class="font-serif italic text-on-surface-variant">Quiet hours</span>
                    </div>
                @endif
            </div>
        </section>

        {{-- Identity and password cards --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">

            {{-- Chronicler identity --}}
            <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-[0_2px_16px_rgba(74,72,68,0.05)] flex flex-col justify-between gap-6">
                <div class="flex items-center justify-between pb-3 border-b border-surface-container">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[19px]">id_card</span>
                        </span>
                        <div>
                            <h3 class="font-title-lg text-title-lg text-on-surface">Chronicler Identity &amp; Affiliation</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Credentials associated with your personal volume</p>
                        </div>
                    </div>
                    <button class="font-label-md text-label-md text-primary hover:text-primary-container font-semibold transition-colors" type="button" data-open-persona>
                        Update
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                    <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Full Legal Name</span>
                        <span class="font-title-md text-title-md text-on-surface">{{ $user->full_name ?: 'Not set' }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant mt-1">Kept private from public archives</span>
                    </div>
                    <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Pen Name / Salutation</span>
                        <span class="font-title-md text-title-md text-on-surface">{{ $user->name }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant mt-1">Used in prompt greetings &amp; exports</span>
                    </div>
                    <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1 sm:col-span-2">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Email Registered</span>
                        <span class="font-title-md text-title-md text-on-surface">{{ $user->email }}</span>
                    </div>
                    <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1 sm:col-span-2">
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Sanctuary Inception</span>
                        <div class="flex items-center justify-between">
                            <span class="font-title-md text-title-md text-on-surface">{{ $user->created_at->format('F j, Y') }} · First Volume</span>
                            <span class="font-label-sm text-label-sm text-on-surface-variant font-serif italic">{{ $daysActive }} {{ Str::plural('Day', $daysActive) }} Active</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Password --}}
            <form class="bg-surface-container-lowest rounded-2xl p-6 shadow-[0_2px_16px_rgba(74,72,68,0.05)] flex flex-col justify-between gap-6" id="password-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between pb-3 border-b border-surface-container">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-surface-container flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-[19px]">lock</span>
                        </span>
                        <div>
                            <h3 class="font-title-lg text-title-lg text-on-surface">Vault Security &amp; Master Passcode</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Update your client-side encryption key to safeguard your private chronicles</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed text-label-sm font-semibold shrink-0">
                        <span class="material-symbols-outlined text-[14px]">verified_user</span>
                        AES-256 Vault
                    </span>
                </div>

                <div class="flex flex-col gap-4 flex-1">
                    {{-- Current password --}}
                    <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1.5">
                        <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="current-password">Current Master Passcode</label>
                        <div class="relative flex items-center">
                            <input class="w-full bg-surface-container-lowest border border-outline-variant/50 rounded-lg px-3.5 py-2 font-body-md text-on-surface focus:outline-none focus:border-primary pr-10 tracking-widest text-[16px]"
                                   id="current-password" name="current_password" type="password" autocomplete="current-password" required>
                            <button class="absolute right-3 text-on-surface-variant hover:text-on-surface transition-colors" type="button" data-toggle-password="current-password" aria-label="Show passcode">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword')
                            <span class="font-label-sm text-label-sm text-error">{{ $message }}</span>
                        @else
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Required to verify sovereign chronicle ownership</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- New password --}}
                        <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1.5">
                            <div class="flex items-center justify-between">
                                <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="new-password">New Passcode</label>
                                <span class="font-label-sm text-label-sm font-medium" id="strength-text"></span>
                            </div>
                            <div class="relative flex items-center">
                                <input class="w-full bg-surface-container-lowest border border-outline-variant/50 rounded-lg px-3.5 py-2 font-body-md text-on-surface focus:outline-none focus:border-primary pr-10 tracking-widest text-[16px]"
                                       id="new-password" name="password" type="password" autocomplete="new-password" required>
                                <button class="absolute right-3 text-on-surface-variant hover:text-on-surface transition-colors" type="button" data-toggle-password="new-password" aria-label="Show passcode">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                            </div>
                            <div class="w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden mt-1">
                                <div class="h-full rounded-full transition-all duration-300" id="strength-bar" style="width: 0%"></div>
                            </div>
                            @error('password', 'updatePassword')
                                <span class="font-label-sm text-label-sm text-error">{{ $message }}</span>
                            @else
                                <span class="font-label-sm text-label-sm text-on-surface-variant">Min 8 chars w/ symbols.</span>
                            @enderror
                        </div>

                        {{-- Confirm password --}}
                        <div class="bg-surface-container-low p-4 rounded-xl flex flex-col gap-1.5">
                            <div class="flex items-center justify-between">
                                <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="confirm-password">Confirm</label>
                                <span class="hidden font-label-sm text-label-sm text-secondary font-medium items-center gap-0.5" id="match-badge">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    Match
                                </span>
                            </div>
                            <div class="relative flex items-center">
                                <input class="w-full bg-surface-container-lowest border border-outline-variant/50 rounded-lg px-3.5 py-2 font-body-md text-on-surface focus:outline-none pr-10 tracking-widest text-[16px]"
                                       id="confirm-password" name="password_confirmation" type="password" autocomplete="new-password" required>
                                <button class="absolute right-3 text-on-surface-variant hover:text-on-surface transition-colors" type="button" data-toggle-password="confirm-password" aria-label="Show passcode">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                            </div>
                            <span class="hidden font-label-sm text-label-sm text-secondary font-serif italic mt-1" id="match-text">Signature verified</span>
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-t border-surface-container">
                    <div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
                        <span class="material-symbols-outlined text-[18px] text-tertiary shrink-0">shield_lock</span>
                        <span class="text-[14px]">End-to-end client-side encryption.</span>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <button class="px-3 py-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container font-title-md text-title-md transition-colors" type="reset">Discard</button>
                        <button class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-primary text-on-primary font-title-md text-title-md hover:bg-primary-container transition-all shadow-[0_2px_8px_rgba(154,68,45,0.2)]" type="submit">
                            <span class="material-symbols-outlined text-[18px]">key</span>
                            <span>Update Passcode</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Manifesto banner --}}
        <div class="w-full rounded-2xl bg-surface-container-high p-8 flex flex-col sm:flex-row items-center gap-6 justify-between text-center sm:text-left shadow-sm">
            <div class="flex flex-col gap-1 max-w-2xl">
                <span class="font-label-sm text-label-sm text-tertiary uppercase tracking-widest">Sanctuary Manifesto</span>
                <blockquote class="font-headline-sm text-headline-sm text-on-surface italic font-serif">
                    “To write is to carve out a quiet harbor in an ocean of noise. Your memories remain yours forever.”
                </blockquote>
                <span class="font-label-md text-label-md text-on-surface-variant mt-1">— The Daily Journal Archival Foundation</span>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-surface-container-lowest text-primary shadow-sm">
                    <span class="material-symbols-outlined text-[24px]">ink_pen</span>
                </span>
            </div>
        </div>
    </div>

    @include('profile.partials.edit-persona-modal')

    {{-- Toast after saving --}}
    @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
        <div class="fixed bottom-8 right-8 bg-inverse-surface text-inverse-on-surface px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 transition-opacity duration-300 z-50" id="profile-toast">
            <span class="material-symbols-outlined text-secondary-fixed text-[20px]">task_alt</span>
            <span class="font-label-md text-label-md">
                {{ session('status') === 'profile-updated' ? 'Persona saved to your dossier.' : 'Master passcode updated.' }}
            </span>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            // Show / hide passcode fields
            document.querySelectorAll('[data-toggle-password]').forEach(button => {
                button.addEventListener('click', () => {
                    const input = document.getElementById(button.dataset.togglePassword);
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    button.querySelector('.material-symbols-outlined').textContent = show ? 'visibility_off' : 'visibility';
                });
            });

            // Strength meter (visual hint only; the server enforces the real rules)
            const newPassword = document.getElementById('new-password');
            const confirmPassword = document.getElementById('confirm-password');
            const strengthText = document.getElementById('strength-text');
            const strengthBar = document.getElementById('strength-bar');
            const levels = [
                { label: 'Weak', text: 'text-error', bar: 'bg-error' },
                { label: 'Fair', text: 'text-tertiary', bar: 'bg-tertiary' },
                { label: 'Good', text: 'text-secondary', bar: 'bg-secondary' },
                { label: 'Strong', text: 'text-secondary', bar: 'bg-secondary' },
            ];

            function updateStrength() {
                const pw = newPassword.value;
                let score = 0;
                if (pw.length >= 8) score++;
                if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
                if (/\d/.test(pw)) score++;
                if (/[^A-Za-z0-9]/.test(pw)) score++;

                const level = levels[Math.max(score, 1) - 1];
                strengthText.className = 'font-label-sm text-label-sm font-medium ' + level.text;
                strengthText.textContent = pw ? `${level.label} · ${pw.length}` : '';
                strengthBar.className = 'h-full rounded-full transition-all duration-300 ' + level.bar;
                strengthBar.style.width = pw ? `${score * 25}%` : '0%';
            }

            function updateMatch() {
                const matches = confirmPassword.value !== '' && confirmPassword.value === newPassword.value;
                document.getElementById('match-badge').classList.toggle('hidden', !matches);
                document.getElementById('match-badge').classList.toggle('inline-flex', matches);
                document.getElementById('match-text').classList.toggle('hidden', !matches);
                confirmPassword.classList.toggle('border-secondary', matches);
                confirmPassword.classList.toggle('border-outline-variant/50', !matches);
            }

            newPassword.addEventListener('input', () => { updateStrength(); updateMatch(); });
            confirmPassword.addEventListener('input', updateMatch);
            document.getElementById('password-form').addEventListener('reset', () => {
                setTimeout(() => { updateStrength(); updateMatch(); });
            });

            // Fade out the "saved" toast
            const toast = document.getElementById('profile-toast');
            if (toast) {
                setTimeout(() => toast.classList.add('opacity-0', 'pointer-events-none'), 3500);
            }

            // Scroll back to the password card when it has errors
            @if ($errors->updatePassword->any())
                document.getElementById('password-form').scrollIntoView({ block: 'center' });
            @endif
        })();
    </script>
@endpush
