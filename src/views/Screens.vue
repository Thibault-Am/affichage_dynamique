<template>
  <div class="screens">
    <div v-html="compiled"></div>
    <div class="close">
      <div>
        <router-link
          to="/TokenSelect"
          class="btn"
        >
          <i class="fa-solid fa-xmark"></i>
        </router-link>
        <p
          v-on:click="loadApi"
          class="btn"
        >
          <i class="fa-solid fa-arrow-rotate-right"></i>
        </p>
        <p
          v-on:click="fullscreen"
          class="btn"
        >
          <i class="fa-solid fa-expand"></i>
        </p>
      </div>

    </div>
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
      indexTab: [],
    };
  },
  mounted() {
    let recaptchaScript = document.createElement("script");
    recaptchaScript.setAttribute(
      "src",
      "https://kit.fontawesome.com/c0ee7c1d52.js"
    );
    document.head.appendChild(recaptchaScript);
  },
  methods: {
    fullscreen() {
      document.documentElement.requestFullscreen();
    },
    restart() {
      document.reload();
    },
    logout() {
      this.$router.push("/TokenSelect");
    },
    login() {
      this.axios.patch(
        `https://api.interdisp.valentinbardet.dev/items/Dispositif/${this.$store.state.token}`,
        { status: "connecte" }
      );
    },

    loadApi() {
      fetch(
        `https://api.interdisp.valentinbardet.dev/items/Sequence?filter[Dispositif][_eq]=${this.$store.state.token}`
      )
        .then((response) => {
          return response.json();
        })
        .then((data) => {
          data.data.map((value, key) => {
            this.Boucle = value.Boucle;
            fetch(
              `https://api.interdisp.valentinbardet.dev/items/Sequence_Ecrans?filter[Sequence_id][_eq]=${value.id}&fields=Ecrans_id.Nombre_de_colonnes,Ecrans_id.Nombre_de_lignes,Ecrans_id.Nombre_de_colonnes_video,Ecrans_id.Nombre_de_lignes_video,Ecrans_id.Nombre_de_colonnes_image,Ecrans_id.Nombre_de_lignes_image,Ecrans_id.Type,Ecrans_id.FontColor,Ecrans_id.BackgroundColor,Ecrans_id.Markdown,Ecrans_id.Image,Ecrans_id.Video,Ordre,Duree`
            )
              .then((response) => {
                return response.json();
              })
              .then((data) => {
                this.content = data.data;
                this.IndexContent();
                this.firstSlide();
              });
          });
        });
    },
    IndexContent() {
      for (var i = 0; i < this.content.length; i++) {
        this.indexTab[i] = this.content[i].Ordre;
      }
    },
    nextSlide(n) {
      let i = this.indexTab.indexOf(n);
      this.current = this.content[i];
      this.Next = this.Next + 1;
      this.compiled = `<div class="main" style='width:100vw;height:100vh;background-color:${this.current.Ecrans_id.BackgroundColor};color:${this.current.Ecrans_id.FontColor}'>`;
      /*-------  Cas d'un contentu Multimédia   --------*/
      if (this.current.Ecrans_id.Type == "multimedia") {
        this.compiled += `<div class='grille'>`;
        if (this.current.Ecrans_id.Image != null) {
          this.compiled += `<div id='img' style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_image}; grid-raw: span ${this.current.Ecrans_id.Nombre_de_lignes_image}' />
                <img width='100%'src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Image}'/>
              </div>`;
        }
        if (this.current.Ecrans_id.Markdown != null) {
          this.compiled += `<section class='texte' style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes}; grid-raw: span ${this.current.Ecrans_id.Nombre_de_lignes}'>`;
          this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
          this.compiled += "</section>";
        }
        if (this.current.Ecrans_id.Video != null) {
          this.compiled += `  <div style='grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_video}; grid-raw: span ${this.current.Ecrans_id.Nombre_de_lignes_video}'>
                                      <video controls width="100%" autoplay='true'>
                                        <source src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Video}>
                                        Sorry, your browser doesn't support embedded videos.
                                    </video>
                                  </div>`;
        }
      } else {
        if (this.current.Ecrans_id.Image != null) {
          this.compiled += `<img id='imgFull' src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Image}' />`;
        }
        if (this.current.Ecrans_id.Markdown != null) {
          this.compiled += "<section class='texte'>";
          this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
          this.compiled += "</section>";
        }
        if (this.current.Ecrans_id.Video != null) {
          this.compiled += `
                                    <video controls width="500" autoplay='true'>
                                      <source src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Video}>
                                      Sorry, your browser doesn't support embedded videos.
                                  </video>`;
        }
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
          this.nextSlide(this.Next);
        }, this.current.Duree * 1000);
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

          /*-------  Cas d'un contentu Multimédia   --------*/

          if (this.current.Ecrans_id.Type == "multimedia") {
            this.compiled += `<div class='grille'>`;
            if (this.current.Ecrans_id.Image != null) {
              this.compiled += `<div id='img' style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_image}; grid-raw: span ${this.current.Ecrans_id.Nombre_de_lignes_image}' />
                <img width='100%'src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Image}'/>
              </div>`;
            }
            if (this.current.Ecrans_id.Markdown != null) {
              this.compiled += `<section class='texte' style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes}; grid-raw: span ${this.current.Ecrans_id.Nombre_de_lignes}'>`;
              this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
              this.compiled += "</section>";
            }
            if (this.current.Ecrans_id.Video != null) {
              this.compiled += `  <div style='grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_video}; grid-raw: span ${this.current.Ecrans_id.Nombre_de_lignes_video}'>
                                      <video controls width="100%" autoplay='true'>

                                        <source src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Video}>

                                        Sorry, your browser doesn't support embedded videos.
                                    </video>
                                  </div>
                                  `;
            }
          }

          /*-------  Cas d'un contentu Image   --------*/

          if (this.current.Ecrans_id.Type == "image") {
            this.compiled += `<div id='imgFull' style='background-position: center;width: 100vw; height: 100vh;background-size: cover;background-image: url(https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Image})' /></div>`;
          }

          /*-------  Cas d'un contentu Texte   --------*/

          if (this.current.Ecrans_id.Type == "texte") {
            this.compiled += "<section class='texte'>";
            this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
            this.compiled += "</section>";
          }

          /*-------  Cas d'un contentu Video   --------*/

          if (this.current.Ecrans_id.Type == "video") {
            this.compiled += `<video controls width="500" autoplay='true'>
                                <source src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Video}>
                                Sorry, your browser doesn't support embedded videos.
                              </video>`;
          }
          this.compiled += "</div>";
          setTimeout(() => {
            this.nextSlide(this.Next);
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
.grille {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  grid-gap: 10px;
  grid-auto-rows: minmax(100px, auto);
}
#imgFull {
  width: 100vw;
  height: 100vh;
  background-size: cover;
  background-position: center;
}

.close {
  height: 100vh;
  width: 20vw;
  position: fixed;
  top: 0;
  right: 0;
  z-index: 40;
  &:hover div {
    min-width: 20vw;
    max-width: 20vw;
  }
  &:hover div .btn {
    opacity: 1;
  }
  div {
    // background: rgb(43, 41, 146);
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    transition: all 0.3s;
    background-color: rgba(68, 63, 63, 0.822);
    height: 100vh;
    max-width: 0vw;
    min-width: 0vw;
    position: fixed;
    top: 0;
    right: 0;
    z-index: 41;
    .btn {
      cursor: pointer;
      margin-top: 15px;
      margin-bottom: 0;
      transition: all 0.3s;
      background: black;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      padding: 10px;
      opacity: 0;
      z-index: 90;
      color: rgb(255, 251, 251);
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      i {
        font-size: 30px;
      }
    }
  }
}
</style>
