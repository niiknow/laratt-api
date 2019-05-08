<template>
  <ol class="breadcrumb">
    <li
      v-for="(routeObject, index) in routeRecords"
      :key="index"
      class="breadcrumb-item"
    >
      <router-link
        v-if="isHome(routeObject)"
        :to="routeObject"
        title="Home"
      >
        <i class="mdi mdi-home" />
      </router-link>
      <span
        v-else-if="isLast(index)"
        class="active"
      >
        {{ getLabel(routeObject) }}
      </span>
      <router-link
        v-else
        :to="routeObject"
      >
        {{ getLabel(routeObject) }}
      </router-link>
    </li>
  </ol>
</template>

<script>
export default {
  props: {
    list: {
      type: Array,
      required: true,
      default: () => []
    }
  },
  computed: {
    routeRecords: function () {
      return this.list.filter((route) => route.path !== '' && route.meta.label !== 'Index')
    }
  },
  methods: {
    isHome (item) {
      return item.name === 'Home'
    },
    getName (item) {
      return item.name
    },
    getLabel (item) {
      return item.meta && (item.meta.label || (item.name || ''))
    },
    isLast (index) {
      return index === this.routeRecords.length - 1
    }
  },
}
</script>
