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
                    <div style="font-weight:bold;">{{ doc.receiving_or_activation_date.split('T')[0] }} : {{ doc.title }}</div>
                    <small v-html="doc.repr"></small>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    require('axios');

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
                    this.status = 'blank';
                    return;
                }
                this.status = 'pending';

                axios.get('/' + this.endpoint + '/preview', {params: params})
                .then(
                    (response) => {
                        // console.log(response.json().docs);
                        this.status = 'ok';
                        this.docs = response.data.docs;
                    },
                    (response) => {
                        console.log('Failed');
                        this.status = 'error';
                        this.error = 'Unknown error';
                        var j = response.data;
                        var errormsg = j.error;
                        if (!errormsg) {
                            errormsg = JSON.stringify(j);
                        }
                        this.status = 'error';
                        this.error = errormsg;
                    }
                );
            }
        },
        mounted() {
            var bus = this.$parent;  // TODO: Temporary solution until I figure out how to setup and require an eventbus Vue object

            console.log('Component ready.');

            var timer = setTimeout(() => { this.preview() }, 1000);
            this.docs = [];

            bus.$on('changed', (el) => {
                // console.log('it changed: ');
                clearTimeout(timer);
                timer = setTimeout(() => { this.preview() }, 1000)
            })
        }
    }
</script>
