import store from '~/store'

// guard against expired token
export default (to, from, next) => {
  const accessLevel = (to.meta || { accessLevel: 99}).accessLevel || 99

  // only check if it's not a login page
  // or accessLevel does not have a value
  if (accessLevel < 99 && to.name !== 'Login') {
    if (store.state.auth.expiredAt < new Date().getTime()) {
      store.commit('auth/setMe', null)
      return next({ name: 'Login', query: { from: to.fullPath } })
    }
  }
}
