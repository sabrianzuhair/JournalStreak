{{--
    "Edit Persona" dialog. Opens from any [data-open-persona] button, and on page load
    when the profile form came back with validation errors.
--}}
<div class="fixed inset-0 z-50 items-center justify-center p-4 bg-on-surface/40 backdrop-blur-sm transition-all duration-200 {{ $errors->any() ? 'flex' : 'hidden' }}"
     id="edit-persona-modal" role="dialog" aria-modal="true" aria-labelledby="edit-persona-title">
    <form class="bg-surface-container-lowest w-full max-w-2xl rounded-2xl shadow-[0_4px_24px_-2px_rgba(74,72,68,0.06)] border border-outline-variant/50 overflow-hidden flex flex-col"
          method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <input id="remove-avatar" name="remove_avatar" type="hidden" value="0">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-surface-container flex items-center justify-between bg-surface-container-low/70">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">edit_note</span>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-label-sm text-label-sm uppercase tracking-widest text-tertiary">Archival Chamber</span>
                        <span class="text-outline-variant font-label-sm">•</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Dossier Editor</span>
                    </div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface" id="edit-persona-title">Edit Persona &amp; Dossier</h2>
                </div>
            </div>
            <button class="w-8 h-8 rounded-full flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors" type="button" data-close-persona aria-label="Close">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="p-6 overflow-y-auto max-h-[75vh] flex flex-col gap-5">

            @if ($errors->any())
                <div class="flex items-center gap-2 px-4 py-3 rounded-xl bg-error-container text-on-error-container font-label-md text-label-md">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Portrait --}}
            <div class="flex flex-col sm:flex-row items-center gap-5 p-4 rounded-xl bg-surface-container-low/80 border border-surface-container">
                <div class="relative shrink-0">
                    <div class="w-20 h-20 rounded-2xl p-1 bg-surface-container-lowest shadow-sm">
                        <img class="w-full h-full object-cover rounded-xl {{ $user->avatarUrl() ? '' : 'hidden' }}" id="avatar-preview" src="{{ $user->avatarUrl() }}" alt="Portrait preview">
                        <span class="w-full h-full rounded-xl bg-primary-fixed text-primary font-title-md text-[24px] flex items-center justify-center {{ $user->avatarUrl() ? 'hidden' : '' }}" id="avatar-initials">
                            {{ $user->initials() }}
                        </span>
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[13px]">photo_camera</span>
                    </span>
                </div>
                <div class="flex flex-col items-center sm:items-start gap-1.5 flex-1 text-center sm:text-left">
                    <div>
                        <h4 class="font-title-md text-title-md text-on-surface">Chronicler Portrait</h4>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Presented on your public sanctuary folio and journal headers. JPG or PNG, up to 2 MB.</p>
                    </div>
                    <div class="flex items-center gap-2 mt-1">
                        <label class="cursor-pointer px-3 py-1.5 rounded-lg bg-surface-container-lowest text-primary font-label-md text-label-md hover:bg-surface-container border border-outline-variant/50 transition-colors shadow-sm inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">upload</span>
                            Change Portrait
                            <input class="sr-only" id="avatar-input" name="avatar" type="file" accept="image/*">
                        </label>
                        <button class="px-3 py-1.5 rounded-lg text-on-surface-variant font-label-md text-label-md hover:text-error transition-colors" id="avatar-remove" type="button">Remove</button>
                    </div>
                </div>
            </div>

            {{-- Pen name and handle --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="persona-name">Pen Name / Salutation</label>
                    <input class="w-full bg-surface-container-low border border-outline-variant/50 rounded-lg px-3.5 py-2 font-body-md text-on-surface focus:outline-none focus:border-primary text-[15px]"
                           id="persona-name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Used in prompt greetings &amp; entries</span>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="persona-handle">Chronicler Handle / ID</label>
                    <input class="w-full bg-surface-container-low border border-outline-variant/50 rounded-lg px-3.5 py-2 font-body-md text-on-surface focus:outline-none focus:border-primary text-[15px]"
                           id="persona-handle" name="handle" type="text" value="{{ old('handle', $user->handle ? '@'.$user->handle : '') }}" maxlength="31" placeholder="@your_handle">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Sovereign identity in archival records</span>
                </div>
            </div>

            {{-- Affiliation --}}
            <div class="flex flex-col gap-1.5">
                <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="persona-affiliation">Affiliation &amp; Academic Dossier</label>
                <input class="w-full bg-surface-container-low border border-outline-variant/50 rounded-lg px-3.5 py-2 font-body-md text-on-surface focus:outline-none focus:border-primary text-[15px]"
                       id="persona-affiliation" name="affiliation" type="text" value="{{ old('affiliation', $user->affiliation) }}" maxlength="255" placeholder="e.g. Northwood University · Computer Science '25">
            </div>

            {{-- Full legal name and email --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="persona-full-name">Full Legal Name</label>
                    <input class="w-full bg-surface-container-low border border-outline-variant/50 rounded-lg px-3.5 py-2 font-body-md text-on-surface focus:outline-none focus:border-primary text-[15px]"
                           id="persona-full-name" name="full_name" type="text" value="{{ old('full_name', $user->full_name) }}" maxlength="255" autocomplete="name">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Kept private from public archives</span>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="persona-email">Email Registered</label>
                    <input class="w-full bg-surface-container-low border border-outline-variant/50 rounded-lg px-3.5 py-2 font-body-md text-on-surface focus:outline-none focus:border-primary text-[15px]"
                           id="persona-email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Used to sign in to your sanctuary</span>
                </div>
            </div>

            {{-- Bio --}}
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center justify-between">
                    <label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider" for="persona-bio">Personal Philosophy / Bio Quote</label>
                    <span class="font-label-sm text-label-sm text-on-surface-variant"><span id="bio-count">0</span> / 180 chars</span>
                </div>
                <textarea class="w-full bg-surface-container-low border border-outline-variant/50 rounded-lg p-3 font-body-md text-on-surface font-serif italic text-[15px] focus:outline-none focus:border-primary resize-none"
                          id="persona-bio" name="bio" rows="3" maxlength="180" placeholder="Documenting fleeting ordinary beauty...">{{ old('bio', $user->bio) }}</textarea>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Displayed prominently on your dossier banner.</span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-surface-container bg-surface-container-low/50 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-1.5 text-on-surface-variant font-label-sm text-label-sm">
                <span class="material-symbols-outlined text-[16px] text-secondary">lock</span>
                <span>Changes are cryptographically signed &amp; backed up.</span>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <button class="px-4 py-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container font-title-md text-title-md transition-colors" type="button" data-close-persona>Cancel</button>
                <button class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg bg-primary text-on-primary font-title-md text-title-md hover:bg-primary-container transition-all shadow-[0_2px_8px_rgba(154,68,45,0.2)]" type="submit">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    <span>Save Persona Changes</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        (function () {
            const modal = document.getElementById('edit-persona-modal');
            const preview = document.getElementById('avatar-preview');
            const initials = document.getElementById('avatar-initials');
            const avatarInput = document.getElementById('avatar-input');
            const removeAvatar = document.getElementById('remove-avatar');
            const bio = document.getElementById('persona-bio');

            function setOpen(open) {
                modal.classList.toggle('hidden', !open);
                modal.classList.toggle('flex', open);
            }

            document.querySelectorAll('[data-open-persona]').forEach(button => button.addEventListener('click', () => setOpen(true)));
            document.querySelectorAll('[data-close-persona]').forEach(button => button.addEventListener('click', () => setOpen(false)));
            modal.addEventListener('click', event => {
                if (event.target === modal) setOpen(false);
            });
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') setOpen(false);
            });

            // Preview a newly chosen portrait
            avatarInput.addEventListener('change', () => {
                const file = avatarInput.files[0];
                if (!file) return;
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                initials.classList.add('hidden');
                removeAvatar.value = '0';
            });

            // Remove the portrait (applied when the form is saved)
            document.getElementById('avatar-remove').addEventListener('click', () => {
                avatarInput.value = '';
                preview.classList.add('hidden');
                initials.classList.remove('hidden');
                removeAvatar.value = '1';
            });

            // Live character count for the bio
            function updateBioCount() {
                document.getElementById('bio-count').textContent = bio.value.length;
            }
            bio.addEventListener('input', updateBioCount);
            updateBioCount();
        })();
    </script>
@endpush
