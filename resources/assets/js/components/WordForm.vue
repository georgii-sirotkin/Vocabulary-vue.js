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
                        <a href="#imageFile" role="tab" id="imageFile-tab" data-toggle="tab">Upload</a>
                    </li>
                    <li role="presentation">
                        <a href="#imageUrl" id="imageUrl-tab" role="tab" data-toggle="tab">URL</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="imageFile">
                        <picture-input
                                @change="onPictureInputChange"
                                width="400"
                                height="250"
                                accept="image/jpeg,image/png"
                                size="10"
                                buttonClass="btn"
                                removable
                                hideChangeButton
                                :customStrings="{
                                    drag: 'Drop an image or click here to select a file'
                                }">
                        </picture-input>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="imageUrl">
                        <div class="">
                            <img v-if="word.imageUrl" :src="word.imageUrl" class="img-responsive"/>
                        </div>
                        <input v-model="word.imageUrl"
                               type="text"
                               class="form-control"
                               placeholder="Image URL"
                               @input="errors.clear('imageUrl')">

                        <span v-if="errors.has('imageUrl')" class="help-block" v-text="errors.get('imageUrl')"></span>
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

<style>
    #imageFile .picture-inner-text {
        font-size: 1.3em;
    }
</style>

<script>
    import WordFormDefinition from './WordFormDefinition.vue';
    import Errors from '../Errors';
    import PictureInput from 'vue-picture-input';

    export default {
        components: {
            'word-form-definition': WordFormDefinition,
            PictureInput
        },

        props: ['url'],

        data: function () {
            return {
                word: {
                    title: '',
                    image: '',
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
            },

            onPictureInputChange (image) {
                if (image) {
                    this.word.image = image
                } else {
                    alert("FileReader API not supported. You're probably using old browser.");
                }
            }
        }
    }
</script>
