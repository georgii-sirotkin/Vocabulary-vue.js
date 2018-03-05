@extends('layouts.authenticated')

@section('title', 'Words')

@section('content')
	@if ($words->isEmpty())
		@include('errors.partials.nowords')
	@else
	    <div class="row">
	        <div class="col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2">
		        <div class="row">
			        @foreach ($words->getCollection()->chunk(ceil($words->perPage() / 2)) as $chunk)
				        <div class="col-sm-6">
					        <ul class="list-unstyled" style="margin-bottom: 0">
						        @foreach ($chunk as $word)
							        <li><b><a href="{{ route('words.show', $word) }}">{{ $word->title }}</a></b></li>
						        @endforeach
					        </ul>
				        </div>
			        @endforeach
		        </div>
	        </div>
	    </div>

    	<div class="space-for-stick-to-bottom{{ $words->hasPages() ? '' : ' hidden-xs' }}">
		</div>

	    <div class="stick-to-bottom text-center">
	        {!! $words->links() !!}
	    </div>
	@endif
@endsection