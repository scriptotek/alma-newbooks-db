<template>
    <div>
        <form class="form-inline">
            <div class="form-group">
                <label>{{ trans('reports.snippet') }}:</label>
                <b-form-select
                    name="snippetTemplate"
                    v-model="snippetTemplate"
                    :options="snippetTemplates"
                    v-on:change="update"
                ></b-form-select>
            </div>
            <div class="form-group">
                <label>{{ trans('reports.template') }}:</label>
                <b-form-select
                    name="template"
                    v-model="template"
                    :options="templatesOptions"
                    v-on:change="update"
                ></b-form-select>
            </div>
            <div class="form-group" v-if="showLimit" v-show="snippetTemplate == 1">
                <label>{{ trans('reports.max_elements') }}: </label>
                <input type="number" v-model="limit" style="width:100px;" class="form-control" v-on:input="update">
            </div>
            <div class="form-group" v-if="showReceived">
                <label>
                    <input type="checkbox" v-model="received" v-on:change="update">
                    {{ trans('reports.received') }}
                </label>
            </div>
        </form>


        <pre v-show="snippetTemplate == 1"><code>${include:feed url=[{{ url }}?template={{ template }}{{ received ? '' : '&received=false' }}] display-feed-title=[false] item-description=[true] item-picture=[true] published-date=[none]{{ showLimit ? ' max-messages=[' + limit + ']' : '' }} allow-markup=[true] all-messages-link=[false] if-empty-message=[Ingen bøker]}</code></pre>
        <div v-show="snippetTemplate == 2" class="well">
            <a :href="url + '?template=' + template + (received ? '' : '&received=false')">{{ url }}?template={{ template }}{{ received ? '' : '&received=false' }}</a>
        </div>

        <form class="form-inline">
            <div class="form-group">
                <label>{{ trans('reports.group_by') }}:</label>
                <b-form-select
                    name="groupBy"
                    v-model="groupBy"
                    :options="groupByOptions"
                    v-on:change="update"
                ></b-form-select>
            </div>
        </form>

        <p v-if="error" class="text-danger">{{ error }}</p>

        <div v-for="(docs, groupId) in documents">
            <h3 v-if="groupId">
                <a v-if="groups[groupId] && groups[groupId].link" :href="groups[groupId].link">{{ groups[groupId].title }}</a>
                <span v-else>
                    <span v-if="groups[groupId]">{{ groups[groupId].title }}</span>
                    <span v-else>{{ groupId }}</span>
                </span>
            </h3>
            <ul>
                <li v-for="doc in docs" style="margin-bottom:.8em;">
                    <a :href="doc.link">{{ doc.title }}</a>
                    <div v-html="doc.description"></div>
                </li>
            </ul>
        </div>

    </div>
</template>
<script>
    // import { bSelect } from 'bootstrap-vue/es/components/form-select/form-select'

    export default{
        props: {
            viewUrl: {
                type: String
            },
            rssUrl: {
                type: String
            },
            jsonUrl: {
                type: String
            },
            templates: {
                type: Array,
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
                    {value: 'none', text: this.trans('reports.no_grouping') },
                    {value: 'month', text: this.trans('reports.month') },
                    {value: 'week', text: this.trans('reports.week') },
                    {value: 'dewey', text: this.trans('reports.dewey') },
                ],
                snippetTemplates: [
                    {value: 1, text: this.trans('reports.vortex_snippet')},
                    {value: 2, text: this.trans('reports.link_only')},
                ],
                documents: [],
                groups: [],
                snippetTemplate: 1,
                template: 1,
                limit: 50,
                groupBy: 'none',
            }
        },
        computed: {
            url () {
                return this.rssUrl;
            },
            templatesOptions () {
                if (!this.templates) return [];
                return this.templates.map((t) => {
                    return {value: t.id, text: t.name};
                });
            }
        },
        methods: {
            update() {
                Vue.nextTick(() => {
                    let url = this.viewUrl +'?group_by=' + this.groupBy + '&template=' + this.template + '&limit=' + this.limit;
                    window.history.replaceState(null, null, url);

                    let params = {
                        template: this.template,
                        limit: this.limit,
                        group_by: this.groupBy,
                        received: this.received,
                    };
                    console.log(params);
                    this.error = null;
                    axios.get(this.jsonUrl, {params: params})
                    .then(
                        (response) => {
                            this.documents = response.data.documents;
                            this.groups = response.data.groups;
                        },
                        (response) => {
                            // ERROR
                            this.error = 'Uh oh, something went wrong!';
                            this.documents = [];
                            this.groups = [];
                            console.log('Response failed!');
                        }
                    );
                });
            }
        },
        mounted() {
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
            // 'b-form-select': bSelect,
        },
    }
</script>
