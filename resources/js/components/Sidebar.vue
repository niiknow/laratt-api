<template>
  <b-navbar-nav class="w-100">
    <template v-for="(item, index) in navItems">
      <template v-if="item.title">
        <b-nav-text
          :key="index"
        >
          {{ item.name }}
        </b-nav-text>
      </template>
      <template v-else-if="item.divider">
        <b-dropdown-divider
          :key="index"
        />
      </template>
      <template v-else>
        <template v-if="item.children">
          todo: dropdown
        </template>
        <template v-else>
          <b-nav-item
            :key="index"
            :to="item.url"
            exact
            v-if="canView(item)"
          >
            <i
              class="mdi-24px"
              :class="item.icon"
              :title="item.name"
            /><span class="navbar-text">
              {{ item.name }}
            </span>
          </b-nav-item>
        </template>
      </template>
    </template>
  </b-navbar-nav>
</template>

<script>
export default {
  name: 'Sidebar',
  components: {
  },
  props: {
    navItems: {
      type: Array,
      required: true,
      default: () => []
    }
  },
  data () {
    return {
      psSettings: {
        maxScrollbarLength: 200,
        minScrollbarLength: 40,
        suppressScrollX: true,
        wheelPropagation: false,
        interceptRailY: styles => ({ ...styles, height: 0 })
      }
    }
  },
  methods: {
    canView(item) {
      return true
    },
    scrollHandle (evt) {
      // console.log(evt)
    }
  }
}
</script>

