@extends('layouts.authenticated')

@section('title', 'Edit word')

@section('content')
    <div class="row">
        <div class="col-sm-11">
            @include('errors.partials.errors')
            {!! Form::model($word, array('route' => array('update_word', $word->slug), 'method' => 'PUT', 'files' => true, 'class' => 'form-horizontal')) !!}
                @include('words.partials.form')
            {!! Form::close() !!}
        </div>
    </div>
@endsection
