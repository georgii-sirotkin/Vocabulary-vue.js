<template>
    <div>
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

            <div class="form-group" :class="{ 'has-error' : errors.has('image') || errors.has('remoteImageUrl') }">
                <label class="col-sm-3 control-label">Image</label>
                <div class="col-sm-9 col-md-8">
                    <ul id="imageTabs" class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#imageFile" role="tab" id="imageFile-tab" data-toggle="tab">Upload</a>
                        </li>
                        <li role="presentation">
                            <a href="#remoteImageUrl" id="remoteImageUrl-tab" role="tab" data-toggle="tab">URL</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane fade active in" id="imageFile">
                            <div class="imageFileInput">
                                <picture-input
                                    width="400"
                                    height="250"
                                    accept="image/jpeg,image/png,image/gif"
                                    size="10"
                                    buttonClass="btn btn-close"
                                    removable
                                    removeButtonClass="remove-image-btn"
                                    hideChangeButton
                                    :zIndex="999"
                                    :customStrings="{
                                        drag: 'Drop an image or click here to select a file',
                                        remove: 'x'
                                    }"
                                    :prefill="word.image_url"
                                    @change="onPictureInputChange"
                                    @remove="onPictureRemove">
                                </picture-input>
                            </div>
                            <span v-if="errors.has('image')" class="help-block" v-text="errors.get('image')"></span>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="remoteImageUrl">
                            <div class="">
                                <img v-if="word.remoteImageUrl" :src="word.remoteImageUrl" class="image-url-preview"/>
                            </div>
                            <input v-model="word.remoteImageUrl"
                                   type="text"
                                   class="form-control"
                                   placeholder="Image URL"
                                   @input="errors.clear('remoteImageUrl')">

                            <span v-if="errors.has('remoteImageUrl')" class="help-block" v-text="errors.get('remoteImageUrl')"></span>
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
        <word-form-modal
            :display="displayModal"
            :words-count="wordsCount"
            :words-url="wordsUrl">
        </word-form-modal>
    </div>
</template>

<style>
    .imageFileInput {
        position: relative;
    }

    .imageFileInput .picture-inner-text {
        font-size: 1.3em !important;
    }

    button.remove-image-btn {
        font-size: 25px;
        font-weight: 700;
        line-height: 1;
        color: #444;
        text-shadow: 0 1px 0 #fff;
        filter: alpha(opacity=60);
        opacity: .6;
        background-color: rgba(255, 255, 255, 0.6);
        border-radius: 15%;
        position: absolute;
        top: 0;
        right: 0;
        z-index: 1000;
        margin: 0.8em !important;
        padding: 4px 10px;
    }

    .image-url-preview {
        max-width: 100%;
        max-height: 215px;
        width: auto;
        height: auto;
        margin-bottom: 15px;
    }
</style>

<script>
    import WordFormDefinition from './WordFormDefinition.vue';
    import WordFormModal from './WordFormModal.vue';
    import Errors from '../Errors';
    import PictureInput from 'vue-picture-input';

    export default {
        components: {
            'word-form-definition': WordFormDefinition,
            'word-form-modal': WordFormModal,
            PictureInput
        },

        props: ['words-url', 'initialWord'],

        data: function () {
            return {
                word: this.isEditing() ? this.initialWord : this.getEmptyWord(),
                errors: new Errors(),
                displayModal: false,
                wordsCount: null
            };
        },

        computed: {
            definitionsCount() {
                return this.word.definitions.length;
            }
        },

        methods: {
            isEditing() {
                return typeof this.initialWord == 'object';
            },

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
                if (this.isEditing()) {
                    this.update();
                } else {
                    this.create();
                }
            },

            update() {
                axios.put(this.word.url, this.getSanitizedWord())
                    .then(() => {
                        window.location.href = this.word.url;
                    })
                    .catch(error => this.handleErrorResponse(error));
            },

            create() {
                axios.post(this.wordsUrl, this.getSanitizedWord())
                    .then((response) => {
                        this.wordsCount = response.data.words_count;
                        this.resetForm();
                        this.displayModal = true;
                    })
                    .catch(error => this.handleErrorResponse(error));
            },

            onPictureInputChange(image) {
                if (image) {
                    this.word.image = image
                } else {
                    alert("FileReader API not supported. You're probably using old browser.");
                }
            },

            onPictureRemove() {
                this.word.image = null;
                this.word.image_filename = null;
            },

            resetForm() {
                this.errors.clear();
                this.word = this.getEmptyWord();
                $(".remove-image-btn").click();
            },

            getEmptyWord() {
                return {
                    title: '',
                    image: '',
                    remoteImageUrl: '',
                    definitions: [
                        { text: '' },
                    ]
                };
            },

            handleErrorResponse(error) {
                if (error.response.status == 422) {
                    this.errors.record(error.response.data.errors);
                } else {
                    alert('Something went wrong!');
                }
            }
        },
    }
</script>
