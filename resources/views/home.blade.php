@extends('layouts.authenticated')

@section('title', 'Home')

@section('content')
	<div class="row">
		<div class="col-sm-offset-1 col-sm-10 col-md-offset-2 col-md-8">
			<ul class="list-group">
				<a href="{{ route('add_word') }}" class="list-group-item"><i class="fa fa-plus"></i> Add</a>
				<a href="{{ route('words') }}" class="list-group-item"><i class="fa fa-list"></i> Words</a>
				<a href="{{ route('words') }}?search" class="list-group-item"><i class="fa fa-search"></i> Search</a>
				<a href="{{ route('random_word') }}" class="list-group-item"><i class="fa fa-random"></i> Random</a>
				<a href="" class="list-group-item"><i class="fa fa-info-circle"></i> About</a>
			</ul>
		</div>
	</div>
@endsection