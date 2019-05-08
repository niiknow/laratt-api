import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import store from '~/store'

// set Vue.prototype.$http = axios
Vue.use(VueAxios, axios)

window.axios = axios

/* Allows Us To Authorized Api Request If Authenticated Using Web Middleware */
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
/* Set The Token if Present So We Can Authorize Request */
let token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
  console.error(
    'CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token'
  )
}

window.axios.interceptors.request.use(config => {
  delete config.headers['Authorization']

  // hardcode API Key here for demo
  config.headers['X-API-Key'] = 'demo123'
  config.headers['X-Tenant'] = 'test'
  store.commit('setLoading', true)
  return config
})

window.axios.interceptors.response.use(
  response => {
    store.commit('setLoading', false)
    return response
  },
  error => {
    store.commit('setLoading', false)

    Vue.prototype.$app.error(error)

    return Promise.reject(error)
  }
)

export default axios
