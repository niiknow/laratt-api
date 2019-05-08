import Vue from 'vue'
import Router from 'vue-router'
// import before from '~/middleware/before'
// import after from '~/middleware/after'
import routes from '~/routes'

Vue.use(Router)

/* Our Vue Router Object */
const router = new Router({
  routes,
  /* Use Pretty URL */
  mode: 'history',
  /* Save The Scroll Position , Useful When Redirecting Back */
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { x: 0, y: 0 }
    }
  }
})

/* Middlewares */
// router.beforeEach(before)
// router.afterEach(after)

export default router
