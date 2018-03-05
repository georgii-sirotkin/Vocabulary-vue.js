@extends('layouts.authenticated')

@section('title')
	{{ $searchString ? $searchString : 'Search' }}
@endsection

@section('pageHeader', $searchString ? 'Search results' : 'Search')

@section('content')
    <div class="row">
	    <div class="col-sm-offset-1 col-sm-10 col-md-9">
	        {!! Form::open(array('method' => 'GET', 'route' => 'words.index')) !!}
		        <div class="input-group form-group padding-bottom-sm">
			        {!! Form::text('search', request('search'), ['class' => 'form-control']) !!}
			        <span class="input-group-btn">
				        <button type="submit" class="btn btn-primary">Search</button>
		            </span>
	            </div>
		    {!! Form::close() !!}

	        @if ($searchString)
		        <div style="padding-left: 15px">
				    @if ($words->isEmpty())
					    Not found
				    @else
					    <ul class="list-unstyled">
						    @foreach ($words as $word)
							    <li><b><a href="{{ route('words.show', $word->slug) }}">{{ $word->title }}</a></b></li>
						    @endforeach
					    </ul>
				    @endif
			    </div>
		    @endif
	    </div>
    </div>

@endsection