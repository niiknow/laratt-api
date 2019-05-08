import './plugins'

import MyApp from './MyApp.vue'
import router from './router'
import store, { types } from '~/store'
import i18n from './i18n'
import { sync } from 'vuex-router-sync'
import Vue from 'vue'

export function createApp () {
  Vue.config.productionTip = false

  sync(store, router)
  const app = new Vue({
    store,
    router,
    i18n,
    created() {
      /*
      store.dispatch('auth/keepAlive')

      setInterval(() => {
        store.dispatch('auth/keepAlive')
      }, 301 * 1000)
      */
    },
    render: h => h(MyApp)
  })

  return { app, router, store, i18n }
}

// Init App
if (document.getElementById('myApp') !== null) {
  const { app } = createApp()
  app.$mount('#myApp')
  window.window.appSettings.$app = app
}
