<div class="form-group">
    <div class="col-xs-12 padding-bottom-sm">
        <textarea class="form-control" name="definitions[]" rows="2">{{ !empty($definition) ? $definition->text : '' }}</textarea>
    </div>
    <div class="col-xs-12 text-right">
        <button class="btn btn-danger btn-sm deleteDefinition" type="button">
            <i class="fa fa-btn fa-trash"></i> Delete
        </button>
    </div>
</div>