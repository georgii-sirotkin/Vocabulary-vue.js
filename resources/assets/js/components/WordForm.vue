<template>
    <form class="form-horizontal">
        <div class="form-group">
            <label class="col-sm-3 control-label">Word</label>
            <div class="col-sm-9 col-md-8">
                <input
                   type="text"
                   v-model="word.title"
                   class="form-control">
            </div>
        </div>

        <div class="row"><!-- @if ($definitions->isEmpty()) style="display: none" -->
            <label class="col-sm-3 control-label">Defintions</label>

            <div class="col-sm-9 col-md-8">
                <word-form-definition
                    v-for="(definition, index) in word.definitions"
                    :key="index"
                    :index="index"
                    v-model="definition.text"
                    @delete="removeDefinition">
                </word-form-definition>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-7 col-sm-6 col-sm-offset-3 col-md-5">
                <button
                    type="button"
                    class="btn btn-default btn-block"
                    @click="addDefinition">
                    <i class="fa fa-btn fa-plus"></i> Add Definition
                </button>
            </div>
        </div>
    </form>
</template>

<script>
    import WordFormDefinition from './WordFormDefinition.vue';

    export default {
        components: {
            'word-form-definition': WordFormDefinition
        },

        data: function () {
            return {
                word: {
                    title: 'Test',
                    definitions: [
                        {
                            text: 'Test definition'
                        },
                        {
                            text: 'Another test definition'
                        }
                    ]
                }
            };
        },

        methods: {
            removeDefinition(index) {
                this.word.definitions.splice(index, 1);
            },

            addDefinition() {
                this.word.definitions.push({text: ''});
            }
        }
    }
</script>
