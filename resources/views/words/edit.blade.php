@extends('layouts.authenticated')

@section('title', 'Edit word')

@section('content')
    <word-form :initial-word="{{ $word }}"></word-form>
@endsection
