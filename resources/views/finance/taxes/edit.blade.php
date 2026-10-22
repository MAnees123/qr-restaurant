@extends('layouts.admin')
@section('title', 'Edit Tax Rate')
@section('header', 'Edit Tax Rate')
@section('content')
<div class="max-w-md mx-auto">@include('finance.partials.errors')
    <div class="card p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.taxes.update', $rate) }}">@csrf @method('PUT') @include('finance.taxes.fields')
            <div class="flex flex-wrap items-center gap-3"><button class="btn btn-primary">Save</button><a href="{{ route('admin.taxes.index') }}" class="btn btn-secondary">Cancel</a></div></form>
    </div>
</div>
@endsection
