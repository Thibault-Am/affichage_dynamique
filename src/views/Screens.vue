<template>
  <div class="screens">
    <v-runtime-template :template="compiled"></v-runtime-template>
    <div class="close">
      <div>
        <router-link to="/TokenSelect" class="btn">
          <i class="fa-solid fa-xmark"></i>
        </router-link>
        <p v-on:click="loadApi" class="btn">
          <i class="fa-solid fa-arrow-rotate-right"></i>
        </p>
        <p v-on:click="fullscreen" class="btn">
          <i class="fa-solid fa-expand"></i>
        </p>
      </div>
    </div>
  </div>
</template>
<script>
import { marked } from "marked";
import VRuntimeTemplate from "vue3-runtime-template";
const date = new Date();
export default {
  data() {
    return {
      dateTime: {
        hours: date.getHours(),
        minutes: date.getMinutes(),
        seconds: date.getSeconds(),
      },
      timer: undefined,
      content: [],
      current: [""],
      compiled: null,
      Next: 0,
      Boucle: false,
      img_src: "",
      compteur: 1,
      indexTab: [],
    };
  },
  components: {
    VRuntimeTemplate,
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
    setDateTime() {
      const date = new Date();
      this.dateTime = {
        hours: date.getHours(),
        minutes: date.getMinutes(),
        seconds: date.getSeconds(),
      };
    },

    meteo(inputVal, apiKey) {
      const url = `https://api.openweathermap.org/data/2.5/weather?q=${inputVal}&appid=${apiKey}&units=metric`;
      // console.log("methode meteo");
      fetch(url)
        .then((response) => response.json())
        .then((data) => {
          const { main, name, sys, weather } = data;
          const icon = `https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/${weather[0]["icon"]}.svg`;

          const div = document.createElement("div");
          div.classList.add("city");
          var markup;
          if (this.current.Ecrans_id.Background_color != null) {
            markup = ` <style> body {
              background-color:${this.current.Ecrans_id.Background_color};
              } </style>`;
          } else {
            markup = ``;
          }

          markup += `
           <style>

                              .container {
                                width: auto;
                                height: auto;
                              }
                              .ajax-section .city {
                                
                                border-radius: 20px;
                                background: white;
                                color: grey;
                                text-align:center;
                                
                              }

                              .ajax-section .city-temp {
                                font-size: 1rem;
                                font-weight: bold;
                                color: black;
                              }

                              .ajax-section .city sup {
                                font-size: 0.5em;
                              }

                              .ajax-section .city-name sup {
                    
                                border-radius: 30px;
                                color: grey;
                                background: orange;
                              }

                              .ajax-section .city-icon {
                                margin: auto;
                                width: 50px;
                                height: 50px;
                              }
                              </style>
                <h2 class="city-name" data-name="${name},${sys.country}">
                  <span>${name}</span>
                  <sup>${sys.country}</sup>
                </h2>
                <div class="city-temp">${Math.round(
                  main.temp
                )}<sup>°C</sup></div>
                <figure>
                  <img class="city-icon" src="${icon}" alt="${
            weather[0]["description"]
          }">
                  
                </figure>
              `;

          div.innerHTML = markup;
          document.querySelector(".container").appendChild(div);
        })
        .catch(() => {
          alert("erreur la ville n'existe pas ");
        });
    },

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
            //console.log(`Valeur id : .${value.id}`);
            fetch(
              `https://api.interdisp.valentinbardet.dev/items/Sequence_Ecrans?filter[Sequence_id][_eq]=${value.id}&fields=Ecrans_id.Choix_Image,Ecrans_id.Choix_Horloge,Ecrans_id.Horloge_position,Ecrans_id.Lignes_twitter,Ecrans_id.widget_twitter,Ecrans_id.Nombre_de_colonnes_twitter,Ecrans_id.Nombre_de_colonnes_videoYoutube,Ecrans_id.Lignes_videoYoutube,Ecrans_id.videoYoutube,Ecrans_id.Choix_videoYoutube,Ecrans_id.Choix_Meteo,Ecrans_id.Meteo_position,Ecrans_id.Ville,Ecrans_id.Background_color,Ecrans_id.Nombre_de_colonnes,Ecrans_id.Lignes_texte,Ecrans_id.Nombre_de_colonnes_video,Ecrans_id.Lignes_video,Ecrans_id.Nombre_de_colonnes_image,Ecrans_id.Lignes_image,Ecrans_id.Type,Ecrans_id.FontColor,Ecrans_id.BackgroundColor,Ecrans_id.Markdown,Ecrans_id.Image,Ecrans_id.Video,Ordre,Duree`
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
        if (this.current.Ecrans_id.Choix_Meteo == true) {
          this.compteur = 2;
          this.compiled += ` <div style=' grid-column: ${
            this.current.Ecrans_id.Meteo_position
          }; grid-row: 1' onload="${this.meteo(
            this.current.Ecrans_id.Ville,
            "4d8fb5b93d4af21d66a2948710284366"
          )}">
                                  <section class="ajax-section">
                                    <div class="container">
                                      <div class="cities"></div>
                                    </div>
                                  </section>
                                                
                                </div>`;
        }
        if (this.current.Ecrans_id.videoYoutube != null) {
          this.compiled += `
                <div style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_videoYoutube}; grid-row:${this.compteur}/${this.current.Ecrans_id.Lignes_videoYoutube}'  class="youtube">
                  <iframe
                    width="560"
                    height="315"
                    src="https://www.youtube.com/embed/${this.current.Ecrans_id.videoYoutube}?autoplay=1"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; ; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                  ></iframe>
                </div>
              `;
        }
        if (this.current.Ecrans_id.Image != null) {
          console.log("frip");
          this.compiled += `<div  style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_image}; grid-row: ${this.compteur}/${this.current.Ecrans_id.Lignes_image}'>
                <img class='imgG' src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Image}'/>
              </div>`;
          // this.compiled = `<img src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Image}' />`;
        }
        if (this.current.Ecrans_id.Markdown != null) {
          //console.log("test");
          this.compiled += `<section class='texte' style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes}; grid-row: ${this.compteur}/${this.current.Ecrans_id.Lignes_texte}'>`;
          this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
          this.compiled += "</section>";
        }
        if (this.current.Ecrans_id.Video != null) {
          this.compiled += `  <div style='grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_video}; grid-row: ${this.compteur}/${this.current.Ecrans_id.Lignes_video}'>
                                      <video controls width="100%" autoplay='true'>
                                        <source src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Video}>
                                        Sorry, your browser doesn't support embedded videos.
                                    </video>
                                  </div>
                                  `;
        }
      } else {
        if (this.current.Ecrans_id.Type == "image") {
          this.compiled += `<img id='imgFull' src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Image}' />`;
        } else if (this.current.Ecrans_id.Type == "texte") {
          this.compiled += "<section class='texte'>";
          this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
          this.compiled += "</section>";
        } else if (this.current.Ecrans_id.Type == "video") {
          this.compiled += `
                                    <video controls width="500" autoplay='true'>
                                      <source src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Video}>
                                      Sorry, your browser doesn't support embedded videos.
                                  </video>`;
        } else if (this.current.Ecrans_id.videoYoutube != null) {
          this.compiled += `
                <div class="youtube">
                  <iframe
                    width="560"
                    height="315"
                    src="https://www.youtube.com/embed/${this.current.Ecrans_id.videoYoutube}?autoplay=1"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; ; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                  ></iframe>
                </div>`;
        } else if (this.current.Ecrans_id.widget_twitter == true) {
          this.compiled += `<div class="container"> <a class="twitter-timeline" data-width="600" data-height="500" data-theme="light" href="https://twitter.com/iutCharlemagne?ref_src=twsrc%5Etfw" data-link-color="#E95F28" data-tweet-limit="1">Tweets by iutCharlemagne</a> </div>`;
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
          this.compiled += `<div class='grille'>`;
          if (this.current.Ecrans_id.Type == "multimedia") {
            if (this.current.Ecrans_id.Choix_Meteo == true) {
              this.compteur = 2;
              this.compiled += ` <div style=' grid-column: ${
                this.current.Ecrans_id.Meteo_position
              }; grid-row: 1' onload="${this.meteo(
                this.current.Ecrans_id.Ville,
                "4d8fb5b93d4af21d66a2948710284366"
              )}" >
                                  <section class="ajax-section">
                                    <div class="container">
                                      <div class="cities"></div>
                                    </div>
                                  </section>
                                 </div>               
                                `;
            }
            if (this.current.Ecrans_id.videoYoutube != null) {
              this.compiled += `
                <div style=' grid-column:span ${this.current.Ecrans_id.Nombre_de_colonnes_videoYoutube}; grid-row:${this.compteur}/${this.current.Ecrans_id.Nombre_de_lignes_videoYoutube}'  class="youtube">
                  <iframe
                    width="560"
                    height="315"
                    src="https://www.youtube.com/embed/${this.current.Ecrans_id.videoYoutube}?autoplay=1"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; ; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                  ></iframe>
                </div>
              `;
            }
            if (this.current.Ecrans_id.Image != null) {
              console.log(this.current.Ecrans_id.Image);
              this.compiled += `<div  style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_image}; grid-row:${this.compteur}/${this.current.Ecrans_id.Nombre_de_lignes_image}' >
                <img class='imgG' src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Image}' />
              </div>`;
            }
            if (this.current.Ecrans_id.Markdown != null) {
              this.compiled += `<section class='texte' style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes}; grid-row: ${this.compteur}/${this.current.Ecrans_id.Nombre_de_lignes}'>`;
              this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
              this.compiled += "</section>";
            }
            if (this.current.Ecrans_id.Video != null) {
              this.compiled += `  <div style='grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_video}; grid-row: ${this.compteur}/${this.current.Ecrans_id.Nombre_de_lignes_video}'>
                                      <video controls width="100%" autoplay='true'>
                                        <source src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Video}>
                                        Sorry, your browser doesn't support embedded videos.
                                    </video>
                                  </div>
                                  `;
            }
            if (this.current.Ecrans_id.Choix_Horloge != null) {
              this.compiled += `
              <div style='grid-column: ${this.current.Ecrans_id.Horloge_position}; grid-row: 1'><p id="horloge">{{ dateTime.hours }}:{{ dateTime.minutes }}:{{ dateTime.seconds }}</p></div></div>`;
            }
          } else {
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
            /* ------ Cas d'un contenu video youtube -------*/

            if (this.current.Ecrans_id.videoYoutube != null) {
              this.compiled += `
                <div class="youtube">
                  <iframe
                    width="560"
                    height="315"
                    src="https://www.youtube.com/embed/${this.current.Ecrans_id.videoYoutube}?autoplay=1"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; ; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                  ></iframe>
                </div>
              `;
            }
            if (this.current.Ecrans_id.widget_twitter == true) {
              let widgets = document.createElement("script");
              widgets.setAttribute("charset", "UTF-8");
              widgets.setAttribute(
                "src",
                "https://platform.twitter.com/widgets.js"
              );

              document.head.appendChild(widgets);
              this.compiled += `<div class="container"> <a class="twitter-timeline" data-width="600" data-height="500" data-theme="light" href="https://twitter.com/iutCharlemagne?ref_src=twsrc%5Etfw" data-link-color="#E95F28" data-tweet-limit="1">Tweets by iutCharlemagne</a> </div> `;
            }
          }

          this.compiled += "</div>";
          setTimeout(() => {
            this.nextSlide(this.Next);
          }, this.current.Duree * 1000);
        }
      });
    },
  },

  beforeUnmount() {
    clearInterval(this.timer);
  },
  created() {
    this.loadApi();
    this.login();
    console.log("avant");

    // var_dump(this.indexTab);
    // console.log("apres");
  },
};
</script>


<style lang="scss">
.imgG {
  width: 100%;
}
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
.youtube {
  overflow: hidden;
  padding-bottom: 56.25%;
  position: relative;
  height: 0;
  grid-column: span 3;

  iframe {
    left: 0;
    top: 0;
    height: 100%;
    width: 100%;
    position: absolute;
  }
}
ul {
  margin: 0;
}
.grille {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  grid-auto-rows: auto;
  grid-gap: 10px;
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
