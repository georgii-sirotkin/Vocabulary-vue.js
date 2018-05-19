<template>
    <div id="wordFormModal" class="modal fade in" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Word was added</h4>
                </div>
                <div class="modal-body">
                    <p class="success-message">
                        <span class="text-success">
                            <span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>
                        </span>
                        &nbsp;You have added a new word!
                    </p>
                    <div>
                        Now there are {{ wordsCount }} words in your vocabulary.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" @click="redirectToWordsIndex">View words</button>
                    <button type="button" class="btn btn-primary" @click="$parent.displayModal = false;">
                        Add another word
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .modal-body .success-message {
        font-size: 1.5em;
    }

    .modal-body .text-success {
        margin-left: 5px;
    }
</style>

<script>
    export default {
        props: ['display', 'words-count', 'words-url'],
        methods: {
            redirectToWordsIndex() {
                this.$parent.displayModal = false;
                window.location.href = this.wordsUrl;
            }
        },
        watch: {
            display: function (newValue) {
                if (newValue) {
                    $("#wordFormModal").modal('show');
                } else {
                    $("#wordFormModal").modal('hide');
                }
            }
        }
    }
</script>