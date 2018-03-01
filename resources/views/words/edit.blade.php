@extends('layouts.authenticated')

@section('title', 'Edit word')

@section('content')
    @include('errors.partials.errors')
    {!! Form::model($word, array('route' => array('update_word', $word), 'method' => 'PUT', 'files' => true, 'class' => 'form-horizontal')) !!}
        @include('words.partials.form')
    {!! Form::close() !!}
@endsection
