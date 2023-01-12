<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - Weatherlink Suryacipta</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="https://suryacipta.com/wp-content/uploads/2021/09/va.png" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

        <style>
            .text-header {
                font-family: "Merriweather", sans-serif;
                color: #fff;
                margin-top: 20px;
                margin-left: 3vw;
                margin-right: 3vw;

            }
        </style>
    </head>
    <body>
        <!-- Responsive navbar-->

        <!-- Header - set the background image for the header in the line below-->

            <div class="row">
                <div class="col-4" style="background-color: #0e394b;">
                    <h3 class="text-header">
                        <strong>{{ "Rain Rate $last_rain_rate(mm)"  }}</strong>
                    </h3>
                </div>
                <div class="col-4" style="background-color: #0e394b;">

                </div>
                <div class="col-4" style="background-color: #0e394b;">
                    <h3 class="text-header text-end" >
                        <strong><a href="{{ route('weather-history.report') }}" style="text-decoration: none;">Report Download</a></strong>
                    </h3>
                </div>

            </div>
            <div class="row">
                <div class="col-7">
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
                            height: 100vh;
                        ">
                    </iframe>
                </div>
                <div class="col-5" style="margin: auto;">
                    <canvas  id="myChart" height="100%"></canvas>
                </div>
            </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" ></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


        <script type="text/javascript">

        const labels = {{ Js::from($labels) }};
        const data = {
        labels: labels,
            datasets: [
                {
                    label: 'Rain Rate (mm)',
                    data: {{ Js::from($rain_rate_hi_mm_datas) }},
                    borderColor: "#ff5252",
                    backgroundColor: "#ff5252",
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                    minBarLength: 7,
                },
                {
                    label: 'Rain Fall (mm)',
                    data: {{ Js::from($rain_rate_datas) }},
                    borderColor: "#34ace0",
                    backgroundColor: "#34ace0",
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                    minBarLength: 7,
                }
            ]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                devicePixelRatio: 2,
                y: {
                    title: {
                        display: true,
                        text: 'Value'
                    },
                    min: 0,
                    max: 100,
                    ticks: {
                        // forces step size to be 50 units
                        stepSize: 50
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Grafik Curah Hujan'
                    }
                }
            }
        };

        new Chart(
            document.getElementById('myChart'),
            config
        );
        </script>
    </body>
</html>
