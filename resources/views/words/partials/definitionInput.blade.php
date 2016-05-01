<div class="row">
    <div class="col-xs-12 padding-bottom-sm">
        <textarea class="form-control" name="definitions[]" rows="2">{{ !empty($definition) ? $definition->definition : '' }}</textarea>
    </div>
    <div class="col-xs-6 col-xs-offset-6 col-sm-4 col-sm-offset-8 col-md-3 col-md-offset-9 form-group">
        <button class="btn btn-danger btn-block deleteDefinition" type="button">
            <i class="fa fa-btn fa-trash"></i> Delete
        </button>
    </div>
    <input type="hidden" name="definitionIds[]" value="{{ !empty($definition) ? $definition->id : '' }}">
</div>