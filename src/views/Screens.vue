<template>
  <div class="screens">
    <div v-html="compiled"></div>
  </div>
</template>

<script>
import { marked } from "marked";
export default {
  data() {
    return {
      content: [],
      current: [""],
      compiled: null,
      Next: 0,
      Boucle: false,
      img_src: "",
    };
  },
  methods: {
    logout() {
      this.$router.push("/TokenSelect");
    },
    login() {
      this.axios.patch(
        `http://149.91.80.75:8055/items/Dispositif/${this.$store.state.token}`,
        { status: "connecte" }
      );
    },

    loadApi() {
      fetch(
        `http://149.91.80.75:8055/items/Sequence?filter[Dispositif][_eq]=${this.$store.state.token}`
      )
        .then((response) => {
          return response.json();
        })
        .then((data) => {
          data.data.map((value, key) => {
            this.Boucle = value.Boucle;
            fetch(
              `http://149.91.80.75:8055/items/Sequence_Ecrans?filter[Sequence_id][_eq]=${value.id}&fields=Ecrans_id.FontColor,Ecrans_id.BackgroundColor,Ecrans_id.Markdown,Ecrans_id.Image,Ecrans_id.Video,Ordre,Duree`
            )
              .then((response) => {
                return response.json();
              })
              .then((data) => {
                this.content = data.data;
                this.firstSlide();
              });
          });
        });
    },
    nextSlide() {
      for (var i = 0; i < this.content.length; i++) {
        if (this.content[i].Ordre === this.Next) {
          // console.log(this.Next)
          this.current = this.content[i];
          this.Next = this.Next + 1;

          this.compiled = `<div class="main" style='width:100vw;height:100vh;background-color:${this.current.Ecrans_id.BackgroundColor};color:${this.current.Ecrans_id.FontColor}'>`;
          // console.log(this.current.Ecrans_id.Image)
          // console.log(this.current.Ecrans_id.Video)
          if (this.current.Ecrans_id.Image != null) {
            this.compiled += `<img id='imgFull' src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Image}' />`;
          } else if (this.current.Ecrans_id.Markdown != null) {
            this.compiled += "<section class='texte'>";
            this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
            this.compiled += "</section>";
          } else if (this.current.Ecrans_id.Video != null) {
            this.compiled += `
                                  <video controls width="500" autoplay='true'>

                                    <source src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Video}>

                                    Sorry, your browser doesn't support embedded videos.
                                </video>`;
          }
          this.compiled += "</div>";

          if (this.Next > this.content.length) {
            if (this.Boucle == true) {
              setTimeout(() => {
                this.firstSlide();
              }, this.current.Duree * 1000);
            } else {
              setTimeout(() => {
                this.end();
              }, this.current.Duree * 1000);
            }
          } else {
            setTimeout(() => {
              this.nextSlide();
            }, this.current.Duree * 1000);
          }

          break;
        }
      }
    },
    end() {
      this.compiled = "<div class='fin'>Fin De Sequence</div>";
    },
    firstSlide() {
      this.content.map((value, key) => {
        if (value.Ordre === 1) {
          this.current = value;
          this.Next = 2;
          this.compiled = `<div class="main" style='width:100vw;height:100vh;background-color:${this.current.Ecrans_id.BackgroundColor};color:${this.current.Ecrans_id.FontColor}'>`;
          if (this.current.Ecrans_id.Image != null) {
            this.compiled += `<div id='imgFull' style='background-position: center;width: 100vw; height: 100vh;background-size: cover;background-image: url(http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Image})' /></div>`;
            // this.compiled = `<img src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Image}' />`;
          } else if (this.current.Ecrans_id.Markdown != null) {
            this.compiled += "<section class='texte'>";
            this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
            this.compiled += "</section>";
          } else if (this.current.Ecrans_id.Video != null) {
            this.compiled += `
                                  <video controls width="500" autoplay='true'>

                                    <source src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Video}>

                                    Sorry, your browser doesn't support embedded videos.
                                </video>`;
          }
          this.compiled += "</div>";
          // this.compiled = marked.parse(this.current.Ecrans_id.Markdown);
          setTimeout(() => {
            this.nextSlide();
          }, this.current.Duree * 1000);
        }
      });
    },
  },
  created() {
    this.loadApi();
    this.login();
  },
};
</script>

<style lang="scss">
.main {
  box-sizing: border-box;
  font-family: "Segoe print", Arial, Helvetica, sans-serif;
  .texte {
    padding: 1em;
    h1,
    h2,
    h3,
    h4 {
      text-align: center;
    }
  }
}
ul {
  margin: 0;
}
#imgFull {
  width: 100vw;
  height: 100vh;
  background-size: cover;
  background-position: center;
}
</style>
