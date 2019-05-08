<template>
  <div class="app flex-row align-items-center">
    <div class="container">
      <b-row class="justify-content-center">
        <b-col md="6">
          <div class="clearfix">
            <h1
              v-if="errorCode === 403"
              class="float-left display-3 mr-4"
            >
              403
            </h1>
            <h1
              v-if="errorCode === 404"
              class="float-left display-3 mr-4"
            >
              404
            </h1>
            <h1
              v-if="errorCode >= 500"
              class="float-left display-3 mr-4"
            >
              500
            </h1>
            <h4
              v-if="errorCode === 403"
              class="pt-3"
            >
              Nuh-uh-uh, you didn't say the magic word.
            </h4>
            <h4
              v-if="errorCode === 404"
              class="pt-3"
            >
              Oops! You're lost.
            </h4>
            <h4
              v-if="errorCode >= 500"
              class="pt-3"
            >
              Houston, we have a problem!
            </h4>
            <p
              v-if="errorCode === 403"
              class="text-muted"
            >
              You are not allowed to access this resource.
            </p>
            <p
              v-if="errorCode === 404"
              class="text-muted"
            >
              The page you are looking for was not found.
            </p>
            <p
              v-if="errorCode >= 500"
              class="text-muted"
            >
              The page you are looking for is temporarily unavailable.
            </p>
          </div>
        </b-col>
      </b-row>
      <b-row class="justify-content-center">
        <b-col md="6">
          <div class="clearfix">
            <b-button
              class="pull-right"
              @click="goHome"
            >
              Home
            </b-button>
            <b-button @click="redirectBack">
              Back
            </b-button>
          </div>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    errorCode() {
      return Number(this.$route.meta.code)
    }
  },
  methods: {
    redirectBack () {
      this.$router.go(-1)
    },
    goHome () {
      this.$router.push({ name: 'Home' })
    }
  }
}
</script>
