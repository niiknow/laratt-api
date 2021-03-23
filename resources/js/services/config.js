import Vue from 'vue'
import swal from 'sweetalert2'
import i18n from '~/i18n'
import keyBy from 'lodash/keyBy'
import isString from 'lodash/isString'
import isNumber from 'lodash/isNumber'
import isBoolean from 'lodash/isBoolean'
import isArray from 'lodash/isArray'
import isDate from 'lodash/isDate'
import clone from 'lodash/clone'
import filter from 'lodash/filter'
import map from 'lodash/map'
import find from 'lodash/find'
import get from 'lodash/get'
import mapValues from 'lodash/mapValues'
import * as pdfjs from 'pdfjs-dist'
import template from 'lodash/template'

pdfjs.disableWorker = true

window.$bus        = (window.$bus = new Vue())
window.appSettings = window.appSettings || {}
window.Vue         = Vue
const $app = window.appSettings

$app.primaryKey = window.appSettings.primaryKey || 'id'
$app.keyBy      = keyBy
$app.isString   = isString
$app.isNumber   = isNumber
$app.isBoolean  = isBoolean
$app.isArray    = isArray
$app.isDate     = isDate
$app.clone      = clone
$app.filter     = filter
$app.map        = map
$app.get        = get
$app.find       = find
$app.mapValues  = mapValues
$app.templating = template

Vue.prototype.$app      = $app
Vue.prototype.$bus      = $bus
Vue.prototype.$app.swal = swal
Vue.prototype.$pdfjs    = pdfjs

$app.error = (error) => {
  if (error instanceof String) {
    // noty('error', error)
    return
  }

  if (error.response) {
    // Not allowed error
    if (error.response.status === 403) {
      // noty('error', i18n.t('exceptions.unauthorized'))
      return
    }

    // Domain error
    if (error.response.data.errors || error.response.data.error) {
      // noty('error', error.response.data.message || JSON.stringify(error.response.data.error))
      return
    }
  }
}

$app.apiRoute = (modelName, type = '', id = null) => {
  const path = id ? `${id}/${type}` : type
  const api  = $app.api_endpoint

  modelName = modelName == 'project' ? 'space' : modelName

  // replace multiple forward slash into one
  let url = `${api}/${modelName}/${path}`
  url = url.replace(/\/+/gi, '\/').replace(/\/+$/gi, '')
  return url
}

