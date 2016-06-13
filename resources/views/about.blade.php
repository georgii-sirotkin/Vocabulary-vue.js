@extends('layouts.authenticated')

@section('title', 'About')

@section('content')
	<div class="row">
		<div class="col-sm-10 col-sm-offset-1">
			<p>{!! trans('about.appInfo', ['linkToRepo' => '<b><a href="https://github.com/georgii-sirotkin/Vocabulary" target="_blank">github</a></b>']) !!}</p>
			<p>{{ trans('about.contactInfo', ['emailAddress' => 'georgiysirotkin@gmail.com']) }}
		</div>
	</div>
@endsection