@php($user = auth()->user())

{{-- Name and role beside the avatar, laid out like the archive masthead's account button --}}
<span class="rris-identity">
    <span class="rris-identity-name">{{ $user?->name }}</span>
    <span class="rris-identity-role">{{ $user?->role?->label() }}</span>
</span>
