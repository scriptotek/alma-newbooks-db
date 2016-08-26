<template>
    <div>

        <textarea name="{{ id }}" id="{{ id }}" class="form-control" v-model="value"></textarea>
        <div id="{{ id }}-editor" class="editor"></div>

    </div>
</template>
<script>
    export default{
        props: ['id', 'value', 'mode', 'fields'],

        ready(){
            var bus = this.$parent;  // TODO: Temporary solution until I figure out how to setup and require an eventbus Vue object

//            ace.require("brace/ext/language_tools");

            console.log("Got value: " + this.value);
            var editor = window.ace.edit(this.id + '-editor');
            var textarea = $('#' + this.id).hide();
            // editor.setTheme('ace/theme/iplastic');
            editor.getSession().setMode('ace/mode/' + this.mode);
            editor.getSession().setValue(this.value);

            var timer = 0;
            editor.getSession().on('change', () => {
                textarea.val(editor.getSession().getValue());
                bus.$emit('changed', this);
            });

            editor.setShowPrintMargin(false);
            editor.setHighlightActiveLine(false);
            editor.renderer.setOption('showGutter', false);

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
</script>
