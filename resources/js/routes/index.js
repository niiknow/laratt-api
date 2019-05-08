import DefaultContainer from '~/components/MainLayout'
import Dashboard from '~/views/Dashboard'
import ErrorPage from '~/views/Error'
import Contacts from '~/views/Contacts'
import Logout from '~/views/auth/Logout'

const $routes = [
  {
    path: '/',
    redirect: '/home/welcome',
    component: DefaultContainer,
    children: [
      {
        path: 'home',
        redirect: '/home/welcome',
        component: {
          render (c) {
            return c('router-view')
          }
        },
        children: [
          {
            path: 'welcome',
            name: 'Welcome',
            component: Dashboard
          },
          {
            path: 'contacts',
            name: 'Contacts',
            component: Contacts
          }
        ]
      }
    ]
  },
  /* Begin Auth Routes */
  {
    path: '/logout',
    name: 'Logout',
    access: 11,
    component: Logout,
    props: (route) => ({
      from: route.query.from
    })
  },
  /* Begin Error Handing Routes */
  {
    path: '/403',
    component: ErrorPage,
    name: 'Unauthorized',
    access: 99,
    meta: {
      code: 403
    }
  },
  {
    path: '/404',
    component: ErrorPage,
    name: 'NotFound',
    access: 99,
    meta: {
      code: 404
    }
  },
  {
    path: '/500',
    component: ErrorPage,
    name: 'Error',
    access: 99,
    meta: {
      code: 500
    }
  },
  /* Default Route */
  {
    path: '*',
    component: ErrorPage,
    name: 'Default',
    redirect: '/404',
    access: 99
  }
]

export default $routes

