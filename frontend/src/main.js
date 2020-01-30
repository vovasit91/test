// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import VueGoodTablePlugin from 'vue-good-table';
import 'vue-good-table/dist/vue-good-table.css'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import {ClientTable} from 'vue-tables-2';


Vue.config.productionTip = false;
Vue.use(VueGoodTablePlugin);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(ClientTable);


/* eslint-disable no-new */
new Vue({
  el: '#app',
  components: { App },
  template: '<App/>'
})
