<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BillingInvoice;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $invoices = collect();
        if (Schema::hasTable('billing_invoices')) {
            $query = BillingInvoice::with('restaurant', 'plan')->latest();

            if ($request->filled('status')) {
                $query->where('payment_status', $request->status);
            }
            if ($request->filled('q')) {
                $query->whereHas('restaurant', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->q . '%');
                });
            }

            $invoices = $query->paginate(20);
        }

        // Quick billing stats
        $stats = [
            'total_revenue'   => Schema::hasTable('billing_invoices') ? BillingInvoice::where('payment_status', 'paid')->sum('amount') : 0,
            'pending_amount'  => Schema::hasTable('billing_invoices') ? BillingInvoice::where('payment_status', 'unpaid')->sum('amount') : 0,
            'this_month'      => Schema::hasTable('billing_invoices') ? BillingInvoice::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('amount') : 0,
            'total_invoices'  => Schema::hasTable('billing_invoices') ? BillingInvoice::count() : 0,
        ];

        return view('superadmin.billing.index', compact('invoices', 'stats'));
    }
}
