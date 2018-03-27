<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link rel="shortcut icon" href="{{ env('APP_HOME_URL') }}/static/img/favicon.ico" />

		<title>{{ $title }}</title>
		<meta name="description" content="Havas Worldwide Jakarta" />
		<meta name="robots" content="nofollow" />
		<meta name="copyright" content="Havas Worldwide Jakarta 2015" />
		<meta name="home_url" content="{{ env('APP_HOME_URL') }}" />
        <meta name="csrf_token" content="{{ csrf_token() }}">
		

        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Roboto">
        <link rel="stylesheet" type="text/css" href="{{ env('APP_HOME_URL') }}/static/css/lib.css" />
        <link rel="stylesheet" type="text/css" href="{{ env('APP_HOME_URL') }}/static/css/chosen.min.css" />
	    <link rel="stylesheet" type="text/css" href="{{ env('APP_HOME_URL') }}/static/css/admin-master.css" />
	    <link rel="stylesheet" type="text/css" href="{{ env('APP_HOME_URL') }}/static/css/admin-custom.css" />

	</head>
	<body>
        <header class="text-center">
            <a href="{{ env('APP_HOME_URL') }}/admin">
                <img src="{{ env('APP_HOME_URL') }}/static/img/logo-admin-transparent.png" />
            </a>
        </header>
