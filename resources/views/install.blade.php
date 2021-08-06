<!doctype html>
<html lang="en">

<head>
    <title>Installation</title>
	<link rel="shortcut icon" href="/public/img/favicon/favicon.ico" />
    <link rel="apple-touch-icon" href="/public/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/public/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/public/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/public/img/favicon/site.webmanifest">
    <!-- CSS files -->
    <link href="{{asset("css/jqvmap/jqvmap.min.css")}}" rel="stylesheet"/>
    <link href="{{asset("css/tabler.min.css")}}" rel="stylesheet"/>
    <link href="{{asset("css/tablerd.min.css")}}" rel="stylesheet"/>
</head>

<body class="antialiased">
    <div class="page">
        <div class="content">
            <div class="container-xl col-11 col-md-10 col-lg-8">
                @if (\Session::has('success'))
                <div class="alert alert-success alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {!! \Session::get('success') !!}
                </div>
                @elseif (\Session::has('error'))
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {!! \Session::get('error') !!}
                </div>
                @elseif ($errors->any())
                <div class="alert alert-warning alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    {!!$errors->first()!!}
                </div>
                @endif
                <form id="form" class="card m-0" method="post" action="{{route('install')}}">
                    @csrf
                    <div class="card-header bg-primary text-white font-weight-bold">Backend Installation</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Backend name:</label>
                                <input type="text" class="form-control" name="name" placeholder="Mintly" value="{{old('name')}}">
                            </div>
                            <div class="mb-3 col-md-6">
                                {!!$data['url']!!}
                            </div>
                        </div>
                        <div class="hr-text text-blue font-weight-bold mt-4 mb-3">Database configuration</div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Database host:</label>
                                <input type="text" class="form-control" name="db_host" value="{{old('db_host','localhost')}}">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Database port:</label>
                                <input type="text" class="form-control" name="db_port" value="{{old('db_port','3306')}}">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Database name:</label>
                                <input type="text" class="form-control" name="db_name" placeholder="my_database" value="{{old('db_name')}}">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Database user:</label>
                                <input type="text" class="form-control" name="db_user" placeholder="mysql_user" value="{{old('db_user')}}">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Database password:</label>
                                <input type="text" class="form-control" name="db_pass" placeholder="dbpassword" value="{{old('db_pass')}}">
                            </div>
                        </div>
                        <div class="hr-text text-blue font-weight-bold mt-4 mb-3">Permissions</div>
                        <div class="row">
                            {!!$data['config']!!}
                            {!!$data['upload']!!}
                            {!!$data['storage']!!}
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        {!!$data['btn']!!}
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{asset("js/jquery-1.11.2.min.js")}}"></script>
    <script src="{{asset("js/bootstrap.bundle.min.js")}}"></script>
    <script>
        $('#form').submit(function () {
            $(this).find(':submit').text('Please wait...')
            $(this).find(':submit').attr('disabled', 'disabled');
        });

    </script>
</body>

</html>
