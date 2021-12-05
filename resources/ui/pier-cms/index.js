import './bootstrap';

import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';

import store from './store';
import router from './router';
import Loader from './UI/components/Loader';

Vue.use(VueToast);
Vue.component('Loader', Loader);

new Vue({
  el: '#pierCMS',
  router,
  store,
  render: (h) => h('router-view')
}).$mount();