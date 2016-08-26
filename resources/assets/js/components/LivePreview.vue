<template>
    <div>

        <p v-if="status == 'blank'" class="bg-warning">No query entered</p>
        <p v-if="status == 'ok'" class="bg-success">Query is valid</p>
        <p v-if="status == 'pending'" class="bg-warning">Checking query...</p>
        <p v-if="status == 'error'" class="bg-danger">Query failed: {{error}}</p>

        <div class="panel panel-default">
            <div class="panel-heading">Preview</div>

            <div class="panel-body">
                <div v-for="doc in docs">
                    <div><a href="/docs/{{ doc.mms_id }}">{{ doc.receiving_or_activation_date.split(' ')[0] }} - {{ doc.title }}</a> <small><em>{{ doc.location_name }} {{ doc.permanent_call_number }}</em></small></div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    var $ = require ('jquery');
    export default {
        props: ['editor', 'start', 'end'],
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
                    querystring: $('#' + this.editor).val(),
                    start: $('#' + this.start).val(),
                    end: $('#' + this.end).val(),
                };
                console.log(params);
                if (params.querystring.trim() == '') {
                    this.$set('status', 'blank');
                    return;
                }
                this.$set('status', 'pending');
                this.$http.get('/reports/preview', {params: params})
                        .then((response) => {
                    // console.log(response.json().docs);
                    this.$set('status', 'ok');
                this.$set('docs', response.json().docs);
            }, (response) => {
                    console.log('Failed');
                    this.$set('status', 'error');
                    this.$set('error', response.json().error);
                });
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
