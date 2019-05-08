//! Order is Important if Other Services Dependes On it */
//! Primary Services Add Here
import '~/services/bus'
import '~/services/config'
import '~/services/polyfills'
import '~/services/vuex'
import '~/services/vue-axios'
//! Secondary Services That Depends On the Primary Services
//! i.e: Vuex
import '~/services/bsvue'
import '~/services/loading'
import '~/mixins'
