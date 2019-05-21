import store from '~/store'
import i18n from '~/i18n'

// permission management
export default (to, from, next) => {
  if (!to.matched.length) {
    return next({ name: 'NotFound', query: { from: to.fullPath } })
  }

  const resourceLevel = (to.meta || { accessLevel: 99}).accessLevel || 99
  // require space id for resource access
  const sid = to.query.sid

  // must be a valid sid to continue
  if (resourceLevel === 7 && !store.state.spaces[sid]) {
    store.commit('noty', i18n.t('messages.resource_required_a_project_selection'))
    return next({ name: 'Project Select', query: { from: to.fullPath } })
  }

  // member, system, partner, space, resource
  if (resourceLevel !== 99) {

    const me = store.state.auth.me

    // auth is required, force login if not found
    if (typeof me === 'undefined' || !me) {
      return next({ name: 'Login', query: { from: to.fullPath } })
    }

    // check if admin or user level is ok for this route
    if (resourceLevel < 99 || me.is_admin) {
      return next()
    }

    // below check is only for non-admin
    // next step is to check if user has any permission
    if (!me.perms || Object.keys(me.perms).length <= 0) {
      return next({ name: 'Unauthorized', query: { from: to.fullPath }  })
    }

    // require space id for resource access
    const sid = to.query.sid

    // deny for not having a space id
    if (resourceLevel < 11 && !sid) {
      return next({ name: 'Unauthorized', query: { from: to.fullPath }  })
    }

    // must have access to space to continue from this point
    if (typeof (me.perms[sid]) === undefined) {
      return next({ name: 'Unauthorized', query: { from: to.fullPath }  })
    }

    // resource name/id is required for resource access check
    const resource = to.meta.resource
    if (resourceLevel < 11 && !resource) {
      return next({ name: 'Unauthorized', query: { from: to.fullPath }  })
    }

    // if module access level is a resource
    if (resourceLevel < 11) {
      // check if match wildcard resource or has permission
      // for the specific resource id
      if (me.perms[sid]['*'] || me.perms[sid][resource])
      {
        return next()
      }
    }

    // everything else is unauthorized
    return next({ name: 'Unauthorized', query: { from: to.fullPath }  })
  }

  return next()
}
