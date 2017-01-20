
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

// TODO: Understand how I can setup an event bus https://github.com/vuejs/vue/issues/2873
// const bus = new Vue();

Vue.component('live-preview', require('./components/LivePreview.vue'));
Vue.component('ace-editor', require('./components/AceEditor.vue'));
Vue.component('rss-generator', require('./components/RssGenerator.vue'));

Vue.prototype.trans = (key, params={}) => {
    return _.reduce(params, function(result, value, key) {
        return _.replace(result, key, value);
    }, _.get(window.Laravel.translations, key, key));
};

const app = new Vue({
    el: 'body'
});
