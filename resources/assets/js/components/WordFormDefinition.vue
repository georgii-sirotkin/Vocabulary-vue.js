<template>
    <div class="form-group">
        <div class="col-xs-12 padding-bottom-sm">
            <textarea
                :value="value"
                class="form-control"
                rows="2"
                @input="update($event.target.value)"></textarea>
        </div>
        <div class="col-xs-12 text-right">
            <button class="btn btn-danger btn-sm deleteDefinition" type="button" @click="remove(index)">
                <i class="fa fa-btn fa-trash"></i> Delete
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['value', 'index'],
        methods: {
            update(value) {
                this.$emit('input', value);
            },
            remove(index) {
                this.$emit('delete', index);
            }
        }
    }
</script>
