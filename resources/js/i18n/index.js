import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

const messages = {
  'en-us': require('./en-us.json')
  // , "zh-cn": require('./zh-cn.json')
}

const dateTimeFormats = {
  'en-us': {
    short: {
      month: 'short', day: 'numeric',
    },
    long: {
      month: 'short', day: 'numeric',
      hour: 'numeric', minute: 'numeric'
    }
  }
}

export default new VueI18n({
  locale: 'en-us',
  messages,
  dateTimeFormats,
  silentTranslationWarn: true,
  missing(lang, key) {
    if (!key) {
      return
    }

    return key.replace(/[_]+/gi, ' ').replace(/^\w+\./, '').ucfirst()
  }
})
