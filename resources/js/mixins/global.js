import filters from './filters'
import { createNamespacedHelpers } from 'vuex'
import tinycolor from 'tinycolor2'
import $ from 'jquery'

export default {
  data: () => ({
    theme: {},
    importFile: null
  }),
  methods: {
    ...filters
  },
  created() {
    const that = this
    if (!that.theme) {
      that.theme = {}
    }

    const theme = that.theme
    const style = document.documentElement.style
    theme.primary = style.getPropertyValue('--primary')
    theme.secondary = style.getPropertyValue('--secondary')
    theme.primaryType = tinycolor(theme.primary).isDark() ? 'dark' : 'light'
    theme.secondaryType = tinycolor(theme.secondary).isDark() ? 'dark' : 'light'
  }
}
