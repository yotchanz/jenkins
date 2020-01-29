// ルーティングの定義をインポートする
import router from './router'
// ルートコンポーネントをインポートする
import App from './App.vue'

import Vue from 'vue'
import Datetimepicker from 'vuetify-datetime-picker'

require('./bootstrap');

import Vuetify from 'vuetify'
import ja from 'vuetify/es5/locale/ja.js'
import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify);
Vue.use(Datetimepicker);

new Vuetify({
  lang: {
    locales: {ja},
    current: 'ja'
  }
})

new Vue({
  el: '#app',
  router, // ルーティングの定義を読み込む
  components: { App }, // ルートコンポーネントの使用を宣言する
  template: '<App />', // ルートコンポーネントを描画する
  vuetify: new Vuetify(),
});