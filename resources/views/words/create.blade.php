@extends('layouts.authenticated')

@section('title', 'Add word')

@section('content')
    @include('errors.partials.errors')
    {!! Form::open(array('route' => 'insert_word', 'files' => true, 'class' => 'form-horizontal')) !!}
        @include('words.partials.form')
    {!! Form::close() !!}
@endsection
