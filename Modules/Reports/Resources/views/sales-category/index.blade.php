@extends('layouts.app')

@section('title', 'Sales Category Report')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Sales Category Report</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <livewire:reports.sales-category-report :customers="\Modules\People\Entities\Customer::all()" :categories="\Modules\Product\Entities\Category::all()"/>
    </div>
@endsection
