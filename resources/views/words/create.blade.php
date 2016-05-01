@extends('layouts.authenticated')

@section('title', 'Add word')

@section('content')
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            @include('errors.partials.errors')
            {!! Form::open(array('route' => 'insert_word', 'files' => true)) !!}
                @include('words.partials.form')
            {!! Form::close() !!}
        </div>
    </div>
@endsection
