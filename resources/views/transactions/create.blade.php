@extends('layouts.app')

@section('title', '記録する')

@section('content')
    <h1 class="mb-4 text-xl font-bold">収支を記録する</h1>

    <form method="POST" action="{{ route('transactions.store') }}" class="max-w-md rounded-xl bg-white p-6 border border-slate-200">
        @csrf
        @include('transactions._form')
    </form>
@endsection
