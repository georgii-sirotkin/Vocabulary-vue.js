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