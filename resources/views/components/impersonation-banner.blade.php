{{-- Shown on every page while an administrator is signed in as someone else. Inline styles so it renders the same in the archive and the Filament panel. --}}
@if (session()->has(\App\Http\Controllers\Auth\ImpersonationController::SESSION_KEY) && auth()->check())
    <div role="status" style="background:#7a4b0c;color:#fff;font-size:14px;line-height:1.4;position:relative;z-index:60;">
        <div style="max-width:80rem;margin:0 auto;padding:8px 16px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px 16px;">
            <p style="margin:0;">You are signed in as <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role?->label() }}). Everything you do is recorded as them.</p>
            <form method="POST" action="{{ route('impersonate.leave') }}" style="margin:0;">
                @csrf
                <button type="submit" style="background:#fff;color:#4a2d06;border:0;border-radius:6px;padding:4px 12px;font-weight:600;cursor:pointer;">Return to my account</button>
            </form>
        </div>
    </div>
@endif