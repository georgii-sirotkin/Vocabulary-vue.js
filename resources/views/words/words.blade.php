@extends('layouts.authenticated')

@section('title', 'Words')

@section('content')
    <div class="row">
        <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
		    {!! implode(', ', $words->items()) . '<br>' !!}
	        {!! $words->links() !!}
        </div>
    </div>
@endsection