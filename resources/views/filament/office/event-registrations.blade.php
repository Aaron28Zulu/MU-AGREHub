@php
    $speakers = $attendees->filter(fn ($attendee) => $attendee->pivot->wants_to_speak);
@endphp

<div class="space-y-4 text-sm">
    <p class="text-gray-600">
        {{ $attendees->count() }} registered{{ $event->capacity ? ' of '.$event->capacity.' seats' : '' }}, {{ $speakers->count() }} {{ $event->event_type === \App\Enums\EventType::DEBATE ? 'want to debate' : 'want to speak' }}.
    </p>

    @if ($attendees->isEmpty())
        <p class="rounded-lg border border-gray-200 p-6 text-center text-gray-500">Nobody has registered yet.</p>
    @else
        <div class="rris-table-scroll">
            <table class="w-full divide-y divide-gray-200 overflow-hidden rounded-lg border border-gray-200">
                <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-500">
                    <tr>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Department</th>
                        <th class="px-3 py-2">Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($attendees as $attendee)
                        <tr>
                            <td class="px-3 py-2 font-medium">{{ $attendee->name }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $attendee->department ?? '—' }}</td>
                            <td class="px-3 py-2 text-gray-600">
                                @if ($attendee->pivot->wants_to_speak)
                                    {{ $attendee->pivot->position ? ucfirst($attendee->pivot->position) : 'Speaker' }}
                                @else
                                    Attendee
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
