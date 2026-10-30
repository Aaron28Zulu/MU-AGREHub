<div>
    <section class="border-b border-line bg-canvas">
        <div class="container-page py-8">
            <nav class="text-sm text-muted" aria-label="Breadcrumb">
                <a href="{{ route('innovation') }}" class="hover:text-forest-800">Research &amp; Innovation</a>
                <span class="mx-1.5" aria-hidden="true">/</span>
                <a href="{{ route('innovation') }}#events" class="hover:text-forest-800">Events</a>
            </nav>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <x-pill :color="$record->event_type->color()">{{ $record->event_type->label() }}</x-pill>
                <span class="text-sm text-muted">{{ $record->starts_at->format('l j F Y, H:i') }}@if ($record->venue) &middot; {{ $record->venue }}@endif</span>
            </div>
            <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">{{ $record->title }}</h1>
            @if ($record->motion)
                <blockquote class="mt-4 max-w-3xl border-l-4 border-ochre-500 pl-4 font-serif text-lg leading-relaxed text-ink">
                    <span class="eyebrow block font-sans text-ochre-700">{{ $isDebate ? 'Motion' : 'Theme' }}</span>
                    {{ $record->motion }}
                </blockquote>
            @endif
        </div>
    </section>

    <div class="container-page grid gap-10 py-10 lg:grid-cols-[1fr_20rem]">
        <div class="min-w-0 space-y-10">
            <section>
                <h2 class="text-lg font-semibold">About</h2>
                <div class="mt-3 leading-relaxed whitespace-pre-line text-ink-soft">{{ $record->description }}</div>
            </section>

            @if ($record->outcomes)
                <section class="panel border-forest-200 bg-forest-50/40 p-6">
                    <h2 class="text-lg font-semibold">Outcomes and recommendations</h2>
                    <div class="mt-3 leading-relaxed whitespace-pre-line text-ink-soft">{{ $record->outcomes }}</div>
                </section>
            @endif

            @if ($speakers->isNotEmpty())
                <section>
                    <h2 class="text-lg font-semibold">{{ $isDebate ? 'Debaters' : 'Speakers' }}</h2>
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach ($speakers as $speaker)
                            <li class="chip">{{ $speaker->name }}@if ($speaker->pivot->position) <span class="text-muted">&middot; {{ ucfirst($speaker->pivot->position) }}</span>@endif</li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>

        <aside>
            <div class="panel p-5">
                <h2 class="eyebrow">Registration</h2>
                <p class="mt-3 text-sm text-ink-soft">
                    {{ $attendeeCount }} registered{{ $record->capacity ? ' of '.$record->capacity.' seats' : '' }}
                    @if ($record->speaker_slots) &middot; {{ $speakers->count() }} of {{ $record->speaker_slots }} speaker slots taken @endif
                </p>

                @error('registration') <p class="field-error">{{ $message }}</p> @enderror

                @if ($registration)
                    <div class="mt-4 rounded-md bg-forest-50 p-3 text-sm text-forest-900">
                        <p class="font-semibold">You're registered.</p>
                        @if ($registration->wants_to_speak)
                            <p class="mt-1">You're on the {{ $isDebate ? 'debaters' : 'speakers' }} list{{ $registration->position ? ' for the '.$registration->position : '' }}.</p>
                        @endif
                    </div>
                    @unless ($record->hasStarted())
                        <button type="button" wire:click="cancel" wire:confirm="Cancel your registration?" class="btn-ghost btn-sm mt-3 text-ink-soft hover:text-red-700">Cancel registration</button>
                    @endunless
                @elseif ($record->hasStarted())
                    <p class="mt-4 text-sm text-muted">Registration has closed.</p>
                @else
                    <form wire:submit="signUp" class="mt-4 space-y-4">
                        @auth
                            <label class="flex items-start gap-2.5 text-sm">
                                <input type="checkbox" wire:model.live="wants_to_speak" class="mt-0.5 rounded border-line-strong text-forest-700 focus:ring-forest-600/20" />
                                <span>I'd like to {{ $isDebate ? 'debate' : 'present or speak' }}</span>
                            </label>

                            @if ($isDebate && $wants_to_speak)
                                <div>
                                    <label for="position" class="field-label">Side</label>
                                    <select id="position" wire:model="position" class="field">
                                        <option value="">Choose a side</option>
                                        @foreach ($positions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('position') <p class="field-error">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            <button type="submit" class="btn-primary w-full">Register</button>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary w-full">Sign in to register</a>
                        @endauth
                    </form>
                @endif
            </div>
        </aside>
    </div>
</div>
