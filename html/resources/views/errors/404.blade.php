<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link rel="shortcut icon" href="{{ env('APP_HOME_URL') }}/static/img/favicon.ico" />

		<title>404 - Page not Found</title>
		<meta name="description" content="Havas Worldwide Jakarta" />
		<meta name="robots" content="nofollow" />
		<meta name="copyright" content="Havas Worldwide Jakarta 2015" />

        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Roboto">
        <link rel="stylesheet" type="text/css" href="{{ env('APP_HOME_URL') }}/static/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="{{ env('APP_HOME_URL') }}/static/css/bootstrap.min.css">
	    <link rel="stylesheet" type="text/css" href="{{ env('APP_HOME_URL') }}/static/css/admin-master.css" />

	</head>
	<body>
        
        <div id="notFound">
            <h1>:(</h1>
            <h2>Oops, page not found.</h2>
            <p><a href="{{ env('APP_HOME_URL') }}" class="btn btn-green">Home</a></p>
        </div>

        <div class="clear"></div>

        <footer class="text-center">
            2016 &copy; Havas Worldwide Jakarta
        </footer>

    </body>
</html>
