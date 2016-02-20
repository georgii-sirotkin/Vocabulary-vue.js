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

<!-- file upload / image url -->


<h4>Definitions</h4>

@if (!is_null(old('definitions')) && is_array(old('definitions')) && count(old('definitions')) > 0)
    @foreach (old('definitions') as $definition)
		<div class="form-group">
			<textarea class="form-control" name="definitions[]" cols="50" rows="10">{{ $definition }}</textarea>
		</div>
	@endforeach
@else
	<div class="form-group">
		<textarea class="form-control" name="definitions[]" cols="50" rows="10"></textarea>
	</div>

	{{-- temporary --}}

	<div class="form-group">
		<textarea class="form-control" name="definitions[]" cols="50" rows="10"></textarea>
	</div>

	<div class="form-group">
		<textarea class="form-control" name="definitions[]" cols="50" rows="10"></textarea>
	</div>
@endif

<div class="form-group">
	{!! Form::submit('Add word', ['class' => 'btn btn-primary form-control']) !!}
</div>
