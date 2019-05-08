import Vue from 'vue'
import mixins from './global'
import filters from './filters'

Vue.mixin(mixins)

Object.entries(filters).forEach(([name, filter]) => Vue.filter(name, filter))
