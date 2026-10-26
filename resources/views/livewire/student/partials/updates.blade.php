<section id="updates" class="panel scroll-mt-24" aria-labelledby="updates-heading">
    <div class="flex items-center justify-between border-b border-line px-6 py-4">
        <h2 id="updates-heading" class="eyebrow">Updates</h2>
        @if ($notifications->whereNull('read_at')->isNotEmpty())
            <button type="button" wire:click="markNotificationsRead" class="text-xs font-medium text-forest-700 hover:underline">Mark all read</button>
        @endif
    </div>
    @forelse ($notifications as $notification)
        <div class="flex gap-3 border-b border-line px-6 py-4 last:border-b-0">
            <span @class(['mt-1.5 h-2 w-2 shrink-0 rounded-full', 'bg-ochre-500' => ! $notification->read_at, 'bg-line-strong' => $notification->read_at]) aria-hidden="true"></span>
            <div class="min-w-0">
                <p @class(['text-sm', 'font-semibold' => ! $notification->read_at, 'font-medium text-ink-soft' => $notification->read_at])>{{ $notification->data['title'] ?? 'Update' }}</p>
                @if (! empty($notification->data['body']))
                    <p class="mt-0.5 text-sm leading-relaxed text-ink-soft">{{ $notification->data['body'] }}</p>
                @endif
                <p class="mt-1 text-xs text-muted">{{ $notification->created_at->diffForHumans() }}</p>
            </div>
        </div>
    @empty
        <p class="px-6 py-8 text-sm text-muted">No updates yet. You'll be notified here when your supervisor reviews a submission.</p>
    @endforelse
</section>
