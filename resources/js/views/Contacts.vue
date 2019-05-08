<template>
  <b-row>
    <b-col cols="12">
      <dtable-net
        ref="table"
        r-name="democontact"
        r-path="example"
        import-text="Import"
        :fields="fields"
        :toolbar="toolbar"
      />
    </b-col>
  </b-row>
</template>

<script>
import DtableNet from '~/components/DataTableNet'

/**
 * This is the Generic List
 */
export default {
  components: { DtableNet },
  data() {
    return {
      toolbar: {
        exportall: {
          label: 'Export All',
          action: 'exportall'
        },
        exportpage: {
          label: 'Export Page',
          action: 'exportpage'
        }
      },
      fields: {
        email: {
          sortable: true, searchable: true, label: 'Email'
        },
        first_name: {
          sortable: true, searchable: true, label: 'First name'
        },
        last_name: {
          sortable: true, searchable: true, label: 'Last name'
        },
        photo_url: {
          sortable: true, searchable: true, label: 'Photo url'
        },
        address1: {
          sortable: true, searchable: true, label: 'Address1'
        },
        address2: {
          sortable: true, searchable: true, label: 'Address2'
        },
        city: {
          sortable: true, searchable: true, label: 'City'
        },
        state: {
          sortable: true, searchable: true, label: 'State'
        },
        postal: {
          sortable: true, searchable: true, label: 'Postal'
        },
        country: {
          sortable: true, searchable: true, label: 'Country'
        },
        phone: {
          sortable: true, searchable: true, label: 'Phone'
        },
        occupation: {
          sortable: true, searchable: true, label: 'Occupation'
        },
        employer: {
          sortable: true, searchable: true, label: 'Employer'
        },
        note: {
          sortable: true, searchable: true, label: 'Note'
        },
        lat: {
          sortable: true, searchable: true, label: 'Latitude'
        },
        lng: {
          sortable: true, searchable: true, label: 'Longitude'
        }
      }
    }
  }
}
</script>
