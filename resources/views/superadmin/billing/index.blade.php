@extends('superadmin.layout')

@section('title', 'Billing & Invoices')

@section('content')

<div style="margin-bottom:24px;">
    <h1 style="font-size:22px; font-weight:800;">Billing & Invoices</h1>
    <p style="font-size:13px; color:var(--muted); margin-top:3px;">Track revenue, invoices, and payment statuses</p>
</div>

{{-- ── Quick Stats ──────────────────────────────────────── --}}
<div class="stat-grid" style="grid-template-columns:repeat(4, 1fr);">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.15);">
            <i class="fa-solid fa-dollar-sign" style="color:#22c55e;"></i>
        </div>
        <div class="stat-value">${{ number_format($stats['total_revenue'], 2) }}</div>
        <div class="stat-label">Total Revenue</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,.15);">
            <i class="fa-solid fa-hourglass-half" style="color:#f59e0b;"></i>
        </div>
        <div class="stat-value">${{ number_format($stats['pending_amount'], 2) }}</div>
        <div class="stat-label">Pending Payments</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(108,99,255,.15);">
            <i class="fa-solid fa-calendar" style="color:var(--accent2);"></i>
        </div>
        <div class="stat-value">${{ number_format($stats['this_month'], 2) }}</div>
        <div class="stat-label">This Month</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,.15);">
            <i class="fa-solid fa-file-invoice" style="color:#60a5fa;"></i>
        </div>
        <div class="stat-value">{{ $stats['total_invoices'] }}</div>
        <div class="stat-label">Total Invoices</div>
    </div>
</div>

{{-- ── Filters ──────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:16px; padding:16px;">
    <form method="GET" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by restaurant name…"
               class="form-control" style="max-width:260px;">
        <select name="status" class="form-control" style="max-width:160px;">
            <option value="">All Statuses</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
        </select>
        <button class="btn btn-primary btn-sm" type="submit"><i class="fa-solid fa-search"></i> Filter</button>
        @if(request()->hasAny(['q', 'status']))
            <a href="{{ route('superadmin.billing.index') }}" class="btn btn-secondary btn-sm">Clear</a>
        @endif
    </form>
</div>

{{-- ── Invoice Table ────────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <span><i class="fa-solid fa-file-invoice-dollar" style="color:var(--accent2); margin-right:8px;"></i>Invoices</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice No.</th>
                    <th>Restaurant</th>
                    <th>Plan</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Cycle</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td style="color:var(--muted); font-size:12px;">{{ $inv->id }}</td>
                    <td style="font-weight:600; font-family:monospace; font-size:12px;">{{ $inv->invoice_number ?? '—' }}</td>
                    <td>
                        <div style="font-weight:600;">{{ $inv->restaurant->name ?? '—' }}</div>
                    </td>
                    <td>
                        <span class="badge badge-purple">{{ $inv->plan->name ?? '—' }}</span>
                    </td>
                    <td style="font-weight:700; color:var(--text);">
                        ${{ number_format($inv->amount, 2) }}
                        @if($inv->tax > 0)
                            <div style="font-size:10px; color:var(--muted);">+${{ number_format($inv->tax, 2) }} tax</div>
                        @endif
                    </td>
                    <td>
                        @if($inv->payment_status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($inv->payment_status === 'unpaid')
                            <span class="badge badge-warning">Unpaid</span>
                        @else
                            <span class="badge badge-danger">Failed</span>
                        @endif
                    </td>
                    <td style="font-size:12px; color:var(--muted); text-transform:capitalize;">{{ $inv->billing_cycle ?? '—' }}</td>
                    <td style="font-size:12px; color:var(--muted);">{{ $inv->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <p>No invoices found.</p>
                            <p style="font-size:12px; margin-top:4px;">Invoices will be generated automatically when subscriptions are created.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator && $invoices->hasPages())
    <div style="display:flex; justify-content:center; margin-top:20px;">
        {{ $invoices->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection
