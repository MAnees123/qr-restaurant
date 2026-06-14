@extends('superadmin.layout')

@section('title', 'Activity Logs')

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
    <div>
        <h1 style="font-size:22px; font-weight:800;">Activity Logs</h1>
        <p style="font-size:13px; color:var(--muted); margin-top:3px;">Track all administrative actions across the system</p>
    </div>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:16px; padding:16px;">
    <form method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search in description…"
               class="form-control" style="max-width:260px;">
        <select name="action" class="form-control" style="max-width:180px;">
            <option value="">All Actions</option>
            @foreach($actions as $action)
                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary btn-sm" type="submit"><i class="fa-solid fa-search"></i> Filter</button>
        @if(request()->hasAny(['q', 'action', 'user_id']))
            <a href="{{ route('superadmin.activity-logs.index') }}" class="btn btn-secondary btn-sm">Clear</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="white-space:nowrap; font-size:12px; color:var(--muted);">
                        <i class="fa-regular fa-clock" style="margin-right:4px;"></i>
                        {{ $log->created_at->diffForHumans() }}
                        <div style="font-size:10px; opacity:.7;">{{ $log->created_at->format('M d, Y H:i') }}</div>
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="width:28px;height:28px;border-radius:8px;background:rgba(108,99,255,.15);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:var(--accent2);">
                                {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:13px;">{{ $log->user->name ?? 'System' }}</div>
                                <div style="font-size:10px; color:var(--muted);">{{ $log->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $actionColors = [
                                'create' => 'badge-success',
                                'update' => 'badge-warning',
                                'delete' => 'badge-danger',
                                'login'  => 'badge-purple',
                                'logout' => 'badge-muted',
                            ];
                            $badgeClass = $actionColors[$log->action] ?? 'badge-muted';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                    </td>
                    <td style="max-width:320px; font-size:13px;">
                        {{ \Illuminate\Support\Str::limit($log->description, 80) }}
                    </td>
                    <td style="font-size:12px; color:var(--muted); font-family:monospace;">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fa-solid fa-list-check"></i>
                            <p>No activity logs recorded yet.</p>
                            <p style="font-size:12px; margin-top:4px;">Administrative actions will appear here automatically.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator && $logs->hasPages())
    <div style="display:flex; justify-content:center; margin-top:20px;">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection
