@extends('layouts.admin')
@section('title', 'Edit Tax Rate')
@section('header', 'Edit Tax Rate')
@section('content')
<div class="max-w-md mx-auto">@include('finance.partials.errors')
    <div class="bg-white rounded-[2rem] border shadow-sm p-8">
        <form method="POST" action="{{ route('admin.taxes.update', $rate) }}">@csrf @method('PUT') @include('finance.taxes.fields')<button class="w-full bg-emerald-600 text-white font-black py-3 rounded-2xl">Save</button></form>
    </div>
</div>
@endsection
