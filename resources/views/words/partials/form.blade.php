<div class="form-group">
    {!! Form::label('word', 'Word:') !!}
    {!! Form::text('word', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('imageUrl', 'Image url:') !!}
    {!! Form::text('imageUrl', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('image', 'Image:') !!}
    {!! Form::file('image', null, ['class' => 'form-control']) !!}
</div>

@if (!empty($word) && !empty($word->image_filename))
	<img src="{{ $word->getImageUrl() }}">
	<input type="hidden" name="keepImage" value="keepImage">
@endif

<h4>Definitions</h4>

@if (count($definitions) > 0)
    @foreach ($definitions as $definition)
		<div class="form-group">
			<textarea class="form-control" name="definitions[]" cols="50" rows="10">{{ $definition->definition }}</textarea>
			<input type="hidden" name="definitionIds[]" value="{{ $definition->id }}">
		</div>
	@endforeach
@else
	<div class="form-group">
		<textarea class="form-control" name="definitions[]" cols="50" rows="10"></textarea>
		<input type="hidden" name="definitionIds[]" value="">
	</div>
@endif

<div class="form-group">
	{!! Form::submit($buttonName, ['class' => 'btn btn-primary form-control']) !!}
</div>
