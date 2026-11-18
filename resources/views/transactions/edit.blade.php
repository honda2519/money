@extends('layouts.app')

@section('title', '編集')

@section('content')
    <h1 class="mb-4 text-xl font-bold">収支を編集する</h1>

    <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="max-w-md rounded-xl bg-white p-6 border border-slate-200">
        @csrf
        @method('PUT')
        @include('transactions._form')
    </form>
@endsection
