<template>
  <div class="tokenselect">
    <h1>Inter'Display</h1>
    <div id="container">
      <form @submit.prevent="submit">
        <label class="label" for="login">Token :</label>
        <input
          type="text"
          placeholder="Token"
          name="token"
          id="token"
          v-model="token"
        /><br />
        <input
          type="submit"
          class="pure-button pure-button-primary"
          value="Confirmer"
        />
      </form>
    </div>
  </div>
</template>


<script>
export default {
  name: "TokenSelect",
  data() {
    return {
      token: "",
    };
  },
  methods: {
    notLog() {
      this.axios
        .patch(
          `https://api.interdisp.valentinbardet.dev/items/Dispositif/${this.$store.state.token}`,
          { status: "deconnecte" }
        )
        .then((response) => {
          console.log("deconnecte");
        });
    },
    submit() {
      this.$store.commit("setToken", this.token);
      this.$router.push("/");
      document.documentElement.requestFullscreen();
    },
  },
  mounted() {
    this.token = this.$store.state.token;
    this.notLog;
  },
};
</script>

<style lang="scss" scoped>
.tokenselect {
  background: url("https://www.adventistemagazine.com/wp-content/uploads/2016/01/Silver-Blur-Background-Wallpaper.jpg")
    no-repeat center;
  width: 100%;
  height: 100%;
  margin: 0 auto;
  background-size: cover;
  background-attachment: fixed;
  display: flex;
  flex-direction: column;
  align-items: center;
}
#container {
  min-width: 50%;
  margin-top: 10%;
  overflow: hidden;
}
.label {
  color: #537baf;
  font-weight: 800;
}
h1 {
  font-size: 48px;
  text-shadow: -1px -1px #537baf, 1px 1px grey, -3px 0 4px #000;
  font-family: "Segoe print", Arial, Helvetica, sans-serif;
  color: white;
  font-weight: lighter;
  -moz-box-shadow: none;
  -webkit-box-shadow: none;
  box-shadow: none;
  text-align: center;
  display: block;
  padding-top: 100px;
  margin: 0;
}
/* Bordered form */
form {
  width: 100%;
  padding: 30px;
  border: 1px solid #f1f1f1;
  background: #fff;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
  box-sizing: border-box;
  overflow: hidden;
}
#container h1 {
  width: 38%;
  padding-bottom: 10px;
  font-family: "Segoe print", Arial, Helvetica, sans-serif;
}
label {
  font-family: "Segoe print", Arial, Helvetica, sans-serif;
}

input[type="text"] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

/* Set a style for all buttons */
input[type="submit"] {
  background-color: #537baf;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
  box-sizing: border-box;
}
input[type="submit"]:hover {
  background-color: white;
  color: #537baf;
  border: 1px solid #537baf;
}
</style>
