<template>
    <div>
        <form class="form-inline">
            <div class="form-group">
                <label>{{ trans('reports.snippet') }}:</label>
                <v-select :value.sync="snippetTemplate" :options="snippetTemplates" options-value="id" options-label="name" name="snippetTemplate" v-on:change="update()"></v-select>
            </div>
            <div class="form-group">
                <label>{{ trans('reports.template') }}:</label>
                <v-select :value.sync="template" :options="templates" options-value="id" options-label="name" name="template" v-on:change="update()"></v-select>
            </div>
            <div class="form-group" v-if="showLimit" v-show="snippetTemplate == 1">
                <label>{{ trans('reports.max_elements') }}: </label>
                <input type="number" v-model="limit" style="width:100px;" class="form-control" v-on:input="update()">
            </div>
            <div class="form-group" v-if="showReceived">
                <label>
                    <input type="checkbox" v-model="received" v-on:change="update()">
                    {{ trans('reports.received') }}
                </label>
            </div>
        </form>


        <pre v-show="snippetTemplate == 1"><code>${include:feed url=[{{ url }}.rss?template={{ template }}{{ received ? '' : '&received=false' }}] display-feed-title=[false] item-description=[true] item-picture=[true] published-date=[none]{{ showLimit ? ' max-messages=[' + limit + ']' : '' }} allow-markup=[true] all-messages-link=[false] if-empty-message=[Ingen bøker]}</code></pre>
        <div v-show="snippetTemplate == 2" class="well">
            <a href="{{ url }}.rss?template={{ template }}{{ received ? '' : '&received=false' }}">{{ url }}.rss?template={{ template }}{{ received ? '' : '&received=false' }}</a>
        </div>

        <div class="form-group">
            <label>{{ trans('reports.group_by') }}:</label>
            <v-select :value.sync="groupBy" :options="groupByOptions" options-value="id" options-label="name" name="groupBy" v-on:change="update()"></v-select>
        </div>

        <p v-if="error" class="text-danger">{{ error }}</p>

        <div v-for="(groupId, docs) in documents">
            <h3 v-if="groupId">
                <a v-if="groups[groupId] && groups[groupId].link" href="{{ groups[groupId].link }}">{{ groups[groupId].title }}</a>
                <span v-else>
                    <span v-if="groups[groupId]">{{ groups[groupId].title }}</span>
                    <span v-else>{{ groupId }}</span>
                </span>
            </h3>
            <ul>
                <li v-for="doc in docs" style="margin-bottom:.8em;">
                    <a href="{{ doc.link }}">{{ doc.title }}</a>
                    <div v-html="doc.description"></div>
                </li>
            </ul>
        </div>

    </div>
</template>
<script>
    import { select } from 'vue-strap'

    export default{
        props: {
            urlbase: {
                type: String
            },
            templates: {
                type: Array,
            },
            template: {
                type: Number,
                default: 1,
            },
            snippetTemplate: {
                type: Number,
                default: 1,
            },
            limit: {
                type: Number,
                default: 50,
            },
            groupBy: {
                type: Number,
                default: 'none',
            },
            showLimit: {
                type: Boolean,
                default: true,
            },
            showReceived: {
                type: Boolean,
                default: true,
            },
        },
        data() { 
            return {
                error: null,
                received: true,
                groupByOptions: [
                    {id: 'none', name: this.trans('reports.no_grouping') },
                    {id: 'month', name: this.trans('reports.month') },
                    {id: 'week', name: this.trans('reports.week') },
                    {id: 'dewey', name: this.trans('reports.dewey') },
                ],
                snippetTemplates: [
                    {id: 1, name: this.trans('reports.vortex_snippet')},
                    {id: 2, name: this.trans('reports.link_only')},
                ],
                documents: [],
                groups: [],
            }
        },
        computed: {
            url: function () {
                return this.urlbase
            },
        },
        methods: {
            update() {

                let url = this.urlbase +'?group_by=' + this.groupBy + '&template=' + this.template + '&limit=' + this.limit;
                window.history.replaceState(null, null, url);

                let params = {
                    format: 'json',
                    template: this.template,
                    limit: this.limit,
                    group_by: this.groupBy,
                    received: this.received,
                };
                this.$set('error', null);
                this.$http.get(this.urlbase, {params: params})
                .then(
                    (response) => {
                        let j = response.json();
                        this.$set('documents', j.documents);
                        this.$set('groups', j.groups);
                    },
                    (response) => {
                        // ERROR
                        this.$set('error', 'Uh oh, something went wrong!');
                        this.$set('documents', []);
                        this.$set('groups', []);
                        console.log('Response failed!');
                    }
                );
            }
        },
        ready() {
            function getQueryStringValue (key) {
                return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
            }
            if (getQueryStringValue('group_by')) {
                this.groupBy = getQueryStringValue('group_by');
            }
            if (getQueryStringValue('limit')) {
                this.limit = getQueryStringValue('limit');
            }
            if (getQueryStringValue('template')) {
                this.template = getQueryStringValue('template') - 0;
            }
            this.update();
        },
        components: {
            vSelect: select,
        },
    }
</script>
