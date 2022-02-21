<template>
  <div class="screens">
    <div v-html="compiled"></div>
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
  mounted() {
    let recaptchaScript = document.createElement("script");
    recaptchaScript.setAttribute(
      "src",
      "https://kit.fontawesome.com/c0ee7c1d52.js"
    );
    document.head.appendChild(recaptchaScript);
  },
  methods: {
    meteo(inputVal, apiKey) {
      const url = `https://api.openweathermap.org/data/2.5/weather?q=${inputVal}&appid=${apiKey}&units=metric`;
      console.log("methode meteo");
      fetch(url)
        .then((response) => response.json())
        .then((data) => {
          const { main, name, sys, weather } = data;
          const icon = `https://s3-us-west-2.amazonaws.com/s.cdpn.io/162656/${weather[0]["icon"]}.svg`;

          const div = document.createElement("div");
          div.classList.add("city");

          const markup = `
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
                  <figcaption>${weather[0]["description"]}</figcaption>
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
              `https://api.interdisp.valentinbardet.dev/items/Sequence_Ecrans?filter[Sequence_id][_eq]=${value.id}&fields=Ecrans_id.Nombre_de_colonnes,Ecrans_id.Nombre_de_lignes,Ecrans_id.Nombre_de_colonnes_video,Ecrans_id.Nombre_de_lignes_video,Ecrans_id.Nombre_de_colonnes_image,Ecrans_id.Nombre_de_lignes_image,Ecrans_id.Type,Ecrans_id.FontColor,Ecrans_id.BackgroundColor,Ecrans_id.Markdown,Ecrans_id.Image,Ecrans_id.Video,Ordre,Duree`
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

          if (this.current.Ecrans_id.Type == "multimedia") {
            this.compiled += `<div class='grille'>`;
            if (this.current.Ecrans_id.Image != null) {
              this.compiled += `<div id='img' style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_image}; grid-raw: span ${this.current.Ecrans_id.Nombre_de_lignes_image}' />
                <img width='100%'src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Image}'/>
              </div>`;
              // this.compiled = `<img src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Image}' />`;
            }
            if (this.current.Ecrans_id.Markdown != null) {
              console.log("test");
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

          if (this.current.Ecrans_id.Type == "multimedia") {
            this.compiled += `<div class='grille'>`;
            if (this.current.Ecrans_id.Image != null) {
              this.compiled += `<div id='img' style=' grid-column: span ${this.current.Ecrans_id.Nombre_de_colonnes_image}; grid-raw: span ${this.current.Ecrans_id.Nombre_de_lignes_image}' />
                <img width='100%'src='https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Image}'/>
              </div>`;
              // this.compiled = `<img src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Image}' />`;
            }
            if (this.current.Ecrans_id.Markdown != null) {
              console.log("test");
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
            if (this.current.Ecrans_id.Type == "image") {
              this.compiled += `<div id='imgFull' style='background-position: center;width: 100vw; height: 100vh;background-size: cover;background-image: url(https://api.interdisp.valentinbardet.dev/assets/${this.current.Ecrans_id.Image})' /></div>`;
              // this.compiled = `<img src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Image}' />`;
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
            } else if (this.current.Ecrans_id.Type == "meteo") {
              console.log("test");
              this.compiled += `<style> :root {
    --bg_main: #0a1f44;
    --text_light: #fff;
    --text_med: #53627c;
    --text_dark: #1e2432;
    --red: #ff1e42;
    --darkred: #c3112d;
    --orange: #ff8c00;
  }

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-weight: normal;
  }


  img {
    display: block;
    max-width: 100%;
    height: auto;
  }



  body {
    font: 1rem/1.3 "Roboto", sans-serif;
    background: var(--bg_main);
    color: var(--text_dark);
  }

  .container {
    width: 100%;
    max-width: 1080px;
    margin: 0 auto;
    padding: 0 15px;
  }




  /* SECTION #2
–––––––––––––––––––––––––––––––––––––––––––––––––– */
  .ajax-section {
    padding: 70px 0 20px;
  }



  .ajax-section .city {
    position: relative;
    padding: 40px 10%;
    border-radius: 20px;
    background: var(--text_light);
    color: var(--text_med);

  }

  .ajax-section .city::after {
    content: '';
    width: 90%;
    height: 50px;
    position: absolute;
    bottom: -12px;
    left: 5%;
    z-index: -1;
    opacity: 0.3;
    border-radius: 20px;
    background: var(--text_light);
  }


  .ajax-section .city-temp {
    font-size: 5rem;
    font-weight: bold;
    margin-top: 10px;
    color: var(--text_dark);
  }

  .ajax-section .city sup {
    font-size: 0.5em;
  }

  .ajax-section .city-name sup {
    padding: 0.2em 0.6em;
    border-radius: 30px;
    color: var(--text_light);
    background: var(--orange);
  }

  .ajax-section .city-icon {
    margin-top: 10px;
    width: 100px;
    height: 100px;
  }</style>
                                <div onload="${this.meteo(
                                  "Nancy",
                                  "4d8fb5b93d4af21d66a2948710284366"
                                )}">
                                  <section class="ajax-section">
                                    <div class="container">
                                      <div class="cities"></div>
                                    </div>
                                  </section>
                                                
                                </div>`;
            }
          }
        }
        this.compiled += "</div>";
        // this.compiled = marked.parse(this.current.Ecrans_id.Markdown);
        setTimeout(() => {
          this.nextSlide();
        }, this.current.Duree * 1000);
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
