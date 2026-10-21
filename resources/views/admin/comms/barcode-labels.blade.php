<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Barcode labels</title>
<style>
    body { font-family: Arial, sans-serif; margin: 10px; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, 60mm); gap: 4mm; }
    .label { border: 1px dashed #999; padding: 2mm; text-align: center; page-break-inside: avoid; }
    .label svg { max-width: 100%; height: auto; }
    .name { font-size: 12px; font-weight: bold; } .price { font-size: 11px; }
    .noprint { margin-bottom: 10px; } @media print { .noprint { display: none; } .label { border-color: #fff; } }
</style>
</head>
<body>
<div class="noprint"><button onclick="window.print()">Print labels</button> {{ $items->count() }} label(s)</div>
<div class="grid">
@forelse($items as $item)
    <div class="label"><div class="name">{{ $item->name }}</div>{!! $item->barcode_svg !!}<div class="price">Rs {{ number_format($item->price) }}</div></div>
@empty
    <p>No items have a barcode yet.</p>
@endforelse
</div>
</body>
</html>
