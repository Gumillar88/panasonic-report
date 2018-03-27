@include('layouts.header', ['title' => 'Create User - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove User</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/user/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $user->ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove user "{{ $user->fullname }}"?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/user" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
