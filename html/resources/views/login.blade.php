@include('layouts.header', ['title' => 'Login - Admin Panel'])

<div id="login" class="page">

    <div id="content" class="small">

        <h1>Login</h1>

		@if(Session::has('login-error'))
			<div class="note error">Username and password didn't match. Please try again.</div>
		@endif

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/login">
			{!! csrf_field() !!}
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ old('username') }}">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            </div>
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Login" />
            </p>
        </form>
    </div>

</div>

@include('layouts.footer')
