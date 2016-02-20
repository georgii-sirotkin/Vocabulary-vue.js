@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Add</div>

                <div class="panel-body">
                    @include('errors.partials.errors')
                    {!! Form::open(array('route' => 'insert_word', 'files' => true)) !!}
                        @include('words.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
