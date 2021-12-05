import Router from 'vue-router';
import routes from './routes';

Vue.use(Router);

export function createRouter(){
  const router = new Router({
    mode: 'hash',
    linkActiveClass: 'open active',
    scrollBehavior: () => ({ y: 0 }),
    routes
  });

  return router;
}

const router = createRouter();

export default router;