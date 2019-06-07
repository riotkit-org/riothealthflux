import Vue from 'vue';
import Router from 'vue-router';

import HealthOverview from './views/HealthOverview.vue';
import Errors from './views/Errors.vue';

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    linkActiveClass: 'active',
    linkExactActiveClass: 'exact-active',
    scrollBehavior() {
        return {x: 0, y: 0};
    },
    routes: [
        {
            path: '/',
            name: 'overview',
            component: HealthOverview,
        },
        {
            path: '/errors',
            name: 'errors',
            component: Errors,
        },
        {
            path: '*',
            redirect: '/errors',
        },
    ],
});
