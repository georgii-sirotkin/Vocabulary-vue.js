@extends('layouts.authenticated')

@section('title', $word->word)

@section('content')
	<div class="row">
		<div class="col-sm-offset-1 col-sm-10 col-md-offset-1 col-md-10">
			@if (!empty($word->image_filename))
			    <p>
			        <img src="{{ $word->getImageUrl() }}" class="img-responsive padding-bottom-sm">
			    </p>
			@endif

			@foreach ($word->definitions as $definition)
				<p>{{ $definition->definition }}</p>
			@endforeach
		</div>
	</div>

	<div class="space-for-stick-to-bottom">
	</div>

    <div class="stick-to-bottom button-panel">
	    <div class="row">
		    <div class="col-xs-6 col-sm-offset-1 col-sm-3">
			    {!! Form::open(array('method' => 'DELETE', 'route' => array('delete_word', $word->slug))) !!}
				    <button type="submit" class="btn btn-danger btn-block">
					    <i class="fa fa-btn fa-trash"></i> Delete
				    </button>
			    {!! Form::close() !!}
		    </div>
		    <div class="col-xs-6 col-sm-3">
			    <a href="{{ route('edit_word', $word->slug) }}" class="btn btn-default btn-block">
				    <span class="glyphicon glyphicon-edit"></span> Edit
			    </a>
		    </div>
	    </div>
    </div>
@endsection