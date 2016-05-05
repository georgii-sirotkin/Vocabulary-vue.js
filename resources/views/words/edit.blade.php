@extends('layouts.authenticated')

@section('title', 'Edit word')

@section('content')
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            @include('errors.partials.errors')
            {!! Form::model($word, array('route' => array('update_word', $word->slug), 'method' => 'PUT', 'files' => true)) !!}
                @include('words.partials.form')
            {!! Form::close() !!}
        </div>
    </div>
@endsection
