@include('layouts.header', ['title' => 'Forgot Password - Admin Panel'])

<div id="reset" class="page">

    <div id="content" class="small">

        <h1>Forgot Password</h1>

		@if(count($errors) > 0)
            <div class="note error">{{ $errors->all()[0] }}</div>
        @endif
        
        @if(Session::has('forgot-notes'))
            <div class="note success">Reset password link has been sent to your email address. Please check your inbox to reset your password.</div>
        @endif

        @if(Session::has('expired'))
            <div class="note error">Your reset password link has been expired. Please try again.</div>
        @endif
        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/password/forgot">
			{!! csrf_field() !!}
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
            </div>
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Submit" />
            </p>
            <a href="{{ env('APP_HOME_URL') }}">Login</a>

        </form>
    </div>

</div>

@include('layouts.footer')
