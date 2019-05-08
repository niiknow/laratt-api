<template>
  <div class="app-root d-flex flex-column">
    <!-- Nav -->
    <header class="w-100">
      <nav>
        <b-navbar
          :type="theme.primaryType"
          variant="primary"
        >
          <b-link
            aria-label="Toggle navigation"
            class="sidebar-toggler"
            @click="toggleSidebar()"
          >
            <i class="navbar-toggler-icon" />
          </b-link>
          <b-navbar-brand>
            <span>
              Demo Tenant
            </span>
          </b-navbar-brand>
          <!-- Right aligned nav items -->
          <b-navbar-nav class="ml-auto">
            <b-nav-item-dropdown right>
              <!-- Using button-content slot -->
              <template slot="button-content">
                <i class="mdi mdi-account" /> Demo User
              </template>
              <b-dropdown-item :to="{ path: '/home/account/profile' }">
                <i class="mdi mdi-account-box" /> My Profile
              </b-dropdown-item>
              <b-dropdown-item :to="{ name: 'Logout', query: { from: $route.fullPath } }">
                <i class="mdi mdi-power" /> Logout
              </b-dropdown-item>
            </b-nav-item-dropdown>
          </b-navbar-nav>
        </b-navbar>
      </nav>
    </header>
    <div class="app-main w-100 d-flex d-flex-row">
      <nav
        class="nav navbar-dark bg-secondary flex-column sidebar"
      >
        <sidebar :nav-items="nav" />
      </nav>
      <main class="w-100">
        <b-loading
          :active="$store.state.loading"
          spinner="bar-fade-scale"
        />
        <breadcrumb :list="list" />
        <router-view class="animated fadeIn main-body" />
      </main>
    </div>
  </div>
</template>
<script>
import nav from '~/_nav'
import Sidebar from '~/components/Sidebar'
import Breadcrumb from '~/components/Breadcrumb'
import { mapState, mapGetters } from 'vuex'

export default {
  name: 'DefaultLayout',
  components: { Breadcrumb, Sidebar },
  data () {
    return {
      nav: nav.items
    }
  },
  computed: {
    name () {
      return this.$route.name
    },
    list () {
      return this.$route.matched.filter((route) => route.path !== '' && route.meta.label !== 'Index')
    }
  },
  methods: {
    toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('collapse')
    }
  }
}
</script>

