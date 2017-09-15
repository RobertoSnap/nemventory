
require('./bootstrap');
require('particles.js');
window.particlesJS.load('particles', 'js/particles.json');

import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue);


Vue.component('login', require('./components/Login.vue'))

new Vue({
    el: '#public',
});