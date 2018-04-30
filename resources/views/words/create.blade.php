@extends('layouts.authenticated')

@section('title', 'Add word')

@section('content')
    @include('errors.partials.errors')
    <word-form :url="'{{ route('words.store') }}'"
               :csrf-token="'{{ csrf_token() }}'">
    </word-form>
@endsection
