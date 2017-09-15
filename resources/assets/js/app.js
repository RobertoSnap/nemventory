require('./bootstrap');

import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import App from './components/App.vue'
import router from './router/router'

Vue.use(BootstrapVue);

new Vue({
    el: '#app',
    router,
    template: '<App/>',
    components: {
        App
    }
});





