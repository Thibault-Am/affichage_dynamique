import screenfull from "screenfull";


<template>
  <router-view />
</template>

<style lang="scss">
body {
  margin: 0;
  font-family: "Segoe print", Arial, Helvetica, sans-serif;
}
#app {
  width: 100vw;
  height: 100vh;
  margin: 0;
}
</style>
<script>
export default {
  data() {
    return {
      isFullscreen: false,
    };
  },
  watch: {
    $route() {
      this.verificationToken();
    },
  },
  mounted() {
    this.verificationToken();
  },
  methods: {
    verificationToken() {
      if (this.$route.name == "TokenSelect");
      if (!this.$store.state.token) {
        this.$router.push("/TokenSelect");
      }
    },
  },
};
</script>