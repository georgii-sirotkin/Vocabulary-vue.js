@extends('layouts.authenticated')

@section('title', 'Add word')

@section('content')
    <word-form :words-url="'{{ route('words.index') }}'"></word-form>
@endsection
