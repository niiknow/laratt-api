<template>
  <section class="h-100">
    <div class="container h-100 animated fadeIn">
      <b-card>
        <b-card-body>
          <b-form>
            <div>
              <h1>Logout</h1>
              <p class="text-muted">
                Are you sure you want to logout?
              </p>
              <b-img
                src="https://www.gravatar.com/avatar/0b44473e97ab5db4392762e78cb9933b.jpg?s=200&d=mm"
                thumbnail
                fluid
              />
              <p>Demo User</p>
            </div>
            <b-row>
              <b-col md="6">
                <b-button
                  variant="default"
                  block
                  @click="goBack"
                >
                  Cancel
                </b-button>
              </b-col>
              <b-col md="6">
                <b-button
                  variant="warning"
                  block
                  @click="doLogout"
                >
                  Yes
                </b-button>
              </b-col>
            </b-row>
          </b-form>
        </b-card-body>
      </b-card>
    </div>
  </section>
</template>

<script>
export default {
  name: 'Logout',
  props: ['from'],
  methods: {
    goBack() {
      const that = this
      that.$router.push({
        path: that.from || '/'
      })
    },
    doLogout() {
      const that = this
      that.$router.push({ name: 'Home' })
    }
  }
}
</script>
