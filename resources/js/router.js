import Vue from 'vue';
import VueRouter from 'vue-router';

//コンポーネントをインポート
import Home from './views/Home.vue';

Vue.use(VueRouter);

const routes = [
 {
   path: '/',
   component: Home
 }
];

const router = new VueRouter({
  routes
});

export default router