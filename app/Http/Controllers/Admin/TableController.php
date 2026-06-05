<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Facades\Storage;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::where('restaurant_id', auth()->user()->restaurant_id)
            ->with('qrCode')
            ->get();

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
            
        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $table = Table::create([
            'restaurant_id' => auth()->user()->restaurant_id,
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
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table)
    {
        if ($table->restaurant_id !== auth()->user()->restaurant_id) {
            abort(403);
        }

        $request->validate([
            'table_number' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'status' => 'nullable|in:free,reserved,occupied',
        ]);

        $table->update([
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
