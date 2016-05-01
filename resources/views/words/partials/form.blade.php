<div class="form-group">
    {!! Form::label('word', 'Word:') !!}
    {!! Form::text('word', null, ['class' => 'form-control', 'id' => 'wordInput']) !!}
</div>

<label>Image</label>

@if (!empty($word) && !empty($word->image_filename))
    <div id="oldImage">
        <img src="{{ $word->getImageUrl() }}" class="img-responsive padding-bottom-sm">
        <input type="hidden" name="keepImage" value="keepImage">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-6 col-sm-4 col-sm-offset-8 col-md-3 col-md-offset-9 form-group">
                <button class="btn btn-danger btn-block" id="deleteOldImage" type="button">
                    <i class="fa fa-btn fa-trash"></i> Delete
                </button>
            </div>
        </div>
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
            <div class="form-group">
                {!! Form::text('imageUrl', null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="imageFile">
            <div class="form-group">
                {!! Form::file('image', ['style' => 'height: 34px']) !!}
            </div>
        </div> 
    </div>
</div>

<label>Defintions</label>

<div id="definitionsContainer">
    @each('words.partials.definitionInput', $definitions, 'definition', 'words.partials.definitionInput')
</div>

<div class="row">
    <div class="col-xs-12 form-group">
        <button type="button" class="btn btn-default btn-block" id="addDefinitionButton">
            <i class="fa fa-btn fa-plus"></i> Add Definition
        </button>
    </div>
</div>

<div class="form-group">
    <button type="submit" class="btn btn-primary form-control">
        <i class="glyphicon glyphicon-save"></i> Save
    </button>
</div>

@push('scripts')
    <script type="text/template" id="definitionTemplate">
        @include('words.partials.definitionInput')
    </script>
    <script src="{{ elixir('js/form.js') }}"></script>
@endpush