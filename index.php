<!DOCTYPE html>
<html>

<head>

    <title>Markdown Editor</title>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/marked@0.3.6"></script>
    <script src="https://unpkg.com/lodash@4.16.0"></script>
    <link rel="stylesheet" href="styleindex.css" media="screen" type="text/css" />

    <style>
        * {
            margin: 0;
        }

        .fin {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: black;
            color: white;
            width: 100vw;
            height: 100vh;
        }
    </style>
    <!--<script async src='/cdn-cgi/bm/cv/669835187/api.js'></script>-->
</head>

<body>
    <div id="editor">

        <div v-html="compiled"></div>

    </div>

    <script>
       document.addEventListener("keydown", function(e) {
        if (e.keyCode == 13) {
            toggleFullScreen();
        }
        }, false);
        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                document.exitFullscreen();
                }
            }
        }

         var token = '<?php echo $_GET['token']; ?>';
        //console.log(token);
        new Vue({
            el: "#editor",
            data: {
                content: [],
                current: [''],
                compiled: null,
                Next: 0,
                Boucle: false,
                img_src:"",
                
            },
            methods: {

                update: _.debounce(function (e) {
                    this.input = e.target.value;
                }, 300),// http://149.91.80.75:8055/admin/

                loadApi() {
             
                    fetch(`http://149.91.80.75:8055/items/Sequence?filter[Dispositif][_eq]=${token}`).then((response) => {
                        return response.json();
                    }).then((data) => {
                       console.log(data)
                        data.data.map((value, key) => {
                            this.Boucle = value.Boucle
                            fetch(`http://149.91.80.75:8055/items/Sequence_Ecrans?filter[Sequence_id][_eq]=${value.id}&fields=Ecrans_id.FontColor,Ecrans_id.BackgroundColor,Ecrans_id.Markdown,Ecrans_id.Image,Ecrans_id.Video,Ordre,Duree`).then((response) => {
                                return response.json();
                            }).then((data) => {
                                this.content = data.data;
                                console.log(this.content)
                                this.firstSlide()

                            });
                        });
                    });

                },
                nextSlide() {
                    for (var i = 0; i < (this.content.length); i++) {
                        if (this.content[i].Ordre === this.Next) {
                           // console.log(this.Next)
                            this.current = this.content[i];
                            this.Next = this.Next + 1
                            console.log(this.current.Ecrans_id.BackgroundColor)
                            this.compiled=`<div style='background-color:${this.current.Ecrans_id.BackgroundColor};color:${this.current.Ecrans_id.FontColor}'>`;
                           // console.log(this.current.Ecrans_id.Image)
                           // console.log(this.current.Ecrans_id.Video)
                            if (this.current.Ecrans_id.Image != null){
                                  this.compiled +=`<img src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Image}' />`
                            }
                            if (this.current.Ecrans_id.Markdown != null){
                                this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
                            }
                            if (this.current.Ecrans_id.Video != null){
                                  this.compiled +=`
                                  <video controls width="500" autoplay='true'>

                                    <source src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Video}>

                                    Sorry, your browser doesn't support embedded videos.
                                </video>`

                            }

                            if (this.Next > this.content.length) {
                                if (this.Boucle == true) {
                                    console.log('Boucle True')
                                    setTimeout(() => {
                                        this.firstSlide()
                                    }, this.current.Duree * 1000);

                                } else {
                                    setTimeout(() => {
                                        this.end();
                                    }, this.current.Duree * 1000);
                                }
                            } else {
                                setTimeout(() => {
                                    this.nextSlide()
                                }, this.current.Duree * 1000);
                            }

                            break;
                        }

                    }
                },
                end() {
                    this.compiled = "</div><div class='fin'>Fin De Sequence</div>";
                },
                firstSlide() {
                    this.content.map((value, key) => {
                        if (value.Ordre === 1) {
                            this.current = value
                            this.Next = 2
                            this.compiled=""
                            if (this.current.Ecrans_id.Image != null){
                                  this.compiled +=`<img src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Image}' />`
                            }
                            if (this.current.Ecrans_id.Markdown != null){
                                this.compiled += marked.parse(this.current.Ecrans_id.Markdown);
                            }
                            if (this.current.Ecrans_id.Video != null){
                                  this.compiled +=`
                                  <video controls width="500" autoplay='true'>

                                    <source src='http://149.91.80.75:8055/assets/${this.current.Ecrans_id.Video}>

                                    Sorry, your browser doesn't support embedded videos.
                                </video>`

                            }
                           // this.compiled = marked.parse(this.current.Ecrans_id.Markdown);
                            setTimeout(() => {
                                this.nextSlide()
                            }, this.current.Duree * 1000);

                        }
                    });
                },

            },

            created() {

                this.loadApi();

            },
        });
    </script>
    <script
        type="text/javascript">(function () { window['__CF$cv$params'] = { r: '6da392413f0e3a23', m: 'LEFZ0iMA7ac5xzZqZY_uXYMryYo_UMVTiJ2KPPcJ4Yc-1644309816-0-AYwvxTe8peeu7jvIFlRG+5STeVES0eAqLgupWrCGNnADPO3uj6bMSQ4El+0R/15y655R7vBGKynNJrJm8tHLVAwy1TNLNI4ltNmPwXE154Ky6uwgieXu2SBhVLsyl2Pb3k2AuKyJGOWiOiEJRVyYWgsiJSp/HX0ar1j+NsNHNgUnZJVhpWaeIA8zvFNyMWAz6A==', s: [0x9f37803655, 0xfbde0d158c], } })();</script>
</body>

</html>