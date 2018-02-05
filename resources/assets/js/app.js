/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('live-preview', require('./components/LivePreview.vue'));
Vue.component('ace-editor', require('./components/AceEditor.vue'));
Vue.component('rss-generator', require('./components/RssGenerator.vue'));

Vue.prototype.trans = (key, params={}) => {
    return _.reduce(params, function(result, value, key) {
        return _.replace(result, key, value);
    }, _.get(window.Laravel.translations, key, key));
};

const app = new Vue({
    el: '#content'
});
