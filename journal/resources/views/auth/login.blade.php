@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
    <div class="flex flex-col w-full py-space-lg lg:py-space-xl">
        <div class="max-w-7xl mx-auto px-margin-mobile lg:px-margin w-full">

            {{-- Outer Asymmetric Editorial Auth Mosaic --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter lg:gap-space-xl items-stretch">

                {{-- Left Column: Primary Sign In Sanctuary Form --}}
                <div class="lg:col-span-7 flex flex-col justify-center">
                    <div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_24px_-4px_rgba(74,72,68,0.06),0_1px_3px_0_rgba(74,72,68,0.03)] p-space-md sm:p-space-lg lg:p-space-xl relative overflow-hidden transition-all duration-300">

                        {{-- Subtle Decorative Ambient Warm Glow --}}
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary-fixed/30 rounded-full blur-3xl pointer-events-none"></div>

                        {{-- Header Titles --}}
                        <div class="relative z-10 flex flex-col gap-space-xs mb-space-lg">
                            <div class="inline-flex items-center gap-space-xs self-start bg-surface-container px-3 py-1 rounded-full">
                                <span class="material-symbols-outlined text-primary text-[15px]" style="font-variation-settings: 'FILL' 1;">local_fire_department</span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant tracking-wide uppercase">Evening Session Available</span>
                            </div>
                            <h1 class="font-display-lg text-headline-lg lg:text-display-lg text-on-surface tracking-tight mt-1">
                                Return to your sanctuary.
                            </h1>
                            <p class="font-body-md text-body-md text-on-surface-variant max-w-xl">
                                Enter your quiet realm to chronicle today's moments, nurture your thoughts, and keep your reflection streak glowing.
                            </p>
                        </div>

                        {{-- Quick Identity Provider Options --}}
                        <div class="relative z-10 flex flex-col gap-space-sm mb-space-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-sm">

                                {{-- Google --}}
                                <button class="flex items-center justify-center gap-space-xs py-2.5 px-3 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface transition-all duration-200 group" type="button">
                                    <svg class="w-4 h-4 text-on-surface shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"></path>
                                    </svg>
                                    <span class="font-label-md text-label-md">Google</span>
                                </button>

                                {{-- Apple --}}
                                <button class="flex items-center justify-center gap-space-xs py-2.5 px-3 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface transition-all duration-200 group" type="button">
                                    <svg class="w-4 h-4 text-on-surface shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.42c.67-.82 1.13-1.96.99-3.1-.98.04-2.18.66-2.88 1.48-.61.71-1.15 1.87-1.01 2.98 1.1.08 2.22-.54 2.9-1.36z"></path>
                                    </svg>
                                    <span class="font-label-md text-label-md">Apple</span>
                                </button>

                                {{-- Campus SSO --}}
                                <button class="flex items-center justify-center gap-space-xs py-2.5 px-3 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface transition-all duration-200 group" type="button">
                                    <span class="material-symbols-outlined text-[17px] text-primary">school</span>
                                    <span class="font-label-md text-label-md">Campus SSO</span>
                                </button>
                            </div>

                            {{-- Soft Divider --}}
                            <div class="flex items-center gap-space-sm my-1">
                                <div class="flex-1 h-[1px] bg-surface-container-high"></div>
                                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest px-2">or continue with pen &amp; ink</span>
                                <div class="flex-1 h-[1px] bg-surface-container-high"></div>
                            </div>
                        </div>

                        {{-- Sign-in Form --}}
                        <form class="relative z-10 flex flex-col gap-space-md" id="sanctuary-login-form" method="POST" action="{{ route('login') }}" onsubmit="handleSanctuaryLogin()">
                            @csrf

                            {{-- Email Input --}}
                            <div class="flex flex-col gap-1.5">
                                <label class="font-label-md text-label-md text-on-surface flex items-center justify-between" for="sanctuary-email">
                                    <span>Email Address</span>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant font-normal">Campus or personal mail</span>
                                </label>
                                <div class="relative flex items-center">
                                    <span class="material-symbols-outlined absolute left-3.5 text-on-surface-variant text-[20px] pointer-events-none">mail</span>
                                    <input
                                        class="w-full pl-11 pr-4 py-3 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline-variant focus:bg-surface-container-lowest focus:outline-none focus:shadow-[0_0_0_2px_rgba(224,122,95,0.4)] transition-all duration-200"
                                        id="sanctuary-email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        autocomplete="username"
                                        placeholder="example@mail.com"
                                        required=""
                                        type="email"
                                        >
                                </div>
                            </div>

                            {{-- Password Input --}}
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="font-label-md text-label-md text-on-surface" for="sanctuary-password">Password</label>
                                    <a class="font-label-md text-label-md text-primary hover:text-on-primary-container transition-colors duration-150" href="{{ route('password.request') }}">
                                        Forgot password?
                                    </a>
                                </div>
                                <div class="relative flex items-center">
                                    <span class="material-symbols-outlined absolute left-3.5 text-on-surface-variant text-[20px] pointer-events-none">lock</span>
                                    <input
                                        class="w-full pl-11 pr-11 py-3 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline-variant focus:bg-surface-container-lowest focus:outline-none focus:shadow-[0_0_0_2px_rgba(224,122,95,0.4)] transition-all duration-200"
                                        id="sanctuary-password"
                                        name="password"
                                        autocomplete="current-password"
                                        placeholder="••••••••••••"
                                        required=""
                                        type="password"
                                        >
                                    <button aria-label="Toggle password visibility" class="absolute right-3 p-1 text-on-surface-variant hover:text-on-surface transition-colors" id="toggle-password-btn" onclick="togglePassword()" type="button">
                                        <span class="material-symbols-outlined text-[20px]" id="password-eye-icon">visibility</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Remember Me & Checkbox --}}
                            <div class="flex items-start gap-space-sm pt-1">
                                <label class="relative flex items-center cursor-pointer mt-0.5">
                                    <input checked="" class="sr-only peer" id="keep-signed-in" name="remember" type="checkbox">
                                    <div class="w-5 h-5 rounded-[6px] bg-surface-container flex items-center justify-center peer-checked:bg-primary transition-all duration-200 shadow-sm">
                                        <span class="material-symbols-outlined text-on-primary text-[16px] scale-0 peer-checked:scale-100 transition-transform duration-200">check</span>
                                    </div>
                                </label>
                                <div class="flex flex-col select-none cursor-pointer" onclick="document.getElementById('keep-signed-in').click()">
                                    <span class="font-label-md text-label-md text-on-surface">Keep me signed in on this device</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Recommended for your private notebook to ensure fast daily evening check-ins.</span>
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="p-3 rounded-lg bg-error-container text-on-error-container font-label-md text-label-md flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-[18px]">error</span>
                                    <span>{{ $errors->first() }}</span>
                                </div>
                            @endif

                            {{-- Submit Button CTA --}}
                            <button
                                class="w-full mt-2 py-3.5 px-6 rounded-lg bg-primary-container hover:bg-primary text-on-primary font-title-md text-title-md shadow-[0_4px_16px_rgba(224,122,95,0.25)] hover:shadow-[0_6px_20px_rgba(224,122,95,0.35)] transition-all duration-200 flex items-center justify-center gap-space-xs group active:translate-y-0.5"
                                id="submit-btn"
                                type="submit"
                                >
                                <span id="btn-text">Open Journal</span>
                                <span class="material-symbols-outlined text-[19px] group-hover:translate-x-1 transition-transform duration-200">arrow_forward</span>
                            </button>

                            {{-- Bottom Feedback Banner (Dynamic) --}}
                            <div class="hidden p-3 rounded-lg bg-secondary-fixed/50 text-on-secondary-fixed-variant font-label-md text-label-md flex items-center gap-space-xs" id="login-feedback">
                                <span class="material-symbols-outlined text-[18px]">verified</span>
                                <span>Sanctuary unlocked. Preparing your desk and open pages...</span>
                            </div>

                            {{-- Security Assurance Tag --}}
                            <div class="flex items-center justify-center gap-space-xs text-on-surface-variant pt-2">
                                <span class="material-symbols-outlined text-[16px] text-secondary">verified_user</span>
                                <span class="font-body-sm text-body-sm">End-to-end encrypted reflection vault. Your pages are entirely private.</span>
                            </div>

                            {{-- Account Switcher / Sign-up Invitation --}}
                            <div class="text-center pt-2 mt-1 bg-surface-container-low/60 rounded-lg py-3 px-4">
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Don't have a bound journal yet?</span>
                                <a class="ml-1.5 font-label-md text-label-md text-primary font-semibold hover:underline inline-flex items-center gap-0.5" href="{{ route('register') }}">
                                    <span>Begin your journey</span>
                                    <span class="material-symbols-outlined text-[14px]">auto_stories</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Right Column: Warm Editorial Sanctuary Panel & Habit Preview --}}
                <div class="lg:col-span-5 flex flex-col justify-between gap-space-md mt-6 lg:mt-0">

                    {{-- Top Editorial Hero Card with Wordsworth Quote --}}
                    <div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_24px_-4px_rgba(74,72,68,0.06),0_1px_3px_0_rgba(74,72,68,0.03)] p-space-md sm:p-space-lg flex flex-col justify-between relative overflow-hidden group">
                        <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-tertiary-fixed/30 rounded-full blur-2xl pointer-events-none"></div>

                        <div>
                            <div class="flex items-center justify-between mb-space-md">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[20px]">format_quote</span>
                                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Thought for dusk</span>
                                </div>
                                <span class="font-label-sm text-label-sm text-tertiary bg-tertiary-fixed/40 px-2.5 py-0.5 rounded-full font-medium">Issue Nº 48</span>
                            </div>

                            <blockquote class="font-headline-md text-headline-md text-on-surface italic leading-snug tracking-tight mb-space-sm">
                                "Fill your paper with the breathings of your heart."
                            </blockquote>
                            <p class="font-label-md text-label-md text-on-surface-variant text-right">
                                — William Wordsworth
                            </p>
                        </div>

                        {{-- Mini Atmospheric Photo Insert --}}
                        <div class="mt-space-md rounded-lg overflow-hidden relative h-36 bg-surface-container">
                            <img
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAE5Zp52RniUF_VDlJ4S0EkNM4SYNDTycorKs26CBn5paUvPrsFM82NNqBendKMFjGC3mRcmsZrnn5ykifjwbPwLbmhxFWwmcaDkUmK3gP-ZR98t6h2padFGOATQILQ4i64tGS6BIlYbYsDvifkslyiWdI5F_2sb6_Svf3m23G90f-nUBL1vYhPO-_u-L5ymUou01pHb4asuGN2Q5VuK-rgQHgrox8Rc533z8S4O_VzEWtExJKM2ywA"
                                >
                            <div class="absolute inset-0 bg-gradient-to-t from-inverse-surface/60 via-transparent to-transparent flex items-end p-3">
                                <span class="font-label-sm text-label-sm text-on-secondary flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">spa</span>
                                    <span>The quiet evening writing ritual</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Middle Streak & Milestone Card --}}

                    {{-- Bottom Pillars Mosaic --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-xs bg-surface-container-lowest p-space-sm rounded-xl shadow-sm">
                        <div class="p-2.5 rounded-lg flex flex-col gap-1 items-start bg-surface-container-low/50">
                            <span class="material-symbols-outlined text-secondary text-[20px]">encrypted</span>
                            <span class="font-title-md text-[13px] text-on-surface font-semibold">100% Private</span>
                            <span class="font-body-sm text-[11px] text-on-surface-variant leading-tight">Client-side vault isolation</span>
                        </div>
                        <div class="p-2.5 rounded-lg flex flex-col gap-1 items-start bg-surface-container-low/50">
                            <span class="material-symbols-outlined text-primary text-[20px]">psychology_alt</span>
                            <span class="font-title-md text-[13px] text-on-surface font-semibold">Gentle Habit</span>
                            <span class="font-body-sm text-[11px] text-on-surface-variant leading-tight">No aggressive guilt alerts</span>
                        </div>
                        <div class="p-2.5 rounded-lg flex flex-col gap-1 items-start bg-surface-container-low/50">
                            <span class="material-symbols-outlined text-tertiary text-[20px]">menu_book</span>
                            <span class="font-title-md text-[13px] text-on-surface font-semibold">Archival Export</span>
                            <span class="font-body-sm text-[11px] text-on-surface-variant leading-tight">Cloth-bound PDF prints</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('sanctuary-password');
            const eyeIcon = document.getElementById('password-eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = 'visibility';
            }
        }

        function handleSanctuaryLogin() {
            const btn = document.getElementById('submit-btn');
            const btnText = document.getElementById('btn-text');

            // Loading state only; the form submits to Laravel, which redirects on success
            btnText.textContent = 'Opening Vault...';
            btn.disabled = true;
            btn.classList.add('opacity-80', 'cursor-not-allowed');
        }
    </script>
@endpush
