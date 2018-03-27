@include('layouts.header', ['title' => 'Remove Dashboard Account - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>Remove Dashboard Account</h1>

    <div>

        <form role="form" method="POST" action="{{ env('APP_HOME_URL') }}/dashboard-account/remove">
            {!! csrf_field() !!}
            <input type="hidden" name="ID" value="{{ $account->ID }}" />

            <p>
                Warning: This action cannot be undone. <br />
                Are you sure want to remove the account for {{ $account->name }} ({{ $account->email }}) ?
            </p>

            <p>
                <input type="submit" class="btn btn-red" value="Remove" />
                <a href="{{ env('APP_HOME_URL') }}/dashboard-account" class="btn">Cancel</a>
            </p>

        </form>
    </div>

</div>

@include('layouts.footer');
