import { Store } from 'vuex'
/* Add Below All Your Modules */
import axios from 'axios'
import store from '~/store'

const state = {
  loading: false
}

export default new Store({
  modules: {},
  state: state,
  mutations: {
    noty(state, payload) {
      let msg = payload
      let type = 'info'
      if (!isString(payload)) {
        type = payload.type
        msg = payload.message
      }

      Vue.prototype.$app.noty[type](msg)
    },
    setLoading(state, payload) {
      state.loading = payload
    }
  }
})

