<div>
    <div class="padding-bottom-sm">
        <textarea class="form-control" name="definitions[]" rows="2">{{ !empty($definition) ? $definition->definition : '' }}</textarea>
    </div>
    <div class="form-group text-right">
        <button class="btn btn-danger btn-sm deleteDefinition" type="button">
            <i class="fa fa-btn fa-trash"></i> Delete
        </button>
    </div>
</div>