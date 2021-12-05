import Vue from 'vue';

import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';

import Form from './Form';
import Loader from './Form/components/Loader';

Vue.use(VueToast);
Vue.component('Loader', Loader);

new Vue({
  el: '#pierForm',
  provide: {
    model: window.pierModel,
    values: window.pierModelValues,
  },
  render: (h) => h(Form)
}).$mount();