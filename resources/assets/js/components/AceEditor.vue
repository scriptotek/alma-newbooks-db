<template>
    <div style="height: 100%">
        <textarea :name="id" :id="id" class="form-control" v-model="value" style="display:none;"></textarea>
        <div :id="id + '-editor'" class="editor"></div>
    </div>
</template>
<style type="text/css" media="screen">
    .editor {
        position: relative;
        height: 100%;
        width: 100%;
    }
</style>
<script>
    export default {
        props: {
            id: {
                type: String,
            },
            value: {
                type: String,
            },
            mode: {
                type: String,
            },
            fields: {
                type: String,
            },
            readonly: {
                type: Boolean,
                default: false
            }
        },
        mounted(){
            var bus = this.$parent;  // TODO: Temporary solution until I figure out how to setup and require an eventbus Vue object

            var editor = window.ace.edit(this.id + '-editor');
            var textarea = document.getElementById(this.id);
            editor.setTheme('ace/theme/monokai');
            editor.getSession().setMode('ace/mode/' + this.mode);
            editor.getSession().setValue(this.value);

            var timer = 0;
            editor.getSession().on('change', () => {
                textarea.value = editor.getSession().getValue();
                bus.$emit('changed', this);
            });

            editor.setShowPrintMargin(false);
            editor.setHighlightActiveLine(true);
            editor.setReadOnly(this.readonly);
            editor.renderer.setOption('showGutter', true);

            if (this.fields) {
                var staticWordCompleter = {
                    getCompletions: (editor, session, pos, prefix, callback) => {
                        var wordList = this.fields.split(',');
                        callback(null, wordList.map((word) => {
                            return {
                                caption: word,
                                value: word,
                                meta: "field"
                            };
                        }));
                    }
                };

                editor.completers = [staticWordCompleter];

                editor.setOptions({
                    enableBasicAutocompletion: true,
                    /*enableSnippets: true,*/
                    enableLiveAutocompletion: true
                });
            }
        }
    }
</script>
