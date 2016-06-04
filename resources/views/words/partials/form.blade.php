<div class="form-group">
    {!! Form::label('word', 'Word', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9 col-md-8">
        {!! Form::text('word', null, ['class' => 'form-control', 'id' => 'wordInput']) !!}
    </div>
</div>

<div class="form-group">
    <label class="col-sm-3 control-label">Image</label>
    <div class="col-sm-9 col-md-8">
        @if (!empty($word) && !empty($word->image_filename))
            <div id="oldImage">
                <img src="{{ $word->getImageUrl() }}" class="pull-left img-responsive">
                <input type="hidden" name="keepImage" value="keepImage">
                <button class="btn btn-danger btn-sm pull-left" id="deleteOldImage" type="button">
                    <i class="fa fa-btn fa-trash"></i> Delete
                </button>
            </div>
        @endif

        <div id="imageInput"@if (!empty($word) && !empty($word->image_filename)) style="display: none"@endif>
            <ul id="imageTabs" class="nav nav-tabs" role="tablist"> 
                <li role="presentation" class="active">
                    <a href="#imageUrl" id="imageUrl-tab" role="tab" data-toggle="tab">URL</a>
                </li>
                <li role="presentation">
                    <a href="#imageFile" role="tab" id="imageFile-tab" data-toggle="tab">Upload</a>
                </li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div role="tabpanel" class="tab-pane fade active in" id="imageUrl">
                    {!! Form::text('imageUrl', null, ['class' => 'form-control']) !!}
                </div>
                <div role="tabpanel" class="tab-pane fade" id="imageFile">
                    {!! Form::file('image', ['style' => 'height: 34px']) !!}
                </div> 
            </div>
        </div>
    </div>
</div>

<div class="form-group" id="definitionsArea"@if ($definitions->isEmpty()) style="display: none"@endif>
    <label class="col-sm-3 control-label">Defintions</label>

    <div class="col-sm-9 col-md-8" id="definitionsContainer">
        @each('words.partials.definitionInput', $definitions, 'definition')
    </div>
</div>

<div class="row">
    <div class="col-xs-7 col-sm-6 col-sm-offset-3 col-md-5">
        <button type="button" class="btn btn-default btn-block" id="addDefinitionButton">
            <i class="fa fa-btn fa-plus"></i> Add Definition
        </button>
    </div>
    <div class="col-xs-5 col-sm-3 col-md-3">
        <button type="submit" class="btn btn-primary btn-block">
            <i class="glyphicon glyphicon-save"></i> Save
        </button>
    </div>
</div>

@push('scripts')
    <script type="text/template" id="definitionTemplate">
        @include('words.partials.definitionInput')
    </script>
    <script src="{{ elixir('js/form.js') }}"></script>
@endpush