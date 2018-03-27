@include('layouts.header', ['title' => 'Reset Password - Admin Panel'])

<div id="resetFormPassword" class="page">

    <div id="content" class="small">

        <h1>Reset Password</h1>

        @if(count($errors) > 0)
            <div class="note error">{{ $errors->all()[0] }}</div>
        @endif
        
        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/password/reset">
            <input type="hidden" name="token" value="{{ $token }}">
			{!! csrf_field() !!}
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="New Password">
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password">
            </div>
            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Submit" />
            </p>
        </form>
    </div>

</div>

@include('layouts.footer')
