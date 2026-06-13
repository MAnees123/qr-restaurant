<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Branch;
use App\Models\Portion;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Facades\Storage;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $query = Table::where('restaurant_id', auth()->user()->restaurant_id)->with(['qrCode', 'branch', 'portion']);
        
        if ($request->filled('branch')) {
            $query->where('branch_id', $request->branch);
        }
        if ($request->filled('portion')) {
            $query->where('portion_id', $request->portion);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $tables = $query->get();

        // Auto-fix: Generate missing QR codes for existing tables
        foreach ($tables as $table) {
            if (!$table->qrCode) {
                $uuid = (string) \Illuminate\Support\Str::uuid();
                \App\Models\QrCode::create([
                    'table_id' => $table->id,
                    'code' => $uuid,
                ]);
                $table->load('qrCode'); // Reload relationship
            }
        }
            
        $branches = Branch::where('restaurant_id', auth()->user()->restaurant_id)->where('is_active', true)->get();
        $portions = Portion::where('restaurant_id', auth()->user()->restaurant_id)->where('is_active', true)->get();
            
        return view('admin.tables.index', compact('tables', 'branches', 'portions'));
    }

    public function create()
    {
        $restaurantId = auth()->user()->restaurant_id;
        $branches = Branch::where('restaurant_id', $restaurantId)->where('is_active', true)->get();
        $portions = Portion::where('restaurant_id', $restaurantId)->where('is_active', true)->get();
        
        return view('admin.tables.create', compact('branches', 'portions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'portion_id' => 'required|exists:portions,id',
            'table_number' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        // Prevent duplicate table names in same branch and portion
        $exists = Table::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('branch_id', $request->branch_id)
            ->where('portion_id', $request->portion_id)
            ->where('table_number', $request->table_number)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['table_number' => 'Table name already exists in this branch and portion.']);
        }

        $table = Table::create([
            'restaurant_id' => auth()->user()->restaurant_id,
            'branch_id' => $request->branch_id,
            'portion_id' => $request->portion_id,
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'is_active' => $request->has('is_active'),
        ]);

        // Generate unique QR code UUID
        $uuid = Str::uuid();
        
        // Generate QR code SVG string pointing to the guest menu route
        $url = route('menu.show', ['code' => $uuid]);
        
        // We will generate QR codes on the fly in the view, or store them as SVG.
        // For now, we just save the UUID.
        QrCode::create([
            'table_id' => $table->id,
            'code' => $uuid,
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Table created successfully.');
    }

    public function edit(Table $table)
    {
        if ($table->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        
        $branches = Branch::where('restaurant_id', auth()->user()->restaurant_id)->where('is_active', true)->get();
        $portions = Portion::where('restaurant_id', auth()->user()->restaurant_id)->where('is_active', true)->get();

        return view('admin.tables.edit', compact('table', 'branches', 'portions'));
    }

    public function update(Request $request, Table $table)
    {
        if ($table->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'portion_id' => 'required|exists:portions,id',
            'table_number' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'nullable|in:free,reserved,occupied',
        ]);

        // Prevent duplicate table names in same branch and portion
        $exists = Table::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('branch_id', $request->branch_id)
            ->where('portion_id', $request->portion_id)
            ->where('table_number', $request->table_number)
            ->where('id', '!=', $table->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['table_number' => 'Table name already exists in this branch and portion.']);
        }

        $table->update([
            'branch_id' => $request->branch_id,
            'portion_id' => $request->portion_id,
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'is_active' => $request->has('status') ? $table->is_active : $request->has('is_active'),
            'status' => $request->status ?? $table->status,
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Table updated successfully.');
    }

    public function destroy(Table $table)
    {
        if ($table->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }
        
        $table->delete();
        return redirect()->route('admin.tables.index')->with('success', 'Table deleted successfully.');
    }
}
