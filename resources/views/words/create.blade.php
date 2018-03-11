@extends('layouts.authenticated')

@section('title', 'Add word')

@section('content')
    @include('errors.partials.errors')
    <word-form></word-form>
    {!! Form::open(array('route' => 'words.store', 'files' => true, 'class' => 'form-horizontal')) !!}
        @include('words.partials.form')
    {!! Form::close() !!}
@endsection
