<template>
  <div class="table-container">
    <b-row v-if="importText">
      <b-col
        xl="6"
      >
        <b-card>
          <h3
            slot="header"
            class="card-title"
          >
            Import {{ routeResource }}(s)
          </h3>
          <form @submit.prevent="importData">
            <b-form-file
              v-model="importFile"
              :placeholder="$t('labels.no_file_chosen')"
              required
            />
            <b-button
              type="submit"
              variant="warning"
              class="mt-3"
            >
              {{ importText }}
            </b-button>

            <b-link
              style="float: right; padding-top: 10px"
              title="Comma separated example file"
              @click.prevent="exportOne()"
            >
              Example File
            </b-link>
          </form>
        </b-card>
      </b-col>
    </b-row>
    <b-row>
      <b-col
        md="6"
        class="mb-3"
      >
        <div
          class="d-flex mx-1"
        >
          <slot name="TOOLBAR_actions">
            <b-button-toolbar aria-label="Actions">
              <b-button-group class="mx-1">
                <b-btn
                  @click.prevent="toolbarAction(tb)"
                  type="button"
                  v-for="tb in toolbar"
                  :class="tb.class"
                  :key="tb.action"
                >
                  <i :class="tb.icon" /> {{ tb.label }}
                </b-btn>
              </b-button-group>
            </b-button-toolbar>
          </slot>
        </div>
      </b-col>
      <b-col
        md="6"
        class="mb-3"
      >
        <b-form
          v-if="!hideSearch"
          inline
          class="d-flex mx-1 justify-content-end"
          @submit.stop.prevent="fetchTableData"
        >
          <b-input-group>
            <b-form-input
              v-model="quickSearch"
              :placeholder="$t('labels.quick_search')"
              type="search"
            />
            <b-input-group-append>
              <b-btn
                type="submit"
                variant="outline-secondary"
              >
                <i class="mdi mdi-magnify" /> {{ $t('actions.go') }}
              </b-btn>
            </b-input-group-append>
          </b-input-group>
        </b-form>
      </b-col>
    </b-row>
    <vdtnet-table
      ref="table"
      :fields="fields"
      :opts="options"
      :data-loader="tableLoader"
      v-on="$listeners"
    />
  </div>
</template>

<script>
import 'datatables.net-bs4'
import 'datatables.net-bs4/css/dataTables.bootstrap4.min.css'

import VdtnetTable from 'vue-datatables-net'

export default {
  components: { VdtnetTable },
  mixins: [],
  props: {
    fields: {
      type: Object,
      required: true
    },
    toolbar: {
      type: Object,
      required: true
    },
    importText: {
      type: String,
      required: true
    },
    hideSearch: {
      type: Boolean,
      default: false
    },
    rName: {
      type: String,
      required: true
    },
    rPath: {
      type: String,
      default: 'data'
    },
    tableLoader: {
      type: Function,
      default: null
    }
  },
  data() {
    const that      = this
    const modelName = that.routeResource == 'project' ? 'space' : that.routeResource

    return {
      options: {
        ajax: {
          url: that.$app.apiRoute(that.rName, that.rPath),
          headers: {
            'X-Tenant': 'test',
            'X-Api-Key': 'demo123'
          }
        },
        processing: true,
        pageLength: 15,
        searching: true,
        searchDelay: 1500,
        destroy: true,
        ordering: true,
        lengthChange: true,
        serverSide: true,
        fixedHeader: true
      },
      quickSearch: null,
      importFile: null
    }
  },
  created () {
    const that = this
  },
  mounted() {
    const that = this
    // do nothing
    that.getServerParams = that.$refs.table.getServerParams
  },
  methods: {
    reload() {
      this.$refs.table.reload()
    },
    draw() {
      this.$refs.table.dataTable.draw()
    },
    async fetchTableData (p) {
      this.$refs.table.search(this.quickSearch)
    },
    toolbarAction(tb) {
      if (tb.action === 'exportall') {
        return this.exportAll()
      } else if (tb.action === 'exportpage') {
        return this.exportPage()
      }
    },
    exportOne() {
      const that   = this
      const url    = that.$app.apiRoute(that.rName, that.rPath)
      const gsp    = that.getServerParams
      const parms  = gsp()
      parms.export = 'csv'
      parms.length = 1

      parms['x-tenant']  = 'test'
      parms['x-api-key'] = 'demo123'

      window.open(url + '?' + $.param(parms))
    },
    exportPage () {
      const that   = this
      const url    = that.$app.apiRoute(that.rName, that.rPath)
      const gsp    = that.getServerParams
      const parms  = gsp()
      parms.export = 'csv'

      parms['x-tenant']  = 'test'
      parms['x-api-key'] = 'demo123'

      console.log(JSON.stringify(parms))
      window.open(url + '?' + $.param(parms))
    },
    exportAll () {
      const that   = this
      const url    = that.$app.apiRoute(that.rName, that.rPath)
      const gsp    = that.getServerParams
      const parms  = gsp()
      parms.export = 'csv'
      parms.start  = 0
      delete parms['length']

      parms['x-tenant']  = 'test'
      parms['x-api-key'] = 'demo123'

      window.open(url + '?' + $.param(parms))
    },
    async importData () {
      const that     = this
      const formData = new FormData()
      formData.append('file', that.importFile)

      try {
        const url      = that.$app.apiRoute(that.rName, 'import')
        const { data } = await that.$http.post(url, formData)
        if (that.$refs.table) {
          that.$refs.table.reload()
        }
        that.reload()
      } catch (e) {
        that.$app.error(e)
      }
    }
  }
}
</script>
