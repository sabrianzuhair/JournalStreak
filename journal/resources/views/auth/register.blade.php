@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
    <div class="flex flex-col w-full py-space-xl px-margin-mobile lg:px-margin">
        <div class="max-w-7xl mx-auto w-full">

            {{-- Breadcrumb --}}
            <div class="flex items-center justify-between mb-space-lg">
                <div class="flex items-center gap-space-xs text-on-surface-variant font-label-md text-label-md">
                    <span>Sanctuary Portal</span>
                    <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    <span class="text-primary font-semibold">Volume Registration</span>
                </div>
                <div class="hidden sm:flex items-center gap-space-xs text-on-surface-variant font-body-sm text-body-sm bg-surface-container px-space-md py-1 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                    <span>Sanctuary Vault: Active &amp; Encrypted</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl items-start">

                {{-- Left Column: Registration Card --}}
                <div class="lg:col-span-7 bg-surface-container-lowest rounded-xl p-space-lg lg:p-space-xl shadow-sm">
                    <div class="mb-space-lg">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm tracking-wider uppercase font-semibold">
                            <span class="material-symbols-outlined text-[14px]">edit_note</span>
                            Begin Your Reflection Practice
                        </span>
                        <h1 class="mt-space-sm font-headline-lg text-headline-lg text-on-surface tracking-tight">
                            Open your personal sanctuary.
                        </h1>
                        <p class="mt-space-xs font-body-md text-body-md text-on-surface-variant">
                            Start your unbroken chronicle today. Take a mindful pause each evening, document what matters, and build a lasting habit.
                        </p>
                    </div>

                    {{-- Social SSO Row (not wired up yet) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-sm mb-space-lg">
                        <button class="flex items-center justify-center gap-space-xs py-2.5 px-space-sm bg-surface-container-low hover:bg-surface-container transition-all duration-200 rounded-lg text-on-surface font-label-md text-label-md shadow-sm hover:-translate-y-0.5" type="button">
                            <svg class="w-4 h-4" viewBox="0 0 24 24">
                                <path d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z" fill="#4285F4"></path>
                                <path d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z" fill="#34A853"></path>
                                <path d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z" fill="#FBBC05"></path>
                                <path d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z" fill="#EA4335"></path>
                            </svg>
                            <span>Google</span>
                        </button>
                        <button class="flex items-center justify-center gap-space-xs py-2.5 px-space-sm bg-surface-container-low hover:bg-surface-container transition-all duration-200 rounded-lg text-on-surface font-label-md text-label-md shadow-sm hover:-translate-y-0.5" type="button">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.4c.67-.82 1.12-1.97.99-3.11-.96.04-2.13.64-2.82 1.45-.61.71-1.14 1.87-.99 2.98 1.07.08 2.15-.5 2.82-1.32z"></path>
                            </svg>
                            <span>Apple</span>
                        </button>
                        <button class="flex items-center justify-center gap-space-xs py-2.5 px-space-sm bg-surface-container-low hover:bg-surface-container transition-all duration-200 rounded-lg text-on-surface font-label-md text-label-md shadow-sm hover:-translate-y-0.5" type="button">
                            <span class="material-symbols-outlined text-[18px] text-tertiary">school</span>
                            <span>Campus SSO</span>
                        </button>
                    </div>

                    {{-- Divider --}}
                    <div class="relative flex items-center justify-center my-space-lg">
                        <div class="w-full h-px bg-surface-variant"></div>
                        <span class="absolute px-space-md bg-surface-container-lowest font-label-sm text-label-sm uppercase tracking-wider text-outline">
                            or register with pen &amp; ink
                        </span>
                    </div>

                    {{-- Registration Form --}}
                    <form class="space-y-space-md" method="POST" action="{{ route('register') }}" onsubmit="handleRegister()">
                        @csrf

                        {{-- Pen Name --}}
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="font-title-md text-title-md text-on-surface" for="pen-name">Preferred Pen Name</label>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Used for evening greetings</span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-outline">
                                    <span class="material-symbols-outlined text-[20px]">history_edu</span>
                                </span>
                                <input class="w-full bg-surface-container-low text-on-surface pl-10 pr-space-md py-3 rounded-lg font-body-md text-body-md focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all duration-200 shadow-sm"
                                    id="pen-name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                                    placeholder="e.g. Elena, Julian, or Pen Name">
                            </div>
                            @error('name')
                                <p class="font-body-sm text-body-sm text-error pt-0.5">{{ $message }}</p>
                            @else
                                <p class="font-body-sm text-body-sm text-on-surface-variant pt-0.5">How your daily journal will gently greet you each night.</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="space-y-1">
                            <label class="font-title-md text-title-md text-on-surface" for="email">Sanctuary Email Address</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-outline">
                                    <span class="material-symbols-outlined text-[20px]">mail</span>
                                </span>
                                <input class="w-full bg-surface-container-low text-on-surface pl-10 pr-space-md py-3 rounded-lg font-body-md text-body-md focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all duration-200 shadow-sm"
                                    id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                                    placeholder="university or personal email">
                            </div>
                            @error('email')
                                <p class="font-body-sm text-body-sm text-error pt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="font-title-md text-title-md text-on-surface" for="vault-password">Master Vault Passcode</label>
                                <span class="hidden items-center gap-1 font-label-sm text-label-sm px-2 py-0.5 rounded-full font-medium" id="strength-badge">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    <span id="strength-text"></span>
                                </span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-outline">
                                    <span class="material-symbols-outlined text-[20px]">key</span>
                                </span>
                                <input class="w-full bg-surface-container-low text-on-surface pl-10 pr-12 py-3 rounded-lg font-body-md text-body-md focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all duration-200 shadow-sm"
                                    id="vault-password" name="password" type="password" required autocomplete="new-password">
                                <button aria-label="Toggle password visibility" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-on-surface-variant hover:text-on-surface transition-colors" onclick="togglePassword('vault-password', this)" type="button">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                            <div class="flex items-center gap-space-sm pt-1" id="strength-bars">
                                <div class="flex-1 h-1 bg-surface-variant rounded-full transition-colors"></div>
                                <div class="flex-1 h-1 bg-surface-variant rounded-full transition-colors"></div>
                                <div class="flex-1 h-1 bg-surface-variant rounded-full transition-colors"></div>
                                <div class="flex-1 h-1 bg-surface-variant rounded-full transition-colors"></div>
                            </div>
                            @error('password')
                                <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
                            @else
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Minimum 8 characters with a mix of letters, numbers, and sanctuary symbols.</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="space-y-1">
                            <label class="font-title-md text-title-md text-on-surface" for="vault-password-confirm">Confirm Passcode</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-outline">
                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                </span>
                                <input class="w-full bg-surface-container-low text-on-surface pl-10 pr-12 py-3 rounded-lg font-body-md text-body-md focus:bg-surface-container-lowest focus:outline-none focus:ring-2 focus:ring-primary-container transition-all duration-200 shadow-sm"
                                    id="vault-password-confirm" name="password_confirmation" type="password" required autocomplete="new-password">
                                <button aria-label="Toggle password visibility" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-on-surface-variant hover:text-on-surface transition-colors" onclick="togglePassword('vault-password-confirm', this)" type="button">
                                    <span class="material-symbols-outlined text-[20px]">visibility</span>
                                </button>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="pt-space-md">
                            <button class="w-full py-3.5 px-space-lg rounded-xl bg-primary-container hover:bg-primary text-on-primary font-title-lg text-title-lg flex items-center justify-center gap-space-sm shadow-md hover:shadow-lg transition-all duration-200 hover:-translate-y-0.5" id="register-btn" type="submit">
                                <span id="register-btn-text">Create Bound Journal</span>
                                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                            </button>
                        </div>

                        <div class="pt-space-xs flex items-center justify-center gap-space-xs text-center text-on-surface-variant font-body-sm text-body-sm">
                            <span class="material-symbols-outlined text-secondary text-[16px]">lock</span>
                            <span>End-to-end client-side vault encryption. Your entries are never scanned, sold, or shared.</span>
                        </div>

                        <div class="pt-space-xs text-center">
                            <p class="font-body-md text-body-md text-on-surface-variant">
                                Already have a bound journal?
                                <a class="text-primary font-title-md text-title-md hover:underline inline-flex items-center gap-0.5 ml-1" href="{{ route('login') }}">
                                    <span>Sign in to your sanctuary</span>
                                    <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
                                </a>
                            </p>
                        </div>
                    </form>
                </div>

                {{-- Right Column: Inspiration & Pillars --}}
                <div class="lg:col-span-5 space-y-space-lg">
                    <div class="bg-surface-container-low rounded-xl p-space-lg shadow-sm relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-primary/5 pointer-events-none"></div>
                        <div class="flex items-center justify-between mb-space-sm">
                            <span class="font-label-sm text-label-sm uppercase tracking-widest text-tertiary font-semibold flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">auto_stories</span>
                                Issue Nº 01 · First Page
                            </span>
                            <span class="font-label-sm text-label-sm px-2 py-0.5 rounded bg-surface-container-highest text-on-surface-variant">Archival</span>
                        </div>
                        <blockquote class="font-headline-sm text-headline-sm italic text-on-surface leading-snug my-space-md">
                            “The life of every person is a diary in which they mean to write one story, and write another.”
                        </blockquote>
                        <div class="flex items-center justify-between pt-space-xs text-on-surface-variant">
                            <cite class="font-title-md text-title-md not-italic font-medium">— J.M. Barrie</cite>
                            <span class="font-body-sm text-body-sm">Novelist &amp; Playwright</span>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm space-y-space-md">
                        <div class="border-b border-surface-variant/40 pb-space-sm">
                            <h2 class="font-title-lg text-title-lg text-on-surface">The Daily Journal Sanctuary</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Crafted for intentional thought, not algorithms.</p>
                        </div>
                        <div class="flex items-start gap-space-sm">
                            <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-primary text-[20px]">ink_pen</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-title-md text-title-md text-on-surface font-semibold">One Day, One Story</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Just 5 mindful minutes a day to declutter busy thoughts, record gratitude, and capture ephemeral moments.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-space-sm">
                            <div class="w-9 h-9 rounded-lg bg-tertiary/15 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-tertiary text-[20px]">spa</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-title-md text-title-md text-on-surface font-semibold">Gentle Streak System</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Fosters self-compassion and unbroken continuity. Miss a day? Life happens. Resume without anxiety or penalties.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-space-sm">
                            <div class="w-9 h-9 rounded-lg bg-secondary/15 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-secondary text-[20px]">menu_book</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-title-md text-title-md text-on-surface font-semibold">Archival Permanence</h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Export your life chronicles at any time into cloth-bound archival PDF monographs or open Markdown vaults.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('.material-symbols-outlined');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.textContent = show ? 'visibility_off' : 'visibility';
        }

        // Live strength meter (visual hint only; the server enforces the real rules)
        const strengthLevels = [
            { label: 'Weak', bar: 'bg-error', badge: 'bg-error/15 text-error' },
            { label: 'Fair', bar: 'bg-tertiary', badge: 'bg-tertiary/15 text-tertiary' },
            { label: 'Good', bar: 'bg-secondary', badge: 'bg-secondary/15 text-secondary' },
            { label: 'Strong', bar: 'bg-secondary', badge: 'bg-secondary/15 text-secondary' },
        ];

        document.getElementById('vault-password').addEventListener('input', (e) => {
            const pw = e.target.value;
            const badge = document.getElementById('strength-badge');
            const bars = document.querySelectorAll('#strength-bars > div');

            let score = 0;

            if (pw.length >= 8) score++;

            if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;

            if (/\d/.test(pw)) score++;

            if (/[^A-Za-z0-9]/.test(pw)) score++;

            const level = strengthLevels[Math.max(score, 1) - 1];
            bars.forEach((bar, i) => {
                bar.className = 'flex-1 h-1 rounded-full transition-colors ' + (pw && i < score ? level.bar : 'bg-surface-variant');
            });

            badge.className = 'items-center gap-1 font-label-sm text-label-sm px-2 py-0.5 rounded-full font-medium ' + level.badge + (pw ? ' inline-flex' : ' hidden');
            document.getElementById('strength-text').textContent = level.label + ' · ' + pw.length + ' chars';
        });

        function handleRegister() {
            const btn = document.getElementById('register-btn');
            document.getElementById('register-btn-text').textContent = 'Binding your journal...';
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
        }
    </script>
@endpush
