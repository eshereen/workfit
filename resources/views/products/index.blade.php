@extends('layouts.app')

@section('content')
    @livewire('product-index', ['products' => $result])
@endsection
