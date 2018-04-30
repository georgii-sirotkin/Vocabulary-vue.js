<template>
    <form class="form-horizontal">
        <div class="form-group" :class="{'has-error' : errors.has('title')}">
            <label class="col-sm-3 control-label">Word</label>
            <div class="col-sm-9 col-md-8">
                <input v-model="word.title"
                   type="text"
                   class="form-control"
                   @input="errors.clear('title')">

                <span v-if="errors.has('title')" class="help-block" v-text="errors.get('title')"></span>
            </div>
        </div>

        <div class="form-group" :class="{ 'has-error' : errors.has('image') || errors.has('imageUrl') }">
            <label class="col-sm-3 control-label">Image</label>
            <div class="col-sm-9 col-md-8">
                <ul id="imageTabs" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#imageUrl" id="imageUrl-tab" role="tab" data-toggle="tab">URL</a>
                    </li>
                    <li role="presentation">
                        <a href="#imageFile" role="tab" id="imageFile-tab" data-toggle="tab">Upload</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="imageUrl">
                        <input v-model="word.imageUrl"
                               type="text"
                               class="form-control"
                               @input="errors.clear('imageUrl')">

                        <span v-if="errors.has('imageUrl')" class="help-block" v-text="errors.get('imageUrl')"></span>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="imageFile">
                        <!--{!! Form::file('image', ['style' => 'height: 34px']) !!}-->
                    </div>
                </div>
            </div>
        </div>

        <div v-if="definitionsCount > 0" class="row">
            <label class="col-sm-3 control-label">Defintions</label>

            <div class="col-sm-9 col-md-8">
                <word-form-definition
                    v-for="(definition, index) in word.definitions"
                    :key="index"
                    :index="index"
                    v-model="definition.text"
                    @delete="removeDefinition">
                </word-form-definition>

                <span v-if="errors.has('definitions')" class="help-block" v-text="errors.get('definitions')"></span>
            </div>
        </div>

        <div class="space-for-stick-to-bottom">
        </div>

        <div class="stick-to-bottom button-panel">
            <div class="row">
                <div class="col-xs-7 col-sm-6 col-sm-offset-3 col-md-5">
                    <button @click="addDefinition"
                        type="button"
                        class="btn btn-default btn-block">
                        <i class="fa fa-btn fa-plus"></i> Add Definition
                    </button>
                </div>
                <div class="col-xs-5 col-sm-3 col-md-3">
                    <button type="submit" class="btn btn-primary btn-block" @click.prevent="save">
                        <i class="glyphicon glyphicon-save"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
    import WordFormDefinition from './WordFormDefinition.vue';
    import Errors from '../Errors';

    export default {
        components: {
            'word-form-definition': WordFormDefinition
        },

        props: ['url'],

        data: function () {
            return {
                word: {
                    title: '',
                    imageUrl: '',
                    definitions: [
                        { text: '' },
                    ]
                },
                errors: new Errors()
            };
        },

        computed: {
            definitionsCount() {
                return this.word.definitions.length;
            }
        },

        methods: {
            removeDefinition(index) {
                this.word.definitions.splice(index, 1);
            },

            addDefinition() {
                this.word.definitions.push({text: ''});
            },

            getSanitizedWord() {
                let sanitizedWord = Object.assign({}, this.word);
                sanitizedWord.definitions = this.word.definitions.filter(definition => definition.text.length > 0);
                return sanitizedWord;
            },

            save() {
                axios.post(this.url, this.getSanitizedWord())
                    .then(() => {
                        alert('hooray!');
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            this.errors.record(error.response.data.errors);
                        } else {
                            alert('Something went wrong!');
                        }
                    });
            }
        }
    }
</script>
