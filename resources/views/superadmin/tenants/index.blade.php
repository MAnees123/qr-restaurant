@extends('superadmin.layout')

@section('title', 'Restaurants')

@section('content')

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
    <div>
        <h1 style="font-size:22px; font-weight:800;">Restaurants</h1>
        <p style="font-size:13px; color:var(--muted); margin-top:3px;">Manage all tenant restaurant accounts</p>
    </div>
    <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Add Restaurant
    </a>
</div>

{{-- Search Bar --}}
<div class="card" style="margin-bottom:16px; padding:16px;">
    <form method="GET" style="display:flex; gap:10px; align-items:center;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by name, email…"
               class="form-control" style="max-width:300px;">
        <button class="btn btn-secondary" type="submit"><i class="fa-solid fa-search"></i> Search</button>
        @if(request('q'))
            <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-secondary">Clear</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Restaurant</th>
                    <th>Email</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Subscription Ends</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($restaurants as $r)
                <tr>
                    <td style="color:var(--muted); font-size:12px;">{{ $r->id }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            @if($r->logo)
                                <img src="{{ asset('storage/'.$r->logo) }}" style="width:32px;height:32px;border-radius:8px;object-fit:cover;">
                            @else
                                <div style="width:32px;height:32px;border-radius:8px;background:rgba(108,99,255,.2);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--accent2);">
                                    {{ strtoupper(substr($r->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div style="font-weight:600;">{{ $r->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $r->city ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--muted);">{{ $r->email ?? '—' }}</td>
                    <td>
                        <span class="badge badge-purple">{{ $r->subscription_plan ?? 'Free' }}</span>
                    </td>
                    <td>
                        @if($r->is_suspended)
                            <span class="badge badge-danger">Suspended</span>
                        @elseif($r->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-muted">Inactive</span>
                        @endif
                    </td>
                    <td style="font-size:12px; color:var(--muted);">
                        {{ $r->subscription_ends_at ? $r->subscription_ends_at->format('M d, Y') : '—' }}
                    </td>
                    <td style="font-size:12px; color:var(--muted);">{{ $r->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex; gap:5px; flex-wrap:wrap;">
                            <a href="{{ route('superadmin.tenants.show', $r) }}" class="btn btn-secondary btn-sm" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('superadmin.tenants.edit', $r) }}" class="btn btn-secondary btn-sm" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('superadmin.tenants.toggle-suspension', $r) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $r->is_suspended ? 'btn-success' : 'btn-danger' }}" title="{{ $r->is_suspended ? 'Activate' : 'Suspend' }}">
                                    <i class="fa-solid {{ $r->is_suspended ? 'fa-check' : 'fa-ban' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('superadmin.tenants.destroy', $r) }}" method="POST" style="display:inline;"
                                  onsubmit="return confirm('Delete {{ $r->name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:48px; color:var(--muted);">
                        <i class="fa-solid fa-store" style="font-size:32px; margin-bottom:12px; opacity:.3; display:block;"></i>
                        No restaurants found.
                        <a href="{{ route('superadmin.tenants.create') }}" style="color:var(--accent2);">Add your first →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($restaurants->hasPages())
    <div class="pagination" style="margin-top:20px;">
        {{ $restaurants->links('pagination::simple-default') }}
    </div>
    @endif
</div>

@endsection
