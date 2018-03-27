@include('layouts.header', ['title' => 'Edit User - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Edit User</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/user/edit">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $user->ID }}" />

            @if(count($errors) > 0)
    			<div class="note error">{{ $errors->all()[0] }}</div>
    		@endif

            @if(Session::has('user-updated'))
                <div class="note success">User data has been updated.</div>
            @endif

            <div class="form-group">
                <label for="fullname">Fullname</label>
                <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Fullname" value="{{ $user->fullname }}">
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ $user->username }}">
            </div>

            <div class="form-group">
                <label for="username">Type</label>
                {!! Form::select('type', $types, $user->type, ['class' => 'form-control', 'placeholder' => 'Select type']) !!}
            </div>

            <div class="form-group">
                <label for="password">Password (Reset)</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off">
            </div>

            <p class="text-center">
                <input type="submit" class="btn btn-green" value="Save" />
                <a href="{{ env('APP_HOME_URL') }}/user" class="btn">Back</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
