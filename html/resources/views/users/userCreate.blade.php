@include('layouts.header', ['title' => 'Create User - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Create User</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/user/create">
            {!! csrf_field() !!}

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            <div class="form-group">
                <label for="fullname">Fullname</label>
                <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Fullname" value="{{ old('fullname') }}">
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ old('username') }}">
            </div>

            <div class="form-group">
                <label for="username">Type</label>
                {!! Form::select('type', $types, old('type'), ['class' => 'form-control', 'placeholder' => 'Select type']) !!}
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Create" />
                <a href="{{ env('APP_HOME_URL') }}/user" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
