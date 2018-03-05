@extends('layouts.authenticated')

@section('title', 'Home')

@section('content')
	@if (Session::get('showHelloMessage'))
		@include('words.partials.modal', ['numberOfWords' => Session::get('numberOfWords')])
	@endif

	<div class="row">
		<div class="col-sm-offset-1 col-sm-10 col-md-offset-2 col-md-8">
			<ul class="list-group">
				<a href="{{ route('words.create') }}" class="list-group-item"><i class="fa fa-plus"></i> Add</a>
				<a href="{{ route('random_word') }}" class="list-group-item"><i class="fa fa-question-circle-o"></i> Quiz</a>
				<a href="{{ route('words.index') }}" class="list-group-item"><i class="fa fa-list"></i> Words</a>
				<a href="{{ route('words.index') }}?search" class="list-group-item"><i class="fa fa-search"></i> Search</a>
				<a href="{{ route('about') }}" class="list-group-item"><i class="fa fa-info-circle"></i> About</a>
			</ul>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		$("#modal").modal();
	</script>
@endpush