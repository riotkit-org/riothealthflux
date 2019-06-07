/* eslint-disable */
import Vue from 'vue';
import ShardsVue from 'shards-vue';

// Styles
import 'bootstrap/dist/css/bootstrap.css';
import '@/scss/shards-dashboards.scss';

// Core
import App from './App.vue';
import router from './router';

// Libraries
import VueResource from 'vue-resource'; // HTTP

// Layouts
import Default from '@/layouts/Default.vue';

ShardsVue.install(Vue);

Vue.component('default-layout', Default);
Vue.use(VueResource);

Vue.config.productionTip = false;
Vue.prototype.$eventHub = new Vue();

new Vue({
    router,
    render: h => h(App),
    http: {
        headers: {
            Authorization: function () { return window.dashboard.auth; }
        }
    }
}).$mount('#app');
