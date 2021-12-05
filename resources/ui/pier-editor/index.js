import './bootstrap';

import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';

import Chakra from '@chakra-ui/vue';

import router from './router';
import store from './store';
import App from './App.vue';

Vue.use(VueToast);
Vue.use(Chakra);

const app = new Vue({
  el: '#app',
  store,
  router,
  render: (h) => h(App)
}).$mount();