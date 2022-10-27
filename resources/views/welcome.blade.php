<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - Weatherlink Suryacipta</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="https://suryacipta.com/content/suryacipta-apps/subang/smartpolitan/public/exhibition-oct-nov/assets/favicon.ico" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <style>
            .text-rain {
                font-family: "Merriweather", sans-serif;
                color: #fff;
                margin-top: 20px;
                margin-left: 3vw;
            }
        </style>
    </head>
    <body onload="zoom()">
        <!-- Responsive navbar-->

        <!-- Header - set the background image for the header in the line below-->

            <div class="row">
                <div class="col-12" style="background-color: #0e394b;">
                    <h3 class="text-rain">
                        <strong>{{ "Rain Rate $last_rain_rate->rain_rate_hi_mm (mm)"  }}</strong>
                    </h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <iframe
                        src="https://www.weatherlink.com/embeddablePage/show/86da60c806c44759b79af156b6648793/fullscreen"
                        style="
                           position:absolute
                            top: 0px;
                            bottom: 0px;
                            right: 0px;
                            width: 100%;
                            border: none;
                            margin: 0;
                            padding: 0;
                            overflow: hidden;
                            z-index: 999999;
                            height: 99vh;
                        ">
                    </iframe>
                </div>
            </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <script type="text/javascript">
            function zoom() {
                document.body.style.zoom = "90%"
            }
        </script>
    </body>
</html>
