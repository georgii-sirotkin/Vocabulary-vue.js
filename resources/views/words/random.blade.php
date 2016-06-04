@extends('layouts.authenticated')

@section('title', 'Random')

@section('content')
	@if (is_null($word))
		@include('errors.partials.nowords')
	@else
		{{ $word }}
	@endif
@endsection