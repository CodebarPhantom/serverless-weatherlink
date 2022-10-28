<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Download Data Weather History Suryacipta Swadaya</title>
        <link rel="icon" type="image/x-icon" href="https://suryacipta.com/wp-content/uploads/2021/09/va.png" />


        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            body{
                padding-top: 40px;
                font-family: 'Roboto', sans-serif;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2><strong>Download Data Weather History Suryacipta Swadaya</strong></h2>
            <table class="table table-bordered" id="report-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>File</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
        <!-- DataTables -->
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

        <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
        <!-- Bootstrap JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
        <!-- App scripts -->
        <script type="text/javascript">
            $(function() {
                $('#report-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        method: 'POST',
                        url: "{{ route('weather-history.report-data') }}",
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    },
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'path_s3', name: 'path_s3',searchable:false, orderable: false }

                    ]
                });
            });
            </script>
    </body>
</html>
