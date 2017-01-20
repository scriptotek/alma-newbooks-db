<template>
    <div>

        <p v-if="status == 'blank'" class="alert alert-warning">No query entered</p>
        <p v-if="status == 'ok'" class="alert alert-success">The query is valid</p>
        <p v-if="status == 'pending'" class="alert alert-warning">Checking query...</p>
        <p v-if="status == 'error'" class="alert alert-danger">The query is invalid: {{error}}</p>

        <div class="panel panel-default" v-if="docs.length">
            <div class="panel-heading">Preview</div>

            <div class="panel-body">
                <div v-for="doc in docs">
                    <div><a href="/documents/{{ doc.mms_id }}">{{ doc.receiving_or_activation_date.split(' ')[0] }} - {{ doc.title }}</a> <small>{{ doc.repr }}</small></div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: [
            'editor',
            'endpoint'
        ],
        data: function() {
            return {
                error: '',
                status: 'ok',
                docs: [],
            }
        },
        methods: {
            preview() {
                var params = {
                    name: 'random-title' + Math.random(),
                    max_items: 30,
                    template_id: 1,
                };
                params[this.editor] = document.getElementById(this.editor).value;
                if (params[this.editor].trim() == '') {
                    this.$set('status', 'blank');
                    return;
                }
                this.$set('status', 'pending');
                this.$http.get('/' + this.endpoint + '/preview', {params: params})
                .then(
                    (response) => {
                        // console.log(response.json().docs);
                        this.$set('status', 'ok');
                        this.$set('docs', response.json().docs);
                    },
                    (response) => {
                        console.log('Failed');
                        this.$set('status', 'error');
                        this.$set('error', 'Unknown error');
                        var j = response.json();
                        var errormsg = j.error;
                        if (!errormsg) {
                            errormsg = JSON.stringify(j);
                        }
                        this.$set('status', 'error');
                        this.$set('error', errormsg);
                    }
                );
            }
        },
        ready() {
            var bus = this.$parent;  // TODO: Temporary solution until I figure out how to setup and require an eventbus Vue object

            console.log('Component ready.');

            var timer = setTimeout(() => { this.preview() }, 1000);
            this.$set('docs', []);

            bus.$on('changed', (el) => {
                // console.log('it changed: ');
                clearTimeout(timer);
                timer = setTimeout(() => { this.preview() }, 1000)
            })
        }
    }
</script>
