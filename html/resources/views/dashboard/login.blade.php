@include('layouts.header', ['title' => 'Login - Admin Panel'])

<div id="login" class="page">

    <div id="content" class="small">

        <h1>Login</h1>

		@if(Session::has('error'))
			<div class="note error">Email didn't match. Please try again.</div>
		@endif
        
        @if(Session::has('Expired'))
			<div class="note error">Link is expired. Please try again.</div>
		@endif

        @if(Session::has('finish'))
        
            <p>
                Authentication link has been sent to your email. Please click the link to access the dashboard. Thank you.
            </p>
        
        @else

            <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dashboard/login">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
                </div>
                <p class="text-center">
                    <input type="submit" class="btn btn-green" value="Login" />
                </p>
            </form>
        
        @endif
        
    </div>

</div>

@include('layouts.footer')
