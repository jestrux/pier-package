import './bootstrap';

import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';

import store from './store';
import router from './router';
import Loader from './UI/components/Loader';

Vue.use(VueToast);
Vue.component('Loader', Loader);

const defaultConfig ={};

window.PierCMS = (el = "#pierCMS", config) => {
  const PierCMSConfig = {
    ...defaultConfig,
    ...config
  }

  new Vue({
    el: el,
    router,
    store,
    provide(){
      return {
        PierCMSConfig,
      }
    },
    render: (h) => h('router-view')
  }).$mount();
}

window.dispatchEvent(new CustomEvent('PierCMS:loaded'));