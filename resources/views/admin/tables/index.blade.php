@extends('layouts.admin')

@section('header', 'Table Management')

@section('content')
<div class="mb-6 flex flex-col md:flex-row gap-4 justify-between items-center bg-white p-4 rounded-xl shadow-sm border">
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.branches.index') }}" class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-100 whitespace-nowrap">Manage Branches</a>
        <a href="{{ route('admin.portions.index') }}" class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-lg font-medium hover:bg-indigo-100 whitespace-nowrap">Manage Portions</a>
        
        <label class="flex items-center gap-2 bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-100 cursor-pointer hover:bg-emerald-100 transition">
            <input type="checkbox" id="globalIncludeLogo" class="rounded text-emerald-500" onchange="toggleLogos()">
            <span class="text-sm font-bold text-emerald-700">Include Logo in QR</span>
        </label>
    </div>
    <form action="{{ route('admin.tables.index') }}" method="GET" class="flex gap-4 flex-wrap w-full md:w-auto">
        <select name="branch" class="rounded-lg border-gray-300">
            <option value="">All Branches</option>
            @foreach($branches as $b)
                <option value="{{ $b->id }}" {{ request('branch') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
        <select name="portion" class="rounded-lg border-gray-300">
            <option value="">All Portions</option>
            @foreach($portions as $p)
                <option value="{{ $p->id }}" {{ request('portion') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border-gray-300">
            <option value="">All Statuses</option>
            <option value="free" {{ request('status') == 'free' ? 'selected' : '' }}>Free</option>
            <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
            <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
        </select>
        <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-200">Filter</button>
    </form>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="bg-amber-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-amber-600 whitespace-nowrap">
        + Add Table
    </button>
</div>

@if($errors->any())
<div class="mb-6 bg-red-50 text-red-600 p-4 rounded-xl border border-red-200">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @foreach($tables as $table)
        <div class="bg-white rounded-xl shadow-sm border p-6 flex flex-col items-center text-center relative group">
            
            <h3 class="text-2xl font-black text-slate-800 mb-1">{{ $table->table_number }}</h3>
            <p class="text-xs text-slate-500 mb-3 font-bold uppercase tracking-wider">
                {{ $table->branch->name ?? 'No Branch' }} • {{ $table->portion->name ?? 'No Portion' }}
            </p>

            <div class="mb-4 flex gap-2">
                <span class="px-2 py-1 text-xs font-black rounded-lg uppercase tracking-widest
                    {{ $table->status === 'free' ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $table->status === 'occupied' ? 'bg-amber-100 text-amber-700' : '' }}
                    {{ $table->status === 'reserved' ? 'bg-blue-100 text-blue-700' : '' }}">
                    {{ $table->status }}
                </span>
                <span class="px-2 py-1 text-xs font-black rounded-lg uppercase tracking-widest {{ $table->is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                    {{ $table->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div class="mb-4 w-full">
                <div class="mx-auto inline-block p-4 bg-white border-2 border-dashed border-gray-200 rounded-xl" id="qrWrapper_{{ $table->id }}">
                    <div class="qr-svg-container" style="position: relative; width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->margin(1)->generate(route('menu.show', $table->secure_token ?? ($table->qrCode->code ?? 'none'))) !!}
                        
                        @if(auth()->user()->restaurant && auth()->user()->restaurant->logo)
                            <img src="{{ asset('storage/' . auth()->user()->restaurant->logo) }}" class="qr-logo hidden" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 34px; height: 34px; background-color: white; border: 3px solid white; border-radius: 6px; object-fit: cover; z-index: 10; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        @endif
                    </div>
                </div>
                
                <div class="flex justify-center gap-2 mt-3">
                    <a href="{{ route('menu.show', $table->secure_token ?? ($table->qrCode->code ?? 'none')) }}" target="_blank" class="px-3 py-1 bg-blue-50 text-blue-600 rounded text-xs font-bold hover:bg-blue-100 border border-blue-100">View Menu</a>
                    <button type="button" onclick="downloadTableQR({{ $table->id }}, '{{ $table->table_number }}')" class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded text-xs font-bold hover:bg-emerald-100 border border-emerald-100">Download SVG</button>
                    <button type="button" onclick="printTableQR({{ $table->id }}, '{{ $table->table_number }}')" class="px-3 py-1 bg-slate-50 text-slate-600 rounded text-xs font-bold hover:bg-slate-100 border border-slate-200">Print</button>
                </div>
            </div>
            
            <div class="flex gap-2 w-full mt-auto">
                <form action="{{ route('admin.tables.update', $table) }}" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <input type="hidden" name="branch_id" value="{{ $table->branch_id }}">
                    <input type="hidden" name="portion_id" value="{{ $table->portion_id }}">
                    <input type="hidden" name="table_number" value="{{ $table->table_number }}">
                    <input type="hidden" name="status" value="{{ $table->status === 'free' ? 'occupied' : 'free' }}">
                    <button type="submit" class="w-full py-2 text-xs font-bold border rounded-lg {{ $table->status === 'free' ? 'text-amber-600 bg-amber-50 hover:bg-amber-100 border-amber-100' : 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border-emerald-100' }}">
                        {{ $table->status === 'free' ? 'Mark Occupied' : 'Mark Free' }}
                    </button>
                </form>
                
                <!-- Edit -->
                <button type="button" onclick="openEditModal({{ $table->id }}, '{{ $table->table_number }}', '{{ $table->branch_id }}', '{{ $table->portion_id }}', '{{ $table->capacity }}', {{ $table->is_active ? 'true' : 'false' }})" class="px-3 py-2 text-blue-500 bg-blue-50 hover:bg-blue-100 rounded-lg transition border border-blue-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
                <!-- Delete -->
                <form action="{{ route('admin.tables.destroy', $table) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this table?')" class="px-3 py-2 text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition border border-red-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center" onclick="if(event.target === this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Add New Table</h2>
            <button onclick="document.getElementById('createModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form action="{{ route('admin.tables.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Branch</label>
                <select name="branch_id" class="w-full rounded-lg border-gray-300" required>
                    <option value="">Select Branch</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Portion/Section</label>
                <select name="portion_id" class="w-full rounded-lg border-gray-300" required>
                    <option value="">Select Portion</option>
                    @foreach($portions as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Table Name/Number</label>
                <input type="text" name="table_number" class="w-full rounded-lg border-gray-300" placeholder="e.g. T-1, VIP-1" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Capacity (Optional)</label>
                <input type="number" name="capacity" class="w-full rounded-lg border-gray-300" placeholder="e.g. 4">
            </div>
            <div class="mb-6 flex gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-amber-500">
                    <span class="text-sm font-medium">Active Status</span>
                </label>
            </div>
            <button type="submit" class="w-full bg-amber-500 text-white py-3 rounded-xl font-bold hover:bg-amber-600">Save Table</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center" onclick="if(event.target === this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Edit Table</h2>
            <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Branch</label>
                <select name="branch_id" id="edit_branch_id" class="w-full rounded-lg border-gray-300" required>
                    <option value="">Select Branch</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Portion/Section</label>
                <select name="portion_id" id="edit_portion_id" class="w-full rounded-lg border-gray-300" required>
                    <option value="">Select Portion</option>
                    @foreach($portions as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Table Name/Number</label>
                <input type="text" name="table_number" id="edit_table_number" class="w-full rounded-lg border-gray-300" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Capacity (Optional)</label>
                <input type="number" name="capacity" id="edit_capacity" class="w-full rounded-lg border-gray-300">
            </div>
            <div class="mb-6 flex gap-4">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded text-amber-500">
                    <span class="text-sm font-medium">Active Status</span>
                </label>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white py-3 rounded-xl font-bold hover:bg-blue-600">Update Table</button>
        </form>
    </div>
</div>



@endsection

@push('scripts')
<script>
    function toggleLogos() {
        const includeLogo = document.getElementById('globalIncludeLogo').checked;
        document.querySelectorAll('.qr-logo').forEach(logo => {
            if (includeLogo) {
                logo.classList.remove('hidden');
            } else {
                logo.classList.add('hidden');
            }
        });
    }

    function openEditModal(id, number, branch, portion, capacity, isActive) {
        document.getElementById('editForm').action = '/admin/tables/' + id;
        document.getElementById('edit_table_number').value = number;
        document.getElementById('edit_branch_id').value = branch || '';
        document.getElementById('edit_portion_id').value = portion || '';
        document.getElementById('edit_capacity').value = capacity || '';
        document.getElementById('edit_is_active').checked = isActive;
        document.getElementById('editModal').classList.remove('hidden');
    }

    async function downloadTableQR(tableId, tableName) {
        const wrapper = document.getElementById('qrWrapper_' + tableId);
        if(!wrapper) return;
        
        const svgElement = wrapper.querySelector('svg');
        if(!svgElement) return;
        
        // Clone to avoid modifying the screen version
        let clonedSvg = svgElement.cloneNode(true);
        if (!clonedSvg.getAttribute('xmlns:xlink')) {
            clonedSvg.setAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        }
        
        const includeLogo = document.getElementById('globalIncludeLogo').checked;
        const logoImg = wrapper.querySelector('.qr-logo');
        
        if (includeLogo && logoImg && !logoImg.classList.contains('hidden')) {
            try {
                // Fetch the logo image and convert to base64 so it works perfectly in the downloaded SVG
                const response = await fetch(logoImg.src);
                const blob = await response.blob();
                const base64 = await new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onloadend = () => resolve(reader.result);
                    reader.readAsDataURL(blob);
                });
                
                const size = 34; 
                // The SVG from SimpleSoftwareIO defaults to viewBox dimensions matching the size parameter (150x150)
                const center = (150 - size) / 2;
                
                // Add white background cutout
                const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                rect.setAttribute('x', center - 3);
                rect.setAttribute('y', center - 3);
                rect.setAttribute('width', size + 6);
                rect.setAttribute('height', size + 6);
                rect.setAttribute('fill', 'white');
                rect.setAttribute('rx', '6'); // border-radius equivalent
                
                // Add the image
                const image = document.createElementNS('http://www.w3.org/2000/svg', 'image');
                image.setAttribute('x', center);
                image.setAttribute('y', center);
                image.setAttribute('width', size);
                image.setAttribute('height', size);
                image.setAttributeNS('http://www.w3.org/1999/xlink', 'href', base64);
                
                clonedSvg.appendChild(rect);
                clonedSvg.appendChild(image);
            } catch (e) {
                console.error("Could not embed logo in SVG: ", e);
            }
        }
        
        const serializer = new XMLSerializer();
        let svgString = serializer.serializeToString(clonedSvg);
        
        if (!svgString.startsWith('<?xml')) {
            svgString = '<?xml version="1.0" encoding="UTF-8"?>\n' + svgString;
        }
        
        const blob = new Blob([svgString], {type: 'image/svg+xml;charset=utf-8'});
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.download = `QR_${tableName}.svg`;
        link.href = url;
        link.click();
        URL.revokeObjectURL(url);
    }

    function printTableQR(tableId, tableName) {
        const wrapper = document.getElementById('qrWrapper_' + tableId);
        if(!wrapper) return;
        
        const contentHtml = wrapper.innerHTML;
        
        const windowContent = '<!DOCTYPE html><html><head><title>Print QR Code</title>' +
            '<style>body{display:flex;justify-content:center;align-items:center;height:100vh;margin:0;font-family:sans-serif;} ' +
            '.relative{position:relative;} ' +
            '.absolute{position:absolute;} ' +
            '.top-1\\/2{top:50%;} ' +
            '.left-1\\/2{left:50%;} ' +
            '.transform{transform:translate(-50%,-50%);} ' +
            '.hidden{display:none;} ' +
            '.w-\\[36px\\]{width:36px;} ' +
            '.h-\\[36px\\]{height:36px;} ' +
            '.bg-white{background-color:white;} ' +
            '.rounded-full{border-radius:9999px;} ' +
            '</style></head><body>' +
            '<div style="text-align:center;">' +
            '<h1 style="margin-bottom:20px;">' + tableName + '</h1>' +
            '<div class="relative" style="display:inline-block; position:relative;">' + contentHtml + '</div>' +
            '</div></body></html>';
        
        const printWin = window.open('', '', 'width=800,height=600');
        printWin.document.open();
        printWin.document.write(windowContent);
        printWin.document.close();
        
        setTimeout(() => {
            printWin.focus();
            printWin.print();
            printWin.close();
        }, 500);
    }
</script>
@endpush